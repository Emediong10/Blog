<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResourcResource\RelationManagers\CommentsRelationManager;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Edit Blog')
                    ->description('Edit Blog Post')
                    ->schema([
                        TextInput::make('title')
                        ->required()
                        // ->disabledOn('edit')
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            $set('slug', Str::slug($state));
                         })
                         ->minLength(2)
                         ->maxLength(150)
                        ->debounce('500ms')
                        ->label('Post Title'),
                        TextInput::make('slug'),

                    RichEditor::make('content')
                    ->required()
                    ->fileAttachmentsDirectory('posts/images')
                    ->columnSpanFull(),
                    ])
                    ->columns(2),

                    Section::make('Edit This Blog')
                        ->description('')
                        ->schema([

                            DateTimePicker::make('published_at')->nullable(),

                            Select::make('user_id')
                                ->relationship('author','name')
                                ->searchable()->preload()->nullable(),

                            Select::make('categories')
                            ->relationship('categories', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),

                        ])
                        ->columns(2),

                        Section::make('Edit Blog')
                            ->description('')
                            ->schema([
                                FileUpload::make('image')->image()->directory('posts/thumbnails'),
                                Checkbox::make('active')->label('Check the box to make the post active'),
                            ])
                            // ->columns(2),

            ]);
    }

    protected function afterSave($record, array $data): void
{
    // Sync the categories relationship to the pivot table
    $record->categories()->sync($data['categories'] ?? []);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('slug')->searchable()->sortable(),
                TextColumn::make('author.name')->searchable()->sortable(),
                TextColumn::make('published_at')->date()->searchable()->sortable(),
                CheckboxColumn::make('active'),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
          CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
