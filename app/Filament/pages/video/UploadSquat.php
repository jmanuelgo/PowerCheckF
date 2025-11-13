<?php

namespace App\Filament\Pages\Video;

use Filament\Pages\Page;

class UploadSquat extends Page
{
    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.video.upload';
    protected static ?string $title = 'Subir video – Sentadilla';

    public string $movement = 'squat';

    public function mount(): void {}
}
