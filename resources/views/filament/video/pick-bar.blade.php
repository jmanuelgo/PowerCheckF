<x-filament-panels::page>

    {{-- Pantalla de Carga (Añadido) --}}
    <div id="loading-overlay" class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-gray-900/80"
        style="display: none;">
        <div class="p-8 text-center bg-white shadow-2xl rounded-xl dark:bg-gray-800">
            <x-filament::loading-indicator class="w-10 h-10 mx-auto mb-4 text-primary-600" />
            <h4 class="text-xl font-semibold text-gray-900 dark:text-white">Análisis en curso</h4>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                El procesamiento de video puede tardar varios minutos (30s a 3min).
                <br>
                Por favor, espere. Será redirigido automáticamente.
            </p>
            <p class="mt-4 text-2xl font-bold text-primary-600 dark:text-primary-400">
                Tiempo transcurrido: <span id="timer">00:00</span>
            </p>
        </div>
    </div>

    {{-- Contenido principal --}}
    <x-filament-panels::header heading="Seleccionar barra" subheading="Click centro + click borde para definir radio" />

    <x-filament::section>
        <canvas id="c" class="h-auto max-w-full border border-gray-200 rounded-xl"></canvas>

        {{-- Análisis automático (MediaPipe + barpath con el pick) --}}
        {{-- Añadido ID al formulario para capturar el evento submit --}}
        <form method="POST" action="{{ route('video.manual') }}" id="analysis-form">
            @csrf
            <input type="hidden" name="job_id" value="{{ $record->job_id }}">
            <input type="hidden" name="cx" id="cx">
            <input type="hidden" name="cy" id="cy">
            <input type="hidden" name="r" id="r">

            {{-- Añadida clase mt-4 para separación --}}
            <x-filament::button id="go" type="submit" icon="heroicon-o-sparkles" class="mt-4">
                Analizar automáticamente
            </x-filament::button>
        </form>
    </x-filament::section>

    <script>
        // --- Referencias DOM ---
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.src = @json($record->frame_url);

        const c = document.getElementById('c');
        const ctx = c.getContext('2d');
        const btn = document.getElementById('go');
        const form = document.getElementById('analysis-form'); // Referencia al formulario

        const cxI = document.getElementById('cx');
        const cyI = document.getElementById('cy');
        const rI = document.getElementById('r');

        // Carga y Temporizador
        const overlay = document.getElementById('loading-overlay');
        const timerDisplay = document.getElementById('timer');
        let clicks = [];
        let startTime = 0;
        let timerInterval = null;

        // --- Funciones del Canvas (sin cambios significativos) ---

        img.onload = () => {
            c.width = img.width;
            c.height = img.height;
            ctx.drawImage(img, 0, 0);
        };

        c.addEventListener('click', e => {
            // ... (Lógica de click original)
            const r = c.getBoundingClientRect();
            const x = Math.round((e.clientX - r.left) * (c.width / r.width));
            const y = Math.round((e.clientY - r.top) * (c.height / r.height));

            clicks.push({
                x,
                y
            });
            redraw();

            if (clicks.length === 2) {
                const dx = clicks[1].x - clicks[0].x;
                const dy = clicks[1].y - clicks[0].y;
                const rad = Math.max(3, Math.round(Math.hypot(dx, dy)));

                cxI.value = clicks[0].x;
                cyI.value = clicks[0].y;
                rI.value = rad;

                btn.disabled = false;
            }
        });

        function redraw() {
            // ... (Lógica de redibujo original)
            ctx.clearRect(0, 0, c.width, c.height);
            ctx.drawImage(img, 0, 0);

            if (clicks[0]) {
                ctx.beginPath();
                ctx.arc(clicks[0].x, clicks[0].y, 5, 0, Math.PI * 2);
                ctx.lineWidth = 3;
                ctx.stroke();
            }

            if (clicks.length === 2) {
                const dx = clicks[1].x - clicks[0].x;
                const dy = clicks[1].y - clicks[0].y;
                const rad = Math.max(3, Math.hypot(dx, dy));

                ctx.beginPath();
                ctx.setLineDash([10, 6]);
                ctx.lineWidth = 3;
                ctx.arc(clicks[0].x, clicks[0].y, rad, 0, Math.PI * 2);
                ctx.stroke();
                ctx.setLineDash([]);
            }
        }


        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
        }

        function updateTimer() {
            const elapsedTime = Math.floor((Date.now() - startTime) / 1000);
            timerDisplay.textContent = formatTime(elapsedTime);
        }

        form.addEventListener('submit', function(e) {

            if (btn.disabled) {
                e.preventDefault();
                return;
            }


            btn.disabled = true;


            overlay.style.display = 'flex';


            startTime = Date.now();
            timerDisplay.textContent = '00:00';
            timerInterval = setInterval(updateTimer, 1000);

        });
    </script>
</x-filament-panels::page>
