/**
 * Script para manejar la lógica del cliente del sistema difuso
 */

// Variable para controlar el debounce
let debounceTimer;
// Variable para la animación del ventilador
let fanAnimationInterval;

// Actualizar el valor mostrado de temperatura y calcular
function updateTemperaturaYCalcular(value) {
    document.getElementById('temperaturaValue').textContent = value + ' °C';
    
    // Usar debounce para evitar demasiadas solicitudes
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        calcularControl();
    }, 100); // Pequeño delay para mejor rendimiento
}

// Mantener esta función para compatibilidad
function updateTemperatura(value) {
    document.getElementById('temperaturaValue').textContent = value + ' °C';
}

// Función para calcular y mostrar los resultados del control difuso
function calcularControl() {
    const temperatura = document.getElementById('temperatura').value;
    
    console.log('Calculando para Temperatura:', temperatura, '°C');
    
    // Crear un formulario para enviar los datos mediante AJAX
    const formData = new FormData();
    formData.append('temperatura', temperatura);
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
        if (data.velocidad_rpm !== undefined && data.velocidad_porcentaje !== undefined) {
            const velocidadRPM = data.velocidad_rpm;
            const velocidadPorcentaje = data.velocidad_porcentaje;
            
            console.log('Actualizando UI - Velocidad:', velocidadRPM, 'RPM (', velocidadPorcentaje, '%)');
            
            // Actualizar los elementos del DOM directamente
            const velocidadBar = document.getElementById('velocidadBar');
            
            if (velocidadBar) {
                velocidadBar.style.width = velocidadPorcentaje + '%';
                velocidadBar.textContent = velocidadRPM + ' RPM (' + velocidadPorcentaje + '%)';
                
                // Actualizar la animación del ventilador
                actualizarAnimacionVentilador(velocidadRPM);
                
                // Actualizar los gráficos con los nuevos valores
                actualizarGraficos(temperatura, velocidadRPM);
            } else {
                console.error('No se encontró el elemento para la barra de velocidad');
            }
        } else {
            console.error('Respuesta incorrecta del servidor:', data);
            // Procesar manualmente si no se reciben los datos esperados
            procesarResultadosManualmente(temperatura);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        
        // Procesar manualmente en caso de error
        procesarResultadosManualmente(temperatura);
    });
}

// Función para procesar los resultados manualmente (simulación en cliente)
function procesarResultadosManualmente(temperatura) {
    // Valores predeterminados según la temperatura, coincidiendo con la lógica del backend
    let velocidadRPM;
    
    // Para temperaturas extremas, calcular directamente
    if (temperatura <= 5) {
        // Para temperaturas muy bajas (0-5°C)
        velocidadRPM = temperatura * 40; // 0-200 RPM
    } else if (temperatura >= 35) {
        // Para temperaturas muy altas (35-40°C)
        velocidadRPM = 2500 + ((temperatura - 35) / 5) * 500; // 2500-3000 RPM
    } else {
        // Para temperaturas intermedias, usar una función lineal suave
        velocidadRPM = Math.round((temperatura / 40) * 3000);
        
        // Ajuste para que coincida mejor con las funciones de membresía
        if (temperatura < 15) {
            velocidadRPM = Math.round(temperatura * 50); // 0-750 RPM para 0-15°C
        } else if (temperatura < 25) {
            velocidadRPM = 750 + ((temperatura - 15) / 10) * 1000; // 750-1750 RPM para 15-25°C
        } else {
            velocidadRPM = 1750 + ((temperatura - 25) / 15) * 1250; // 1750-3000 RPM para 25-40°C
        }
    }
    
    // Asegurar que la velocidad esté en el rango correcto
    velocidadRPM = Math.max(0, Math.min(3000, Math.round(velocidadRPM)));
    const velocidadPorcentaje = Math.round((velocidadRPM / 3000) * 100);
    
    console.log('Proceso manual - Velocidad:', velocidadRPM, 'RPM (', velocidadPorcentaje, '%)');
    
    // Actualizar la interfaz
    const velocidadBar = document.getElementById('velocidadBar');
    
    if (velocidadBar) {
        velocidadBar.style.width = velocidadPorcentaje + '%';
        velocidadBar.textContent = velocidadRPM + ' RPM (' + velocidadPorcentaje + '%)';
        
        // Actualizar la animación del ventilador
        actualizarAnimacionVentilador(velocidadRPM);
    }
    
    // Actualizar gráficos
    actualizarGraficos(temperatura, velocidadRPM);
}

// Función para actualizar la animación del ventilador según la velocidad
function actualizarAnimacionVentilador(velocidadRPM) {
    const fanElement = document.getElementById('fan-animation');
    
    if (!fanElement) return;
    
    // Detener la animación actual si existe
    if (fanAnimationInterval) {
        clearInterval(fanAnimationInterval);
        fanAnimationInterval = null;
        fanElement.style.animation = 'none';
    }
    
    // No animar si la velocidad es 0
    if (velocidadRPM === 0) {
        return;
    }
    
    // Calcular la duración de la animación basada en RPM
    // Menor RPM = animación más lenta
    const duration = 3000 / velocidadRPM; // en segundos
    
    // Aplicar la animación
    fanElement.style.animation = `spin ${duration}s linear infinite`;
}

// Inicializar cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado');
    
    // Inicializar valores
    updateTemperatura(document.getElementById('temperatura').value);
    
    // Inicializar gráficos
    inicializarGraficos();
    
    // Calcular una vez al inicio para tener resultados actualizados
    setTimeout(calcularControl, 1000);
});

// Función para inicializar los gráficos
function inicializarGraficos() {
    console.log('Inicializando gráficos');
    
    // Esta función se complementa con charts.js
    if (typeof crearGraficoTemperatura === 'function') {
        crearGraficoTemperatura();
        crearGraficoVelocidad();
    } else {
        console.warn('Las funciones de gráficos no están disponibles. Verifica que charts.js esté cargado correctamente.');
    }
}

// Función para actualizar los gráficos con los valores actuales
function actualizarGraficos(temperatura, velocidadRPM) {
    console.log('Actualizando gráficos con:', 
                'Temperatura:', temperatura, 
                'Velocidad:', velocidadRPM);
    
    if (typeof actualizarGraficoTemperatura === 'function') {
        actualizarGraficoTemperatura(temperatura);
        
        if (typeof actualizarGraficoVelocidad === 'function') {
            actualizarGraficoVelocidad(velocidadRPM);
        } else {
            console.warn('La función actualizarGraficoVelocidad no está disponible');
        }
    } else {
        console.warn('Las funciones de actualización de gráficos no están disponibles');
    }
}