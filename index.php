<?php
// Cargar las clases necesarias al inicio
require_once('lib/FuzzySystem.php');
require_once('lib/FuzzyVariable.php');
require_once('lib/FuzzySet.php');
require_once('lib/FuzzyRule.php');
require_once('lib/FuzzyController.php');

// Procesar la solicitud si es un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener valores de entrada
    $temperatura = isset($_POST['temperatura']) ? floatval($_POST['temperatura']) : 25;
    
    // Crear instancia del controlador difuso
    $controller = new FuzzyController();
    
    // Ejecutar el controlador y obtener resultados
    $resultados = $controller->procesar($temperatura);
    
    // Verificar si es una solicitud AJAX
    if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
        // Asegurar que los headers estén limpios
        if (!headers_sent()) {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }
        
        // Devolver los resultados como JSON para AJAX
        echo json_encode($resultados);
        exit;
    }
    
    // No es necesario hacer nada más si no es AJAX, los resultados se mostrarán en la página
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Control Difuso de Ventilador</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Control Difuso para Ventilador</h1>
        </header>

        <main>
            <section class="input-section">
                <h2>Parámetros de Entrada</h2>
                <form id="fuzzyForm" onsubmit="return false;">
                    <div class="form-group">
                        <label for="temperatura">Temperatura Ambiente (°C):</label>
                        <input type="range" id="temperatura" name="temperatura" min="0" max="40" value="25" oninput="updateTemperaturaYCalcular(this.value)">
                        <span id="temperaturaValue">25 °C</span>
                    </div>
                </form>
            </section>

            <section class="output-section">
                <h2>Resultados del Control Difuso</h2>
                <div class="results">
                    <div class="result-item">
                        <h3>Velocidad del Ventilador:</h3>
                        <div class="meter">
                            <div id="velocidadBar" class="bar fan" style="width: 50%;">1500 RPM (50%)</div>
                        </div>
                    </div>
                    <div class="fan-display">
                        <div class="fan-icon" id="fan-animation">
                            <i class="fan-blade"></i>
                            <i class="fan-blade"></i>
                            <i class="fan-blade"></i>
                            <i class="fan-blade"></i>
                        </div>
                    </div>
                </div>
            </section>

            <section class="charts-section">
                <h2>Visualización Difusa</h2>
                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h3>Membresía de Temperatura</h3>
                        <canvas id="temperaturaChart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <h3>Membresía de Velocidad</h3>
                        <canvas id="velocidadChart"></canvas>
                    </div>
                </div>
            </section>
            
            <section class="doc-link">
                <p><a href="ayuda.php">Ver documentación del sistema →</a></p>
            </section>
        </main>

        <footer>
            <p>Sistema de Control Difuso para Ventilador &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>
    
    <!-- Asegurarse de que los scripts se carguen en el orden correcto -->
    <script src="js/charts.js"></script>
    <script src="js/fuzzy.js"></script>
</body>
</html>