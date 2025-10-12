<?php

namespace App\Filament\Pages\Video;

use Filament\Pages\Page;

class UploadDeadlift extends Page
{
    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.video.upload';
    protected static ?string $title = 'Subir video – Peso muerto';

    public string $movement = 'deadlift';

    public function mount(): void {}
}
