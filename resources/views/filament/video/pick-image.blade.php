<div x-data="picker('{{ $src }}', '{{ $mode }}')" class="space-y-2">
    <img :src="src" x-ref="img" class="border rounded-lg" style="max-width:100%;">
    <template x-if="mode==='bar'">
        <div class="text-xs text-gray-500">Click = centro • rueda = radio — x:<span x-text="cx"></span> y:<span
                x-text="cy"></span> r:<span x-text="r"></span></div>
    </template>
    <template x-if="mode==='full'">
        <div class="text-xs text-gray-500">Orden: Cadera → Rodilla → Tobillo → Barra — H:<span
                x-text="hx"></span>,<span x-text="hy"></span> K:<span x-text="kx"></span>,<span
                x-text="ky"></span> A:<span x-text="ax"></span>,<span x-text="ay"></span> C:<span
                x-text="cx"></span>,<span x-text="cy"></span> r:<span x-text="r"></span></div>
    </template>

    {{-- inputs ocultos que llenan el modal de Filament --}}
    <input type="hidden" x-model="hx" name="hx"><input type="hidden" x-model="hy" name="hy">
    <input type="hidden" x-model="kx" name="kx"><input type="hidden" x-model="ky" name="ky">
    <input type="hidden" x-model="ax" name="ax"><input type="hidden" x-model="ay" name="ay">
    <input type="hidden" x-model="cx" name="cx"><input type="hidden" x-model="cy" name="cy">
    <input type="hidden" x-model="r" name="r">
</div>

<script>
    function picker(src, mode) {
        return {
            src,
            mode,
            r: 18,
            step: 2,
            hx: '',
            hy: '',
            kx: '',
            ky: '',
            ax: '',
            ay: '',
            cx: '',
            cy: '',
            init() {
                this.$refs.img.addEventListener('click', (e) => {
                    const b = this.$refs.img.getBoundingClientRect();
                    const x = Math.round(e.clientX - b.left),
                        y = Math.round(e.clientY - b.top);
                    if (this.mode === 'bar') {
                        this.cx = x;
                        this.cy = y;
                    } else {
                        if (!this.hx) {
                            this.hx = x;
                            this.hy = y;
                        } else if (!this.kx) {
                            this.kx = x;
                            this.ky = y;
                        } else if (!this.ax) {
                            this.ax = x;
                            this.ay = y;
                        } else {
                            this.cx = x;
                            this.cy = y;
                        }
                    }
                });
                this.$refs.img.addEventListener('wheel', (ev) => {
                    ev.preventDefault();
                    this.r = Math.max(3, this.r + (ev.deltaY < 0 ? this.step : -this.step));
                }, {
                    passive: false
                });
            }
        }
    }
</script>
