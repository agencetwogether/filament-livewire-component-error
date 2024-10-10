<?php

namespace App\Components;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Livewire\Attributes\On;
use Livewire\Component;

class ListComments extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Model $record;

    #[On('refreshListComments')]
    public function refresh(): void {}

    public function table(Table $table): Table
    {
        return $table
            ->striped()
            ->relationship(fn (): MorphMany => $this->record->comments()->latest())
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('created_at'),
            ])
            ->actions([
                ViewAction::make()
                    ->form([
                        TextInput::make('title'),
                        Textarea::make('content'),
                    ]),
                ActionGroup::make([
                    DeleteAction::make()
                ]),
            ])
            ->bulkActions([]);
    }

    public function render()
    {
        return view('components.list-comments');
    }
}
