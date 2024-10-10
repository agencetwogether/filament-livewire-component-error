## Issue with livewire component in Infolist after calling action

This repo demonstrate an issue with a livewire Component (a table) in Infolist

```git clone git@github.com:agencetwogether/filament-livewire-component-error.git```

```cd filament-livewire-component-error```

```composer install```

```npm install```

```npm run build```

```cp .env.example .env```

```php artisan key:generate```

```php artisan migrate --seed```

Go to your browser and connect with credentials
```admin@admin.com // password```

See the problem in **View Post Page**.

Open your DevTools window and go to Console tab.
When you click on `Register Cancelation` button in header action, fill the reason in textarea and Save.

In View Page, a Section with red background is shown and Comments section is hidden.

If you click on `Cancel cancelation` in header of this red section. A slideOver opens, to modify title of current Post (no sense here but the goal is to interract with data in real project).

When you hit ```Save``` button, a new error appears in Console like this ```Uncaught (in promise) Component not found: uVTyoagOvbBWvJbGHy4J```.
And as expected, Comments section is shown but with this console error.

Livewire Component is registered in ```AdminPanelProvider```

```php
//AdminPanelProvider.php
...

public function boot(): void{
    Livewire::component('list-comments', ListComments::class);
}

...
```

```php
//ViewPost.php
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
```

```php
//RegisterCancelation.php
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
```

```php
//CancelCancelation.php
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
```

Despite this error, buttons and page behavior works, but I don't understand why I had this error.

In fact, what I want to do is to hide or not a section containing a livewire component table after an action call.

If I remove 
```php
->visible(fn (Model $record): bool => !$record->cancelation()->exists())
```
from my Comments section, after calling ```Register cancelation``` button, a new error appears like ```Uncaught Snapshot missing on Livewire component with id: XabIhO5vvVvPDuXzwTwr```, and my section containing livewire component table (Comments section) is empty and none actions work in page neither ```Cancel cancelation``` neither  ```Add comment```.

If someone can me explain how to remove this error, it will be very helpful.
