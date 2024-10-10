<?php

namespace App\Filament\Resources\MailingResource\Pages;

use App\Filament\Resources\MailingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMailing extends CreateRecord
{
    protected static string $resource = MailingResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
