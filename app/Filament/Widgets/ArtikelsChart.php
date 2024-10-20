<?php

namespace App\Filament\Widgets;

use App\Models\Artikel;
use Filament\Widgets\ChartWidget;

class ArtikelsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart Artikel';
    protected static ?int $sort = 2;


    protected function getData(): array
    {
        $data = array_fill(0, 12, 0);

        $artikelData = Artikel::query()
            ->selectRaw('COUNT(*) as count, MONTH(tanggal_publikasi) as month')
            ->whereYear('tanggal_publikasi', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($artikelData as $artikel) {
            $data[$artikel->month - 1] = $artikel->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Artikel posts created',
                    'data' => $data,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
