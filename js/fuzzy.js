/**
 * Script para manejar la lógica del cliente del sistema difuso de calidad del aire
 */

// Variable para controlar el debounce
let debounceTimer;

// Actualizar el valor mostrado y calcular
function updateValorYCalcular(parametro, valor) {
    document.getElementById(parametro + 'Value').textContent = valor + ' PPM';

    // Usar debounce para evitar demasiadas solicitudes
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        calcularControl();
    }, 100); // Pequeño delay para mejor rendimiento
}

// Función para cambiar el tipo de función de membresía
function cambiarTipoFuncion(tipoFuncion) {
    console.log('Cambiando tipo de función a:', tipoFuncion);

    // Actualizar los gráficos con el nuevo tipo de función
    if (typeof actualizarGraficosPorTipoFuncion === 'function') {
        actualizarGraficosPorTipoFuncion(tipoFuncion);
    }

    // Calcular nuevos resultados
    calcularControl();
}

// Función para calcular y mostrar los resultados del control difuso
function calcularControl() {
    const co2 = document.getElementById('co2').value;
    const h2 = document.getElementById('h2').value;
    const co = document.getElementById('co').value;
    const o2 = document.getElementById('o2').value;
    const tipoFuncion = document.getElementById('tipoFuncion').value;

    console.log('Calculando para: CO2:', co2, 'H2:', h2, 'CO:', co, 'O2:', o2, 'Tipo de función:', tipoFuncion);

    // Crear un formulario para enviar los datos mediante AJAX
    const formData = new FormData();
    formData.append('co2', co2);
    formData.append('h2', h2);
    formData.append('co', co);
    formData.append('o2', o2);
    formData.append('tipoFuncion', tipoFuncion);
    formData.append('ajax', 'true'); // Indicar que es una solicitud AJAX

    // Agregar un timestamp para evitar caché
    const timestamp = new Date().getTime();

    // Enviar la solicitud AJAX
    fetch('index.php?t=' + timestamp, {
        method: 'POST',
        body: formData,
        cache: 'no-store'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos del servidor:', data);

            // Actualizar directamente los resultados con los valores recibidos
            if (data.indice_calidad !== undefined && data.porcentaje_calidad !== undefined) {
                const indiceCalidad = data.indice_calidad;
                const porcentajeCalidad = data.porcentaje_calidad;
                const categoriaCalidad = data.categoria_calidad;

                console.log('Actualizando UI - Calidad:', indiceCalidad, '(', porcentajeCalidad, '%)', categoriaCalidad);

                // Actualizar la barra de calidad
                const calidadBar = document.getElementById('calidadBar');

                if (calidadBar) {
                    calidadBar.style.width = porcentajeCalidad + '%';
                    calidadBar.textContent = indiceCalidad + ' (' + porcentajeCalidad + '%)';

                    // Aplicar color según categoría
                    calidadBar.className = 'bar air-quality ' + obtenerClaseCalidad(indiceCalidad);

                    // Actualizar texto de la categoría
                    document.getElementById('calidad-texto').textContent = categoriaCalidad;

                    // Actualizar icono de calidad
                    actualizarIconoCalidad(indiceCalidad);

                    // Actualizar los gráficos con los nuevos valores
                    actualizarGraficos(co2, h2, co, o2, indiceCalidad);
                } else {
                    console.error('No se encontró el elemento para la barra de calidad');
                }
            } else {
                console.error('Respuesta incorrecta del servidor:', data);
                // Procesar manualmente si no se reciben los datos esperados
                procesarResultadosManualmente(co2, h2, co, o2);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud AJAX:', error);

            // Procesar manualmente en caso de error
            procesarResultadosManualmente(co2, h2, co, o2);
        });
}

// Función para obtener la clase CSS según el índice de calidad
function obtenerClaseCalidad(indice) {
    if (indice <= 100) {
        return 'quality-optimal';
    } else if (indice <= 200) {
        return 'quality-good';
    } else if (indice <= 300) {
        return 'quality-regular';
    } else if (indice <= 400) {
        return 'quality-poor';
    } else {
        return 'quality-harmful';
    }
}

// Función para actualizar el icono de calidad del aire
function actualizarIconoCalidad(indice) {
    const icono = document.getElementById('calidad-icono');

    if (!icono) return;

    // Limpiar clases anteriores
    icono.className = 'indicator-icon';

    // Agregar clase según el índice
    if (indice <= 100) {
        icono.classList.add('quality-optimal-icon');
    } else if (indice <= 200) {
        icono.classList.add('quality-good-icon');
    } else if (indice <= 300) {
        icono.classList.add('quality-regular-icon');
    } else if (indice <= 400) {
        icono.classList.add('quality-poor-icon');
    } else {
        icono.classList.add('quality-harmful-icon');
    }
}

// Función para procesar los resultados manualmente (simulación en cliente)
function procesarResultadosManualmente(co2, h2, co, o2) {
    // Normalizar los valores a una escala 0-1 donde 1 es peor
    const co2Norm = (co2 - 900) / (1046 - 900);
    const h2Norm = (h2 - 500) / (540 - 500);
    const coNorm = (co - 490) / (520 - 490);
    const o2Norm = 1 - ((o2 - 4) / (8 - 4)); // Invertido porque más O2 es mejor

    // Ponderación de cada gas (puede ajustarse según la importancia relativa)
    const co2Peso = 0.35; // CO2 es muy importante
    const h2Peso = 0.20;
    const coPeso = 0.25;
    const o2Peso = 0.20;

    // Calcular un índice ponderado (0-1)
    const indicePonderado = (co2Norm * co2Peso) +
        (h2Norm * h2Peso) +
        (coNorm * coPeso) +
        (o2Norm * o2Peso);

    // Convertir a la escala 0-500 donde 500 es peor
    const indiceCalidad = Math.round(indicePonderado * 500);
    const porcentajeCalidad = Math.round((indiceCalidad / 500) * 100);

    // Determinar categoría
    let categoriaCalidad;
    if (indiceCalidad <= 100) {
        categoriaCalidad = 'Óptimo';
    } else if (indiceCalidad <= 200) {
        categoriaCalidad = 'Medio Bueno';
    } else if (indiceCalidad <= 300) {
        categoriaCalidad = 'Regular';
    } else if (indiceCalidad <= 400) {
        categoriaCalidad = 'Medio Nocivo';
    } else {
        categoriaCalidad = 'Nocivo';
    }

    console.log('Proceso manual - Calidad:', indiceCalidad, '(', porcentajeCalidad, '%)', categoriaCalidad);

    // Actualizar la interfaz
    const calidadBar = document.getElementById('calidadBar');

    if (calidadBar) {
        calidadBar.style.width = porcentajeCalidad + '%';
        calidadBar.textContent = indiceCalidad + ' (' + porcentajeCalidad + '%)';

        // Aplicar color según categoría
        calidadBar.className = 'bar air-quality ' + obtenerClaseCalidad(indiceCalidad);

        // Actualizar texto de la categoría
        document.getElementById('calidad-texto').textContent = categoriaCalidad;

        // Actualizar icono de calidad
        actualizarIconoCalidad(indiceCalidad);
    }

    // Actualizar gráficos
    actualizarGraficos(co2, h2, co, o2, indiceCalidad);
}

// Función para actualizar los gráficos con los valores actuales
function actualizarGraficos(co2, h2, co, o2, indiceCalidad) {
    console.log('Actualizando gráficos con:',
        'CO2:', co2,
        'H2:', h2,
        'CO:', co,
        'O2:', o2,
        'Índice:', indiceCalidad);

    if (typeof actualizarGraficoCO2 === 'function') {
        actualizarGraficoCO2(co2);
        actualizarGraficoH2(h2);
        actualizarGraficoCO(co);
        actualizarGraficoO2(o2);
        actualizarGraficoCalidad(indiceCalidad);
    } else {
        console.warn('Las funciones de actualización de gráficos no están disponibles');
    }
}

// Inicializar cuando se cargue la página
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM completamente cargado');

    // Inicializar los valores mostrados
    document.getElementById('co2Value').textContent = document.getElementById('co2').value + ' PPM';
    document.getElementById('h2Value').textContent = document.getElementById('h2').value + ' PPM';
    document.getElementById('coValue').textContent = document.getElementById('co').value + ' PPM';
    document.getElementById('o2Value').textContent = document.getElementById('o2').value + ' PPM';

    // Calcular una vez al inicio para tener resultados actualizados
    setTimeout(calcularControl, 1000);
});