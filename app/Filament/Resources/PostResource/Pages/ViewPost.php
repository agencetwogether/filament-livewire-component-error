<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Components\ListComments;
use App\Filament\Actions\AddComment;
use App\Filament\Actions\EditPost;
use App\Filament\Actions\CancelCancelation;
use App\Filament\Actions\RegisterCancelation;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Str;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RegisterCancelation::make()
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ...static::getPostFormSchema(),
                ...static::getCancelationFormSchema(),
                ...static::getCommentsFormSchema(),
            ]);
    }

    public static function getPostFormSchema(): array
    {
        return [
            Section::make('Post')
                ->schema([
                    TextEntry::make('title'),
                    TextEntry::make('created_at'),
                ]),
        ];
    }

    public static function getCancelationFormSchema(): array
    {
        return [
            Section::make('A cancelation is registered to this post')
                ->description(fn (Post $record): string => "Cancelation registered {$record->cancelation?->date->format('M j, Y')} by {$record->cancelation?->causer}")
                ->icon('heroicon-o-hand-raised')
                ->iconColor('danger')
                ->schema([
                    TextEntry::make('cancelation.reason')
                        ->hiddenLabel(),
                ])
                ->headerActions([
                    CancelCancelation::make(),
                ])
                ->collapsible()
                ->collapsed()
                ->columns()
                ->extraAttributes([
                    'class' => '!bg-danger-100 dark:!bg-danger-950',
                ])
                ->visible(fn (Post $record): bool => $record->cancelation()->exists()),

        ];
    }

    public static function getCommentsFormSchema(): array
    {
        return [
            Section::make('Comments')
                ->description('See comments to this post')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Livewire::make(ListComments::class)
                        ->key('tableListComments'),
                ])
                ->headerActions([
                    AddComment::make(),
                ])
                ->collapsible()
                ->extraAttributes(['class' => 'section-no-padding'])
                /*
                 * If you remove ->visible(), error like
                 * Uncaught Snapshot missing on Livewire component with id: lkqQQZ5rP0rfSnSQeFhS appears in console devtools and all buttons stop working
                 */
                ->visible(fn (Model $record): bool => !$record->cancelation()->exists()),
        ];
    }
}
