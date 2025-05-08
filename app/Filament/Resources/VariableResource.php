<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VariableResource\Pages;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use App\Models\JenisKopi;
use App\Models\Variables;
use App\Models\Parameters;
use App\Models\SubParameter;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class VariableResource extends Resource
{
    protected static ?string $model = Variables::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Data Supplier/Penggiat Kopi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $parameters = Parameters::with('subParameters')->get();
        $parameterInputs = [];

        foreach ($parameters as $parameter) {
            // Transform the parameter name
            $transformedName = $parameter->kode;
            // $transformedName = str_replace(' ', '_', preg_replace('/[^a-z\s]/', '', $transformedName));
            // $transformedName = trim($transformedName, '_');

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();
                $parameterInputs[] = Forms\Components\CheckboxList::make("parameters.{$transformedName}")
                    ->label($parameter->name)
                    ->options($subParameters);
            } else {
                $parameterInputs[] = Forms\Components\TextInput::make("parameters.{$transformedName}")
                    ->label($parameter->name)
                    ->required()
                    ->numeric() // Ensure the input is numeric
                    ->rules(['min:' . $parameter->min, 'max:' . $parameter->max]);; // Maximum value validation
            }
        }

        return $form
            ->schema(array_merge([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('nama_supplier')->required(),
                Forms\Components\TextInput::make('alamat')->required(),
                Select::make('jenis_kopi')
                    ->label('Jenis Kopi')
                    ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi')),
                FileUpload::make('image')
                    ->image()
                    ->multiple()
                    ->directory('variables')
                    ->storeFiles()
                    ->columnSpanFull(),
            ], $parameterInputs));
    }

    public static function table(Table $table): Table
    {


        $columns = [
            Tables\Columns\TextColumn::make('nama_supplier')->label('Nama Supplier'),
            Tables\Columns\TextColumn::make('name')->label('Nama Kopi'),
            Tables\Columns\ImageColumn::make('images')
                ->getStateUsing(fn($record) => $record->image[0] ?? null) // Ambil gambar pertama
                ->disk('public')
                ->size(50),


            // Tables\Columns\ViewColumn::make('image')->view('filament.tables.columns.carousel'),
            Tables\Columns\TextColumn::make('JenisKopi.jenis_kopi')->label('Jenis Kopi'),
        ];



        $parameters = Parameters::all();
        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;
            // $transformedName = strtolower($parameter->name);
            // $transformedName = str_replace(' ', '_', preg_replace('/[^a-z\s]/', '', $transformedName));
            // $transformedName = trim($transformedName, '_');

            $columns[] = Tables\Columns\TextColumn::make("parameters->{$transformedName}")
                ->label($parameter->kode)
                ->formatStateUsing(function ($state, $record) use ($transformedName) {
                    if (!$record->parameters) {
                        return '';
                    }

                    // Pastikan data parameters di-decode hanya jika berbentuk string
                    $parameters = is_array($record->parameters) ? $record->parameters : json_decode($record->parameters, true);

                    if (is_array($parameters) && isset($parameters[$transformedName])) {
                        $value = $parameters[$transformedName];
                        if (is_array($value)) {
                            $subParams = SubParameter::whereIn('subparameter_id', $value)->pluck('name')->toArray();
                            return implode(', ', $subParams);
                        }
                        return $value;
                    }
                    return '';
                });
        }

        // Tambahkan kolom status sebagai ikon
        $columns[] = Tables\Columns\IconColumn::make('status')
            ->boolean()
            ->label('Status')
            ->trueIcon('heroicon-o-check-circle')
            ->falseIcon('heroicon-o-x-circle')
            ->alignCenter();

        return $table
            ->columns($columns)

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make('viewDetails'),
                Tables\Actions\EditAction::make(),
                Action::make('normalize')
                    ->label('Normalisasi')
                    ->icon('heroicon-o-database')
                    ->action(function ($record) {
                        if (!$record->isValid()) {
                            // Use Filament's notification system to show error
                            Notification::make()
                                ->title('Error')
                                ->body('Data parameter masih belum terisi dengan benar. Silahkan melakukan penyesuaian terlebih dahulu.')
                                ->danger() // Optional: for styling the notification as error
                                ->send();

                            return;
                        }

                        $record->normalisasi = Variables::normalizeData($record->parameters);
                        $record->status = 1; // Set status to 1 after normalizing
                        $record->save();

                        Notification::make()
                            ->title('Success')
                            ->body('Data berhasil dinormalisasi.')
                            ->success() // Optional: for styling the notification as success
                            ->send();
                    })
                    ->visible(function ($record) {
                        return $record->status == 0; // Show button only if not normalized
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('status', 'desc'); // Sort by 'status' column, descending order
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        foreach ($data['parameters'] as $key => $value) {
            if (is_array($value) && empty($value)) {
                $data['parameters'][$key] = [0]; // Set default value to 0 if no option is selected
            }
        }

        return $data;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVariables::route('/'),
            'create' => Pages\CreateVariable::route('/create'),
            'edit' => Pages\EditVariable::route('/{record}/edit'),
        ];
    }
}
