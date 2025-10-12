<x-filament-panels::page>
    <x-filament-panels::header :heading="$title ?? 'Subir video'" subheading="Formato: mp4/mov/avi/mkv" />

    <x-filament::section>
        <form action="{{ route('video.upload') }}" method="post" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- movimiento viene como variable normal --}}
            <input type="hidden" name="movement" value="{{ $movement }}">

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <div class="text-sm text-gray-600">Ejercicio</div>
                    @php $labels = ['squat'=>'Sentadilla','bench'=>'Press banca','deadlift'=>'Peso muerto']; @endphp
                    <div class="font-semibold">{{ $labels[$movement] ?? $movement }}</div>
                </div>

                <label class="block">
                    <span class="text-sm text-gray-600">Video</span>
                    <input type="file" name="video"
                        accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska" required
                        class="block w-full mt-1">
                </label>
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="bar_manual" value="1" checked>
                <span class="text-sm text-gray-600">Pedir ubicación de la barra (modo manual)</span>
            </label>

            <div>
                <x-filament::button type="submit" icon="heroicon-o-arrow-up-tray">
                    Subir y analizar
                </x-filament::button>
            </div>

            @if ($errors->any())
                <div class="text-sm text-danger-600">{{ $errors->first() }}</div>
            @endif
        </form>
    </x-filament::section>
</x-filament-panels::page>
