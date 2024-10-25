<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Models\Comment;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ViewComments extends Page implements HasTable
{
    use InteractsWithTable;
    public $articleId;
    public $comment;

    // protected static ?string $model = Comment::class;
    protected static string $resource = ArticleResource::class;
    
    protected static string $view = 'filament.resources.article-resource.pages.view-comments';
    
    protected static ?string $title = 'Komentar Artikel';
    public function mount()
    {
        $this->articleId = request()->route('record');
        $this->comment = Comment::where('article_id', $this->articleId)->first();

        if ($this->comment && $this->comment->article) {
            static::$title = 'Komentar Artikel: ' . $this->comment->article->judul;
        }
    }
    
    public function table(Table $table): Table
    {

        return $table
            ->query(Comment::query()->where('article_id', $this->articleId))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->url(fn (Comment $record) => route('filament.admin.resources.users.view', ['record' => $record->user_id])),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('komentar')
                    ->label('Komentar')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->searchable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }
}