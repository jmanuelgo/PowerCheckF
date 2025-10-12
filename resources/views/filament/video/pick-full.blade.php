<x-filament-panels::page>
    <x-filament-panels::header heading="Análisis manual completo"
        subheading="Orden: Cadera → Rodilla → Tobillo → Barra (centro) + segundo click = radio" />

    <x-filament::section>
        <canvas id="c" class="h-auto max-w-full border border-gray-200 rounded-xl"></canvas>

        <form class="flex flex-wrap gap-3 mt-4" method="post" action="{{ route('video.manualFull') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $record->id }}">
            <input type="hidden" name="hx" id="hx"><input type="hidden" name="hy" id="hy">
            <input type="hidden" name="kx" id="kx"><input type="hidden" name="ky" id="ky">
            <input type="hidden" name="ax" id="ax"><input type="hidden" name="ay" id="ay">
            <input type="hidden" name="cx" id="cx"><input type="hidden" name="cy" id="cy">
            <input type="hidden" name="r" id="r">
            <x-filament::button id="go" type="submit" icon="heroicon-o-check"
                disabled>Procesar</x-filament::button>
        </form>
    </x-filament::section>

    <script>
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.src = @json($record->frame_url);
        const c = document.getElementById('c'),
            ctx = c.getContext('2d');
        let step = 0,
            p = {};
        img.onload = () => {
            c.width = img.width;
            c.height = img.height;
            ctx.drawImage(img, 0, 0);
        };
        c.addEventListener('click', e => {
            const r = c.getBoundingClientRect();
            const x = Math.round((e.clientX - r.left) * (c.width / r.width));
            const y = Math.round((e.clientY - r.top) * (c.height / r.height));
            if (step === 0) {
                p.hx = x;
                p.hy = y;
                step = 1;
            } else if (step === 1) {
                p.kx = x;
                p.ky = y;
                step = 2;
            } else if (step === 2) {
                p.ax = x;
                p.ay = y;
                step = 3;
            } else if (step === 3) {
                p.cx = x;
                p.cy = y;
                step = 4;
            } else if (step === 4) {
                const dx = x - p.cx,
                    dy = y - p.cy;
                p.r = Math.max(3, Math.round(Math.hypot(dx, dy)));
                for (const k of ['hx', 'hy', 'kx', 'ky', 'ax', 'ay', 'cx', 'cy', 'r']) document.getElementById(k)
                    .value = p[k];
                document.getElementById('go').disabled = false;
            }
            redraw();
        });

        function redraw() {
            ctx.clearRect(0, 0, c.width, c.height);
            ctx.drawImage(img, 0, 0);
            dot(p.hx, p.hy, 'H');
            dot(p.kx, p.ky, 'K');
            dot(p.ax, p.ay, 'A');
            dot(p.cx, p.cy, 'C');
            if (p.cx && p.cy && p.r) {
                ctx.beginPath();
                ctx.setLineDash([10, 6]);
                ctx.lineWidth = 3;
                ctx.arc(p.cx, p.cy, p.r, 0, Math.PI * 2);
                ctx.stroke();
                ctx.setLineDash([]);
            }
        }

        function dot(x, y, l) {
            if (!x || !y) return;
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, Math.PI * 2);
            ctx.lineWidth = 3;
            ctx.stroke();
            ctx.fillText(l, x + 8, y - 8);
        }
    </script>
</x-filament-panels::page>
