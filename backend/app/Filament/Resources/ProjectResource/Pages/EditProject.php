<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Services\GithubRepositoryService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncGithub')
                ->label('Sync GitHub')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function () {
                    $service = app(GithubRepositoryService::class);
                    $service->clearCache($this->record);
                    $service->syncProject($this->record);
                })
                ->successNotificationTitle('GitHub metadata refreshed.'),
            Actions\DeleteAction::make(),
        ];
    }
}
