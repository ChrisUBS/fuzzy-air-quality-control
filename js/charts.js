/**
 * Script para manejar las gráficas del sistema difuso del ventilador
 */

// Variables globales para los charts
let temperaturaChart = null;
let velocidadChart = null;

// Función para crear el gráfico de temperatura
function crearGraficoTemperatura() {
    console.log('Creando gráfico de temperatura');
    const ctx = document.getElementById('temperaturaChart');
    
    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de temperatura');
        return;
    }
    
    temperaturaChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Baja',
                    data: generarDatosTriangular(0, 0, 20, 0, 40, 100),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Media',
                    data: generarDatosTriangular(15, 25, 30, 0, 40, 100),
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Alta',
                    data: generarDatosTriangular(25, 40, 40, 0, 40, 100),
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
                    min: 0,
                    max: 40,
                    title: {
                        display: true,
                        text: 'Temperatura (°C)'
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
    const temperatura = document.getElementById('temperatura').value;
    actualizarGraficoTemperatura(temperatura);
}

// Función para crear el gráfico de velocidad
function crearGraficoVelocidad() {
    console.log('Creando gráfico de velocidad');
    const ctx = document.getElementById('velocidadChart');
    
    if (!ctx) {
        console.error('No se encontró el canvas para el gráfico de velocidad');
        return;
    }
    
    velocidadChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Lenta',
                    data: generarDatosTriangular(0, 0, 500, 0, 3000, 100),
                    borderColor: '#9b59b6',
                    backgroundColor: 'rgba(155, 89, 182, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Media',
                    data: generarDatosTriangular(300, 1000, 1500, 0, 3000, 100),
                    borderColor: '#1abc9c',
                    backgroundColor: 'rgba(26, 188, 156, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0
                },
                {
                    label: 'Rápida',
                    data: generarDatosTriangular(1000, 3000, 3000, 0, 3000, 100),
                    borderColor: '#d35400',
                    backgroundColor: 'rgba(211, 84, 0, 0.1)',
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
                    max: 3000,
                    title: {
                        display: true,
                        text: 'Velocidad (RPM)'
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
    actualizarGraficoVelocidad(1000);
}

// Función para actualizar el gráfico de temperatura con el valor actual
function actualizarGraficoTemperatura(temperatura) {
    if (!temperaturaChart) {
        console.warn('temperaturaChart no está inicializado');
        return;
    }
    
    // Convertir a número para asegurar
    temperatura = parseFloat(temperatura);
    
    console.log('Actualizando gráfico de temperatura con valor:', temperatura);
    
    // Eliminar línea vertical anterior si existe
    temperaturaChart.data.datasets = temperaturaChart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );
    
    // Agregar línea vertical para el valor actual
    temperaturaChart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: temperatura, y: 0 },
            { x: temperatura, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });
    
    temperaturaChart.update();
}

// Función para actualizar el gráfico de velocidad con el valor actual
function actualizarGraficoVelocidad(velocidadRPM) {
    if (!velocidadChart) {
        console.warn('velocidadChart no está inicializado');
        return;
    }
    
    // Convertir a número para asegurar
    velocidadRPM = parseFloat(velocidadRPM);
    
    console.log('Actualizando gráfico de velocidad con valor:', velocidadRPM);
    
    // Eliminar línea vertical anterior si existe
    velocidadChart.data.datasets = velocidadChart.data.datasets.filter(
        dataset => dataset.label !== 'Valor actual'
    );
    
    // Agregar línea vertical para el valor actual
    velocidadChart.data.datasets.push({
        label: 'Valor actual',
        data: [
            { x: velocidadRPM, y: 0 },
            { x: velocidadRPM, y: 1 }
        ],
        borderColor: '#2c3e50',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 0,
        fill: false
    });
    
    velocidadChart.update();
}

// Función para generar datos para funciones de membresía triangulares
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

// Función para generar datos para funciones de membresía trapezoidales
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

// Inicializamos los gráficos cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado en charts.js');
    setTimeout(() => {
        if (document.getElementById('temperaturaChart')) {
            console.log('Inicializando todos los gráficos');
            crearGraficoTemperatura();
            crearGraficoVelocidad();
        } else {
            console.error('No se encontraron los elementos canvas para los gráficos');
        }
    }, 500);
});

// Función para probar la actualización de los gráficos
function probarActualizacionGraficos() {
    console.log('Probando actualización de gráficos');
    
    // Valores de prueba
    const temperatura = 30; // Temperatura alta
    const velocidadRPM = 2000; // Velocidad rápida
    
    // Actualizar gráficos con valores de prueba
    actualizarGraficoTemperatura(temperatura);
    actualizarGraficoVelocidad(velocidadRPM);
    
    // Actualizar la interfaz
    if (document.getElementById('velocidadBar')) {
        const velocidadPorcentaje = Math.round((velocidadRPM / 3000) * 100);
        document.getElementById('velocidadBar').style.width = velocidadPorcentaje + '%';
        document.getElementById('velocidadBar').textContent = velocidadRPM + ' RPM (' + velocidadPorcentaje + '%)';
        
        // Actualizar animación del ventilador
        if (typeof actualizarAnimacionVentilador === 'function') {
            actualizarAnimacionVentilador(velocidadRPM);
        }
    }
    
    console.log('Prueba completada');
}