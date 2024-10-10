<?php

namespace App\Filament\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as Livewire;

class CancelCancelation extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'cancelCancelation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle('Cancelation canceled');

        $this->failureNotificationTitle('Error');

        $this->label('Cancel cancelation');

        $this->color('gray');

        $this->icon('heroicon-o-arrow-uturn-left');

        $this->fillForm(fn (?Model $record): array => [
            'title' => '[Post] ' .$record->title,
        ]);

        $this->form([
            TextInput::make('title')
            ->required(),
        ]);

        $this->action(function (array $data, Model $record, Livewire $livewire): void {

            $record->cancelation->delete();

            $result = $record->update($data);

            if (! $result) {
                $this->failure();

                return;
            }
            $this->success();

        });

        $this->modalHeading('Cancel cancelation');

        $this->modalSubmitAction(fn (StaticAction $action) => $action
            ->label('Save')
            ->color('primary')
        );

        $this->slideOver();

    }
}
