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
    $co2 = isset($_POST['co2']) ? floatval($_POST['co2']) : 950;
    $h2 = isset($_POST['h2']) ? floatval($_POST['h2']) : 520;
    $co = isset($_POST['co']) ? floatval($_POST['co']) : 500;
    $o2 = isset($_POST['o2']) ? floatval($_POST['o2']) : 6;
    $tipoFuncion = isset($_POST['tipoFuncion']) ? $_POST['tipoFuncion'] : 'triangular';
    
    // Crear instancia del controlador difuso
    $controller = new FuzzyController($tipoFuncion);
    
    // Ejecutar el controlador y obtener resultados
    $resultados = $controller->procesar($co2, $h2, $co, $o2);
    
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
    <title>Control Difuso de Calidad del Aire</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Control Difuso para Calidad del Aire</h1>
        </header>

        <main>
            <section class="input-section">
                <h2>Parámetros de Entrada</h2>
                <form id="fuzzyForm" onsubmit="return false;">
                    <div class="form-group">
                        <label for="co2">CO2 (PPM):</label>
                        <input type="range" id="co2" name="co2" min="900" max="1046" value="950" oninput="updateValorYCalcular('co2', this.value)">
                        <span id="co2Value">950 PPM</span>
                    </div>
                    <div class="form-group">
                        <label for="h2">H2 (PPM):</label>
                        <input type="range" id="h2" name="h2" min="500" max="540" value="520" oninput="updateValorYCalcular('h2', this.value)">
                        <span id="h2Value">520 PPM</span>
                    </div>
                    <div class="form-group">
                        <label for="co">CO (PPM):</label>
                        <input type="range" id="co" name="co" min="490" max="520" value="500" oninput="updateValorYCalcular('co', this.value)">
                        <span id="coValue">500 PPM</span>
                    </div>
                    <div class="form-group">
                        <label for="o2">O2 (PPM):</label>
                        <input type="range" id="o2" name="o2" min="4" max="8" value="6" oninput="updateValorYCalcular('o2', this.value)">
                        <span id="o2Value">6 PPM</span>
                    </div>
                    <div class="form-group">
                        <label for="tipoFuncion">Tipo de Función de Membresía:</label>
                        <select id="tipoFuncion" name="tipoFuncion" onchange="cambiarTipoFuncion(this.value)">
                            <option value="triangular">Triangular</option>
                            <option value="trapezoidal">Trapezoidal</option>
                            <option value="gaussiano">Gaussiano</option>
                        </select>
                    </div>
                </form>
            </section>

            <section class="output-section">
                <h2>Resultados del Control Difuso</h2>
                <div class="results">
                    <div class="result-item">
                        <h3>Índice de Calidad del Aire:</h3>
                        <div class="meter">
                            <div id="calidadBar" class="bar air-quality" style="width: 50%;">250 (50%)</div>
                        </div>
                    </div>
                    <div class="quality-indicator">
                        <div class="indicator-label" id="calidad-texto">Regular</div>
                        <div class="indicator-icon" id="calidad-icono">
                            <i class="quality-icon"></i>
                        </div>
                    </div>
                </div>
            </section>

            <section class="charts-section">
                <h2>Visualización Difusa</h2>
                <div class="charts-container">
                    <div class="chart-wrapper">
                        <h3>Membresía de CO2</h3>
                        <canvas id="co2Chart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <h3>Membresía de H2</h3>
                        <canvas id="h2Chart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <h3>Membresía de CO</h3>
                        <canvas id="coChart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <h3>Membresía de O2</h3>
                        <canvas id="o2Chart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <h3>Membresía de Calidad del Aire</h3>
                        <canvas id="calidadChart"></canvas>
                    </div>
                </div>
            </section>
            
        </main>

        <footer>
            <p>Sistema de Control Difuso para Calidad del Aire &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>
    
    <!-- Asegurarse de que los scripts se carguen en el orden correcto -->
    <script src="js/charts.js"></script>
    <script src="js/fuzzy.js"></script>
</body>
</html>