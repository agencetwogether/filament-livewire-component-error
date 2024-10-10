<?php

namespace App\Filament\Actions;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component as Livewire;

class RegisterCancelation extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'registerCancelation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->successNotificationTitle('Post canceled with success');

        $this->failureNotificationTitle('Post not canceled, a failure happened');

        $this->label('Register Cancelation');

        $this->color('danger');

        $this->form([
            Group::make()
                ->relationship('cancelation')
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    $data['date'] = Carbon::now();
                    $data['causer'] = auth()->user()->name;

                    return $data;
                })
                ->schema([
                    Textarea::make('reason')
                        ->label('Reason')
                        ->rows(10)
                        ->columnSpanFull()
                        ->required(),
                ])
                ->columnSpanFull(),
        ]);

        $this->action(function (array $data, Model $record, Livewire $livewire): void {

            $result = $record->update($data);

            if (! $result) {
                $this->failure();

                return;
            }
            $this->success();

        });

        $this->modalHeading('Register a cancelation to this post');

        $this->modalSubmitAction(fn (StaticAction $action) => $action
            ->label('Save')
            ->color('danger')
        );

        $this->slideOver();

        $this->hidden(fn (Model $record) => $record->cancelation()->exists());
    }
}
