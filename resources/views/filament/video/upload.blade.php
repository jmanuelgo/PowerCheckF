<x-filament-panels::page>
    <x-filament-panels::header :heading="$title ?? 'Subir video'" subheading="Máx: 70MB | Formato: mp4/mov/avi/mkv" />

    <x-filament::section>

        <form action="{{ route('video.upload') }}" method="post" enctype="multipart/form-data" class="space-y-6"
            id="upload-form">
            @csrf

            <input type="hidden" name="movement" value="{{ $movement }}">

            <div class="grid gap-6 md:grid-cols-3">

                <div class="space-y-1">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Ejercicio</div>
                    @php $labels = ['squat'=>'Sentadilla','bench'=>'Press banca','deadlift'=>'Peso muerto']; @endphp
                    <div class="font-semibold text-lg dark:text-white">{{ $labels[$movement] ?? $movement }}</div>
                </div>

                <label class="block">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Peso (KG)</span>
                    <input type="number" name="weight" min="0" step="0.01"
                           placeholder="Ej. 100.50"
                           class="block w-full mt-1 rounded-lg shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">El peso total levantado en el video.</p>
                </label>

                <label class="block">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Video</span>
                    <input type="file" name="video"
                           accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska" required
                           class="block w-full mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                           id="video-input">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tamaño máximo: 70 MB.</p>
                </label>
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="bar_manual" value="1" checked
                       class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-600 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                <span class="text-sm text-gray-600 dark:text-gray-400">Pedir ubicación de la barra (modo manual)</span>
            </label>

            <div id="file-size-error" class="text-sm text-danger-600"></div>

            <div>
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
        // Tu script de validación de tamaño no necesita cambios.
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('video-input');
            const errorDiv = document.getElementById('file-size-error');
            const submitButton = document.getElementById('submit-button');
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
