<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ArticleOverview extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Artikel Populer';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Article::where('total_view', '>', 0)
                    ->orderBy('total_view', 'desc');
            })
            ->columns([
                TextColumn::make(('judul'))->label('Judul'),
                TextColumn::make(('total_view'))->label('Total View'),
            ]);
    }
}
