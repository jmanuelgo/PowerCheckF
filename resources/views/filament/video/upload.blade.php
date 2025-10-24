<x-filament-panels::page>
    <x-filament-panels::header :heading="$title ?? 'Subir video'" subheading="Máx: 70MB | Formato: mp4/mov/avi/mkv" />
    {{-- AÑADIDO: LÍMITE MAX. AL SUBHEADING --}}

    <x-filament::section>
        {{-- CAMBIO 1: Añadido id="upload-form" --}}
        <form action="{{ route('video.upload') }}" method="post" enctype="multipart/form-data" class="space-y-4"
            id="upload-form">
            @csrf

            <input type="hidden" name="movement" value="{{ $movement }}">

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <div class="text-sm text-gray-600">Ejercicio</div>
                    @php $labels = ['squat'=>'Sentadilla','bench'=>'Press banca','deadlift'=>'Peso muerto']; @endphp
                    <div class="font-semibold">{{ $labels[$movement] ?? $movement }}</div>
                </div>

                <label class="block">
                    <span class="text-sm text-gray-600">Video</span>
                    <input type="file" name="video" {{-- CAMBIO 2: Añadido id="video-input" --}}
                        accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska" required
                        class="block w-full mt-1" id="video-input">
                    <p class="mt-1 text-xs text-gray-500">Tamaño máximo: 70 MB.</p>
                </label>
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="bar_manual" value="1" checked>
                <span class="text-sm text-gray-600">Pedir ubicación de la barra (modo manual)</span>
            </label>

            {{-- CAMBIO 3: Área para mostrar el error de JavaScript --}}
            <div id="file-size-error" class="text-sm text-danger-600"></div>

            <div>
                {{-- CAMBIO 4: Añadido id="submit-button" --}}
                <x-filament::button type="submit" icon="heroicon-o-arrow-up-tray" id="submit-button">
                    Subir y analizar
                </x-filament::button>
            </div>

            @if ($errors->any())
                <div class="text-sm text-danger-600">{{ $errors->first() }}</div>
            @endif
        </form>
    </x-filament::section>

    <script>
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('video-input');
            const errorDiv = document.getElementById('file-size-error');
            const submitButton = document.getElementById('submit-button');

            // 70 MB en bytes
            const MAX_SIZE_BYTES = 70 * 1024 * 1024;

            errorDiv.textContent = '';

            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size;

                if (fileSize > MAX_SIZE_BYTES) {
                    e.preventDefault();
                    errorDiv.textContent =
                        '⛔ ¡El video es demasiado grande! El tamaño máximo permitido es de 70 MB.';


                    submitButton.disabled = true;
                    setTimeout(() => submitButton.disabled = false, 3000);

                    return false;
                }
            }
        });

        document.getElementById('video-input').addEventListener('change', function() {
            document.getElementById('file-size-error').textContent = '';
            document.getElementById('submit-button').disabled = false;
        });
    </script>
</x-filament-panels::page>
