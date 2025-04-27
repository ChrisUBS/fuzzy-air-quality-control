/**
 * Script para manejar las gráficas del sistema difuso de calidad del aire
 */

// Variables globales para los charts
let co2Chart = null;
let h2Chart = null;
let coChart = null;
let o2Chart = null;
let calidadChart = null;

// Función para crear el gráfico de CO2
function crearGraficoCO2(tipoFuncion) {
    console.log('Creando gráfico de CO2');
    const ctx = document.getElementById('co2Chart');

    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de CO2');
        return;
    }

    var datosBajo,datosMedio,datosAlto;

    if (tipoFuncion === 'trapezoidal') {
        datosBajo = generarDatosTrapezoidal(900, 900, 925, 940, 900, 1046, 100);
        datosMedio = generarDatosTrapezoidal(925, 950, 1025, 1040, 900, 1046, 100);
        datosAlto = generarDatosTrapezoidal(1025, 1040, 1046, 1046, 900, 1046, 100);
    }
    else if (tipoFuncion === 'gaussiano') {
        datosBajo = generarDatosGaussiano(900, 17, 900, 1046, 100);
        datosMedio = generarDatosGaussiano(950, 17, 900, 1046, 100);
        datosAlto = generarDatosGaussiano(1046, 17, 900, 1046, 100);
    }
    else {
        datosBajo = generarDatosTriangular(900, 900, 950, 900, 1046, 100);
        datosMedio = generarDatosTriangular(925, 950, 1000, 900, 1046, 100);
        datosAlto = generarDatosTriangular(975, 1046, 1100, 900, 1046, 100);
    }

    co2Chart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Bajo',
                    data: datosBajo,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Medio',
                    data: datosMedio,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Alto',
                    data: datosAlto,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: 900,
                    max: 1046,
                    title: {
                        display: true,
                        text: 'CO2 (PPM)'
                    }
                },
                y: {
                    min: 0,
                    max: 1,
                    title: {
                        display: true,
                        text: 'Grado de Membresía'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Dibujar línea vertical para el valor actual
    const co2 = document.getElementById('co2').value;
    actualizarGraficoCO2(co2);
}

// Función para crear el gráfico de H2
function crearGraficoH2(tipoFuncion) {
    console.log('Creando gráfico de H2');
    const ctx = document.getElementById('h2Chart');

    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de H2');
        return;
    }

    var datosBajo, datosMedio, datosAlto;
    if (tipoFuncion === 'trapezoidal') {
        datosBajo = generarDatosTrapezoidal(500, 500, 510, 515, 500, 540, 100);
        datosMedio = generarDatosTrapezoidal(510, 515, 525, 530, 500, 540, 100);
        datosAlto = generarDatosTrapezoidal(525, 530, 540, 540, 500, 540, 100);
    }
    else if (tipoFuncion === 'gaussiano') {
        datosBajo = generarDatosGaussiano(510, 5, 500, 540, 100);
        datosMedio = generarDatosGaussiano(520, 5, 500, 540, 100);
        datosAlto = generarDatosGaussiano(540, 5, 500, 540, 100);
    } else {
        datosBajo = generarDatosTriangular(480, 510, 520, 500, 540, 100);
        datosMedio = generarDatosTriangular(510, 520, 530, 500, 540, 100);
        datosAlto = generarDatosTriangular(525, 540, 560, 500, 540, 100);
    }

    h2Chart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Bajo',
                    data: datosBajo,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Medio',
                    data: datosMedio,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Alto',
                    data: datosAlto,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: 500,
                    max: 540,
                    title: {
                        display: true,
                        text: 'H2 (PPM)'
                    }
                },
                y: {
                    min: 0,
                    max: 1,
                    title: {
                        display: true,
                        text: 'Grado de Membresía'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Dibujar línea vertical para el valor actual
    const h2 = document.getElementById('h2').value;
    actualizarGraficoH2(h2);
}

// Función para crear el gráfico de CO
function crearGraficoCO(tipoFuncion) {
    console.log('Creando gráfico de CO');
    const ctx = document.getElementById('coChart');

    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de CO');
        return;
    }

    var datosBajo, datosMedio, datosAlto;
    if (tipoFuncion === 'trapezoidal') {
        datosBajo = generarDatosTrapezoidal(490, 490, 495, 500, 490, 520, 100);
        datosMedio = generarDatosTrapezoidal(495, 500, 505, 510, 490, 520, 100);
        datosAlto = generarDatosTrapezoidal(505, 510, 520, 520, 490, 520, 100);
    }
    else if (tipoFuncion === 'gaussiano') {
        datosBajo = generarDatosGaussiano(490, 5, 490, 520, 100);
        datosMedio = generarDatosGaussiano(510, 5, 490, 520, 100);
        datosAlto = generarDatosGaussiano(520, 5, 490, 520, 100);
    }
    else {
        datosBajo = generarDatosTriangular(470, 490, 500, 490, 520, 100);
        datosMedio = generarDatosTriangular(500, 510, 520, 490, 520, 100);
        datosAlto = generarDatosTriangular(510, 520, 540, 490, 520, 100);
    }

    coChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Bajo',
                    data: datosBajo,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Medio',
                    data: datosMedio,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Alto',
                    data: datosAlto,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: 490,
                    max: 520,
                    title: {
                        display: true,
                        text: 'CO (PPM)'
                    }
                },
                y: {
                    min: 0,
                    max: 1,
                    title: {
                        display: true,
                        text: 'Grado de Membresía'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Dibujar línea vertical para el valor actual
    const co = document.getElementById('co').value;
    actualizarGraficoCO(co);
}

// Función para crear el gráfico de O2
function crearGraficoO2(tipoFuncion) {
    console.log('Creando gráfico de O2');
    const ctx = document.getElementById('o2Chart');

    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de O2');
        return;
    }

    var datosBajo, datosMedio, datosAlto;
    if(tipoFuncion === 'trapezoidal'){
        datosBajo = generarDatosTrapezoidal(3, 4, 4.5, 5, 4, 8, 100);
        datosMedio = generarDatosTrapezoidal(5, 5.5, 6, 7, 4, 8, 100);
        datosAlto = generarDatosTrapezoidal(6.5, 7, 8, 9, 4, 8, 100);
    }
    else if(tipoFuncion === 'gaussiano'){
        datosBajo = generarDatosGaussiano(5.5, 0.5, 4, 8, 100);
        datosMedio = generarDatosGaussiano(6.5, 0.5, 4, 8, 100);
        datosAlto = generarDatosGaussiano(7.5, 0.5, 4, 8, 100);
    } else {
        datosBajo = generarDatosTriangular(3, 4, 5.5, 4, 8, 100);
        datosMedio = generarDatosTriangular(5, 6, 7, 4, 8, 100);
        datosAlto = generarDatosTriangular(6.5, 8, 9, 4, 8, 100);
    }

    o2Chart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Bajo',
                    data: datosBajo,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Medio',
                    data: datosMedio,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Alto',
                    data: datosAlto,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: 4,
                    max: 8,
                    title: {
                        display: true,
                        text: 'O2 (PPM)'
                    }
                },
                y: {
                    min: 0,
                    max: 1,
                    title: {
                        display: true,
                        text: 'Grado de Membresía'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Dibujar línea vertical para el valor actual
    const o2 = document.getElementById('o2').value;
    actualizarGraficoO2(o2);
}

// Función para crear el gráfico de calidad del aire
function crearGraficoCalidad(tipoFuncion) {
    console.log('Creando gráfico de calidad del aire');
    const ctx = document.getElementById('calidadChart');

    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de calidad del aire');
        return;
    }


    var datosOptimos, datosMedioBueno, datosRegular, datosMedioNocivo, datosNocivo;

    // Generar datos según el tipo de función seleccionado
    if (tipoFuncion === 'trapezoidal') {
        datosOptimos = generarDatosTrapezoidal(0, 0, 0, 150, 0, 500, 100);
        datosMedioBueno = generarDatosTrapezoidal(100, 166.66, 233.32, 300, 0, 500, 100);
        datosRegular = generarDatosTrapezoidal(200, 266.66, 333.32, 400, 0, 500, 100);
        datosMedioNocivo = generarDatosTrapezoidal(300, 350, 400, 450, 0, 500, 100);
        datosNocivo = generarDatosTrapezoidal(400, 450, 500, 500, 0, 500, 100);
    }
    else if (tipoFuncion === 'gaussiano') {
        datosOptimos = generarDatosGaussiano(0, 50, 0, 500, 100);
        datosMedioBueno = generarDatosGaussiano(200, 50, 0, 500, 100);
        datosRegular = generarDatosGaussiano(300, 50, 0, 500, 100);
        datosMedioNocivo = generarDatosGaussiano(400, 50, 0, 500, 100);
        datosNocivo = generarDatosGaussiano(500, 50, 0, 500, 100);
    }
    else{
        datosOptimos = generarDatosTriangular(0, 0, 150, 0, 500, 100);
        datosMedioBueno = generarDatosTriangular(100, 200, 300, 0, 500, 100);
        datosRegular = generarDatosTriangular(200, 300, 400, 0, 500, 100);
        datosMedioNocivo = generarDatosTriangular(300, 400, 450, 0, 500, 100);
        datosNocivo = generarDatosTriangular(400, 500, 500, 0, 500, 100);
    }

    calidadChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Óptimo',
                    data: datosOptimos,
                    borderColor: '#27ae60',  // Verde
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Medio Bueno',
                    data: datosMedioBueno,
                    borderColor: '#2ecc71',  // Verde claro
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Regular',
                    data: datosRegular,
                    borderColor: '#f1c40f',  // Amarillo
                    backgroundColor: 'rgba(241, 196, 15, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Medio Nocivo',
                    data: datosMedioNocivo,
                    borderColor: '#e67e22',  // Naranja
                    backgroundColor: 'rgba(230, 126, 34, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Nocivo',
                    data: datosNocivo,
                    borderColor: '#e74c3c',  // Rojo
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom',
                    min: 0,
                    max: 500,
                    title: {
                        display: true,
                        text: 'Índice de Calidad del Aire'
                    }
                },
                y: {
                    min: 0,
                    max: 1,
                    title: {
                        display: true,
                        text: 'Grado de Membresía'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            }
        }
    });

    // Inicializar con valor predeterminado
    actualizarGraficoCalidad(250);
}

// Funciones para actualizar gráficos con los valores actuales
function actualizarGraficoCO2(valor) {
    if (!co2Chart) {
        console.warn('co2Chart no está inicializado');
        return;
    }

    valor = parseFloat(valor);

    // Eliminar línea vertical anterior si existe
    co2Chart.data.datasets = co2Chart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );

    // Agregar línea vertical para el valor actual
    co2Chart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: valor, y: 0 },
            { x: valor, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });

    co2Chart.update();
}

function actualizarGraficoH2(valor) {
    if (!h2Chart) {
        console.warn('h2Chart no está inicializado');
        return;
    }

    valor = parseFloat(valor);

    // Eliminar línea vertical anterior si existe
    h2Chart.data.datasets = h2Chart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );

    // Agregar línea vertical para el valor actual
    h2Chart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: valor, y: 0 },
            { x: valor, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });

    h2Chart.update();
}

function actualizarGraficoCO(valor) {
    if (!coChart) {
        console.warn('coChart no está inicializado');
        return;
    }

    valor = parseFloat(valor);

    // Eliminar línea vertical anterior si existe
    coChart.data.datasets = coChart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );

    // Agregar línea vertical para el valor actual
    coChart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: valor, y: 0 },
            { x: valor, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });

    coChart.update();
}

function actualizarGraficoO2(valor) {
    if (!o2Chart) {
        console.warn('o2Chart no está inicializado');
        return;
    }

    valor = parseFloat(valor);

    // Eliminar línea vertical anterior si existe
    o2Chart.data.datasets = o2Chart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );

    // Agregar línea vertical para el valor actual
    o2Chart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: valor, y: 0 },
            { x: valor, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });

    o2Chart.update();
}

function actualizarGraficoCalidad(valor) {
    if (!calidadChart) {
        console.warn('calidadChart no está inicializado');
        return;
    }

    valor = parseFloat(valor);

    // Eliminar línea vertical anterior si existe
    calidadChart.data.datasets = calidadChart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );

    // Agregar línea vertical para el valor actual
    calidadChart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: valor, y: 0 },
            { x: valor, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });

    calidadChart.update();
}

// Funciones para generar datos de visualización
function generarDatosTriangular(a, b, c, min, max, puntos) {
    const datos = [];
    const paso = (max - min) / puntos;

    // Puntos antes de 'a'
    datos.push({ x: min, y: 0 });
    if (a > min) {
        datos.push({ x: a, y: 0 });
    }

    // Punto central
    datos.push({ x: b, y: 1 });

    // Puntos después de 'c'
    if (c < max) {
        datos.push({ x: c, y: 0 });
    }
    datos.push({ x: max, y: 0 });

    return datos;
}

function generarDatosTrapezoidal(a, b, c, d, min, max, puntos) {
    const datos = [];
    const paso = (max - min) / puntos;

    // Puntos antes de 'a'
    datos.push({ x: min, y: 0 });
    if (a > min) {
        datos.push({ x: a, y: 0 });
    }

    // Puntos de la meseta
    datos.push({ x: b, y: 1 });
    datos.push({ x: c, y: 1 });

    // Puntos después de 'd'
    if (d < max) {
        datos.push({ x: d, y: 0 });
    }
    datos.push({ x: max, y: 0 });

    return datos;
}

function generarDatosGaussiano(centro, desviacion, min, max, puntos) {
    const datos = [];
    const paso = (max - min) / puntos;

    for (let i = 0; i <= puntos; i++) {
        const x = min + (i * paso);
        const exponent = -Math.pow(x - centro, 2) / (2 * Math.pow(desviacion, 2));
        const y = Math.exp(exponent);

        datos.push({ x: x, y: y });
    }

    return datos;
}

// Función para actualizar los gráficos según el tipo de función seleccionado
function actualizarGraficosPorTipoFuncion(tipoFuncion) {
    console.log('Actualizando gráficos al tipo de función:', tipoFuncion);

    // Eliminar gráficos anteriores
    if (co2Chart) co2Chart.destroy();
    if (h2Chart) h2Chart.destroy();
    if (coChart) coChart.destroy();
    if (o2Chart) o2Chart.destroy();
    if (calidadChart) calidadChart.destroy();

    // Crear nuevos gráficos con el tipo de función seleccionado
    crearGraficos(tipoFuncion);
}

// Función para crear todos los gráficos
function crearGraficos(tipoFuncion) {
    crearGraficoCO2(tipoFuncion);
    crearGraficoH2(tipoFuncion);
    crearGraficoCO(tipoFuncion);
    crearGraficoO2(tipoFuncion);
    crearGraficoCalidad(tipoFuncion);
}

// Inicializamos los gráficos cuando se cargue la página
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM cargado en charts.js');
    setTimeout(() => {
        if (document.getElementById('co2Chart')) {
            console.log('Inicializando todos los gráficos');
            crearGraficos('triangular');
        } else {
            console.error('No se encontraron los elementos canvas para los gráficos');
        }
    }, 500);
});