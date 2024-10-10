<?php

namespace App\Filament\Actions;

use App\Models\Comment;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as Livewire;

class AddComment extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'addComment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle('Comment added');

        $this->failureNotificationTitle('Failed to add comment');

        $this->label('Add comment');

        $this->color('info');

        $this->form(fn (Model $record) => [
            Section::make('Add a comment')
                ->description('You can add comment here')
                ->live()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            Select::make('template')
                                ->label('Chhose template')
                                ->options(['templateA' => 'Template A', 'templateB' => 'Template B'])
                                ->native(false)
                                ->afterStateUpdated(function (Set $set, $state): void {
                                    $template = match ($state) {
                                        'templateA' => 'My template A',
                                        'templateB' => 'My template B',
                                        default => null,
                                    };
                                    if ($template) {
                                        $set('title', $template);
                                    }
                                }),

                            TextInput::make('title')
                                ->required(),

                            Textarea::make('content')
                            ->required()
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(1),

        ]);

        $this->action(function (array $data, Livewire $livewire, Model $record): void {

            $comment = new Comment([
                'title' => $data['title'],
                'content' => $data['content'],
            ]);
            $result = $record->comments()->save($comment);

            if (! $result) {
                $this->failure();

                return;
            }
            $this->success();
            //refresh table
            $livewire->dispatch('refreshListComments');
        });


        $this->modalHeading('Add new comment');


        $this->modalSubmitAction(fn (StaticAction $action) => $action
            ->label('Add')
            ->color('primary'));
    }
}
