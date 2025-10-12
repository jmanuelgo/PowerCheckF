<?php

namespace App\Filament\Pages\Video;

use Filament\Pages\Page;

class UploadBench extends Page
{
    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.video.upload';
    protected static ?string $title = 'Subir video – Press banca';

    public string $movement = 'bench';

    public function mount(): void {}
}
