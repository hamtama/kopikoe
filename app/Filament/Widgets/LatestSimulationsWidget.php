<?php

namespace App\Filament\Widgets;

use App\Models\Simulation;
use App\Models\JenisKopi;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSimulationsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Aktivitas Pengujian & Rekomendasi Terkini';

    public static function getName(): string
    {
        return 'app.filament.widgets.latest-simulations-widget';
    }

    protected function getTableQuery(): Builder
    {
        return Simulation::query()->latest('id');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    protected function getTableRecordsPerPage(): int
    {
        return 5;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'id';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),

            Tables\Columns\TextColumn::make('input_data.name')
                ->label('Skenario / Penguji')
                ->getStateUsing(fn ($record) => $record->input_data['name'] ?? ('Simulasi #' . $record->id))
                ->weight('bold'),

            Tables\Columns\BadgeColumn::make('target_kopi')
                ->label('Target Jenis Kopi')
                ->getStateUsing(function ($record) {
                    $jkId = $record->input_data['jenis_kopi'] ?? null;
                    if (!$jkId) return '-';
                    $jk = JenisKopi::find($jkId);
                    return $jk ? $jk->jenis_kopi : "ID: {$jkId}";
                })
                ->color('primary'),

            Tables\Columns\TextColumn::make('top_recommendation')
                ->label('Rekomendasi Utama (#1)')
                ->getStateUsing(function ($record) {
                    $recs = $record->recommendations;
                    if (empty($recs) || !is_array($recs)) return 'Belum ada rekomendasi';
                    $top = $recs[0] ?? null;
                    if (!$top) return '-';
                    $supplier = !empty($top['nama_supplier']) ? ' (' . $top['nama_supplier'] . ')' : '';
                    return ($top['name'] ?? 'Kopi') . $supplier;
                })
                ->icon('heroicon-o-sparkles')
                ->weight('medium'),

            Tables\Columns\BadgeColumn::make('similarity_score')
                ->label('Tingkat Kemiripan')
                ->getStateUsing(function ($record) {
                    $recs = $record->recommendations;
                    if (empty($recs) || !is_array($recs)) return '-';
                    $distance = $recs[0]['distance'] ?? null;
                    if ($distance === null) return '-';
                    $sim = max(0, min(100, round((1 / (1 + (float)$distance)) * 100, 1)));
                    $formattedDist = round((float)$distance, 3);
                    return "{$sim}% (d: {$formattedDist})";
                })
                ->colors([
                    'success' => fn ($state): bool => str_contains($state, '%') && (float)$state >= 80,
                    'warning' => fn ($state): bool => str_contains($state, '%') && (float)$state >= 60 && (float)$state < 80,
                    'danger' => fn ($state): bool => str_contains($state, '%') && (float)$state < 60,
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu Pengujian')
                ->dateTime('d M Y, H:i')
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('viewDetail')
                ->label('Lihat Hasil')
                ->icon('heroicon-o-eye')
                ->modalHeading('Rincian Hasil Rekomendasi KNN')
                ->modalContent(function ($record) {
                    $recs = $record->recommendations ?? [];
                    $input = $record->input_data ?? [];
                    return view('filament.components.simulation-detail-modal', [
                        'record' => $record,
                        'recs' => $recs,
                        'input' => $input,
                    ]);
                }),
        ];
    }
}
