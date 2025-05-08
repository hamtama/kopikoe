<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParametersResource\Pages;
use App\Filament\Resources\ParametersResource\RelationManagers;
use App\Models\Parameters;
use App\Models\SubParameter;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ParametersResource extends Resource
{
    protected static ?string $model = Parameters::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Data Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->label('Kode')
                    ->default(fn () => Parameters::generateKode())
                    ->required()
                    ->unique(ignorable: fn ($record) => $record)
                    ->extraAttributes(['readonly' => true]),
                Forms\Components\TextInput::make('name')->required(),

                Forms\Components\Select::make('type')
                    ->options(['benefit' => 'Benefit', 'cost' => 'Cost'])
                    ->required(),
                Forms\Components\TextInput::make('min')->required(),
                Forms\Components\TextInput::make('max')->required(),
                Forms\Components\Toggle::make('is_multiple')->label('Is Multiple?')->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kode')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('max')->label('Bobot'),
                Tables\Columns\BooleanColumn::make('is_multiple'),
                Tables\Columns\TextColumn::make('subParameters')
                    ->label('Sub Kategori')
                    ->formatStateUsing(function ($state) {
                        $subCategories = $state->isNotEmpty() ? implode(', ', $state->pluck('name')->toArray()) : 'Tidak ada subkategori';
                        return Str::limit($subCategories, 30); // Ubah 50 dengan jumlah karakter yang diinginkan
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('addSubParameter')
                    ->label('Add SubParameter')
                    ->icon('heroicon-o-plus')
                    ->form(fn ($record) => [
                        Forms\Components\Hidden::make('parameter_id')->default($record->parameter_id),
                        Forms\Components\TextInput::make('name')
                            ->label('SubParameter Name')
                            ->required(),
                        Forms\Components\TextInput::make('bobot')
                            ->label('Bobot')
                            ->type('number')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999)
                            ->step(1),
                    ])
                    ->action(function ($data) {
                        SubParameter::create([
                            'parameter_id' => $data['parameter_id'],
                            'name' => $data['name'],
                            'bobot' => $data['bobot'],
                        ]);
                    })
                    ->modalHeading('Add SubParameter')
                    ->modalButton('Add')
                    ->visible(fn ($record) => $record->is_multiple),

                Tables\Actions\ViewAction::make('viewDetails')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Parameter')
                    ->modalContent(fn ($record) => new HtmlString('<div>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px;">Field</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Value</th>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">Name</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">' . $record->name . '</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">Type</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">' . $record->type . '</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">Min</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">' . $record->min . '</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">Max</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">' . $record->max . '</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">Is Multiple</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">' . ($record->is_multiple ? 'Ya' : 'Tidak') . '</td>
                            </tr>
                        </table>
                    </div>'))
                    ->modalButton('Close')
                    ->disableForm(),


            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListParameters::route('/'),
            'create' => Pages\CreateParameters::route('/create'),
            'edit' => Pages\EditParameters::route('/{record}/edit'),
        ];
    }
}
