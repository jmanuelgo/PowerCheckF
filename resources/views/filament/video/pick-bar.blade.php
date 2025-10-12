<x-filament-panels::page>
    <x-filament-panels::header heading="Seleccionar barra" subheading="Click centro + click borde para definir radio" />

    <x-filament::section>
        <canvas id="c" class="h-auto max-w-full border border-gray-200 rounded-xl"></canvas>

        {{-- Análisis automático (MediaPipe + barpath con el pick) --}}
        <form method="POST" action="{{ route('video.manual') }}">
            @csrf
            <input type="hidden" name="job_id" value="{{ $record->job_id }}">
            <input type="hidden" name="cx" id="cx">
            <input type="hidden" name="cy" id="cy">
            <input type="hidden" name="r" id="r">
            <x-filament::button id="go" type="submit" icon="heroicon-o-sparkles">
                Analizar automáticamente
            </x-filament::button>
        </form>


        {{-- (Aún no) Análisis manual completo --}}
        {{--
        <form class="mt-2" method="post" action="{{ route('video.startManual') }}">
            @csrf
            <input type="hidden" name="job_id" value="{{ $record->job_id }}">
            <x-filament::button color="gray" outlined icon="heroicon-o-pencil-square">
                Análisis manual completo
            </x-filament::button>
        </form>
        --}}
    </x-filament::section>

    <script>
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.src = @json($record->frame_url);

        const c = document.getElementById('c');
        const ctx = c.getContext('2d');
        const btn = document.getElementById('go');
        const cxI = document.getElementById('cx');
        const cyI = document.getElementById('cy');
        const rI = document.getElementById('r');

        let clicks = [];

        img.onload = () => {
            c.width = img.width;
            c.height = img.height;
            ctx.drawImage(img, 0, 0);
        };

        c.addEventListener('click', e => {
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
    </script>
</x-filament-panels::page>
