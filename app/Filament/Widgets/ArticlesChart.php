<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\ChartWidget;

class ArticlesChart extends ChartWidget
{
    protected static ?string $heading = 'Chart Artikel';
    protected static ?int $sort = 2;


    protected function getData(): array
    {
        $data = array_fill(0, 12, 0);

        $ArticleData = Article::query()
            ->selectRaw('COUNT(*) as count, MONTH(tanggal_publikasi) as month')
            ->whereYear('tanggal_publikasi', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($ArticleData as $Article) {
            $data[$Article->month - 1] = $Article->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Article posts created',
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
