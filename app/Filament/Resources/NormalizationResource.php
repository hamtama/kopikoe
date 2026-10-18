<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NormalizationResource\Pages;
use App\Models\Variables;
use App\Models\Parameters;
use App\Models\JenisKopi;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class NormalizationResource extends Resource
{
    protected static ?string $model = Variables::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Matriks Normalisasi';
    protected static ?string $navigationGroup = 'Data Testing Collection';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'normalizations';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama Sampel Kopi')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->description(fn ($record) => ($record->JenisKopi->jenis_kopi ?? 'Kopi') . ' • Supplier: ' . ($record->nama_supplier ?? '-')),

            Tables\Columns\BadgeColumn::make('JenisKopi.jenis_kopi')
                ->label('Jenis Kopi')
                ->color('primary')
                ->sortable(),
        ];

        $parameters = Parameters::all();
        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            $columns[] = Tables\Columns\TextColumn::make("norm_{$transformedName}")
                ->label($parameter->kode)
                ->tooltip($parameter->name . ' (' . ucfirst($parameter->type) . ')')
                ->getStateUsing(function ($record) use ($transformedName, $parameter) {
                    if (!$record->normalisasi) {
                        return '-';
                    }

                    $normalized = is_array($record->normalisasi) ? $record->normalisasi : json_decode($record->normalisasi, true);

                    if (!is_array($normalized)) {
                        return '-';
                    }

                    if ($parameter->is_multiple) {
                        // Kriteria multi: hitung nilai atau filter yang aktif
                        $values = array_filter($normalized, function ($key) use ($transformedName) {
                            return strpos($key, $transformedName . '_') === 0;
                        }, ARRAY_FILTER_USE_KEY);

                        if (!empty($values)) {
                            $active = array_sum($values);
                            return "[{$active}/" . count($values) . "]";
                        }
                        return '[0]';
                    }

                    if (isset($normalized[$transformedName])) {
                        return number_format((float)$normalized[$transformedName], 3);
                    }

                    return '0.000';
                })
                ->alignCenter();
        }

        $columns[] = Tables\Columns\BadgeColumn::make('status')
            ->label('Status')
            ->formatStateUsing(fn ($state) => $state ? 'Siap Uji' : 'Belum')
            ->colors([
                'success' => 1,
                'warning' => 0,
            ])
            ->icons([
                'heroicon-o-check-circle' => 1,
                'heroicon-o-clock' => 0,
            ])
            ->alignCenter();

        return $table
            ->columns($columns)
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_kopi')
                    ->label('Filter Jenis Kopi')
                    ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi')),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status Normalisasi')
                    ->trueLabel('Siap Uji (Ternormalisasi)')
                    ->falseLabel('Belum Dinormalisasi'),
            ])
            ->actions([
                Action::make('recalculate')
                    ->label('Hitung Ulang')
                    ->icon('heroicon-o-refresh')
                    ->color('secondary')
                    ->action(function ($record) {
                        if (!$record->isValid()) {
                            Notification::make()
                                ->title('Data Belum Lengkap')
                                ->body('Data parameter belum lengkap untuk dinormalisasi.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->normalisasi = Variables::normalizeData($record->parameters);
                        $record->status = 1;
                        $record->save();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Vektor normalisasi data berhasil dihitung ulang.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkRecalculate')
                    ->label('Hitung Ulang Terpilih')
                    ->icon('heroicon-o-refresh')
                    ->color('secondary')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->isValid()) {
                                $record->normalisasi = Variables::normalizeData($record->parameters);
                                $record->status = 1;
                                $record->save();
                                $count++;
                            }
                        }
                        Notification::make()
                            ->title('Selesai')
                            ->body("{$count} data berhasil dihitung ulang.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['JenisKopi'])
            ->orderBy('status', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNormalizations::route('/'),
        ];
    }
}
