document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar números automáticamente
    function actualizarNumeracion() {
        console.log('Actualizando numeración...');

        // Actualizar números de serie
        document.querySelectorAll('[data-field-wrapper-name="series"]').forEach(seriesRepeater => {
            const items = seriesRepeater.querySelectorAll('[data-repeater-item]');
            items.forEach((item, index) => {
                const numeroSerieInput = item.querySelector('input[name$="[numero_serie]"]');
                if (numeroSerieInput) {
                    numeroSerieInput.value = index + 1;
                    // Actualizar el label del item
                    const itemLabel = item.querySelector('[data-repeater-item-label]');
                    if (itemLabel) {
                        itemLabel.textContent = 'Serie ' + (index + 1);
                    }
                }
            });
        });

        // Actualizar números de ejercicio
        document.querySelectorAll('[data-field-wrapper-name="ejercicios"]').forEach(ejerciciosRepeater => {
            const items = ejerciciosRepeater.querySelectorAll('[data-repeater-item]');
            items.forEach((item, index) => {
                const ordenInput = item.querySelector('input[name$="[orden]"]');
                if (ordenInput) {
                    ordenInput.value = index + 1;
                    // Actualizar el label del item
                    const itemLabel = item.querySelector('[data-repeater-item-label]');
                    if (itemLabel) {
                        const ejercicioSelect = item.querySelector('select[name$="[ejercicio_id]"]');
                        const ejercicioNombre = ejercicioSelect ? ejercicioSelect.options[ejercicioSelect.selectedIndex]?.text : '';
                        itemLabel.textContent = 'Ejercicio ' + (index + 1) + (ejercicioNombre ? ' - ' + ejercicioNombre : '');
                    }
                }
            });
        });

        // Actualizar labels de días
        document.querySelectorAll('[data-field-wrapper-name="dias"]').forEach(diasRepeater => {
            const items = diasRepeater.querySelectorAll('[data-repeater-item]');
            items.forEach((item, index) => {
                const diaSelect = item.querySelector('select[name$="[dia]"]');
                if (diaSelect) {
                    const diaNombre = diaSelect.options[diaSelect.selectedIndex]?.text || 'Seleccionar día';
                    const itemLabel = item.querySelector('[data-repeater-item-label]');
                    if (itemLabel) {
                        itemLabel.textContent = 'Día ' + (index + 1) + ' - ' + diaNombre;
                    }
                }
            });
        });

        // Actualizar labels de semanas
        document.querySelectorAll('[data-field-wrapper-name="semanas"]').forEach(semanasRepeater => {
            const items = semanasRepeater.querySelectorAll('[data-repeater-item]');
            items.forEach((item, index) => {
                const semanaSelect = item.querySelector('select[name$="[numero_semana]"]');
                if (semanaSelect) {
                    const semanaNumero = semanaSelect.value || (index + 1);
                    const itemLabel = item.querySelector('[data-repeater-item-label]');
                    if (itemLabel) {
                        itemLabel.textContent = 'Semana ' + semanaNumero;
                    }
                }
            });
        });
    }

    // Actualizar al cargar la página
    setTimeout(actualizarNumeracion, 500);

    // Actualizar cuando se agregan nuevos items
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-repeater-create-button]')) {
            setTimeout(actualizarNumeracion, 300);
        }
    });

    // Actualizar cuando se eliminan items
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-repeater-delete-button]')) {
            setTimeout(actualizarNumeracion, 300);
        }
    });

    // Actualizar cuando cambian los selects
    document.addEventListener('change', function(e) {
        if (e.target.matches('select[name$="[numero_semana]"], select[name$="[dia]"], select[name$="[ejercicio_id]"]')) {
            setTimeout(actualizarNumeracion, 100);
        }
    });

    // Actualizar cuando cambian los inputs
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name$="[orden]"]')) {
            setTimeout(actualizarNumeracion, 100);
        }
    });
});
