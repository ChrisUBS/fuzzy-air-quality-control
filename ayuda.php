<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación - Control Difuso de Ventilador</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos adicionales para la documentación */
        .doc-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .doc-section {
            margin-bottom: 40px;
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .doc-image {
            max-width: 100%;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        pre.code-block {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            overflow-x: auto;
            margin: 15px 0;
            white-space: pre;
            line-height: 1.5;
            color: #333;
            font-size: 14px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #f0f0f0;
        }
        
        .nav-link {
            display: inline-block;
            margin: 10px 0;
            color: #0077b6;
            text-decoration: none;
            font-weight: bold;
        }
        
        .nav-link:hover {
            text-decoration: underline;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #0077b6;
            color: white;
            border-radius: 8px;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="doc-container">
        <header>
            <h1>Documentación del Sistema de Control Difuso para Ventilador</h1>
            <p><a href="index.php" class="nav-link" style="color: white;">← Volver al Sistema</a></p>
        </header>

        <main>
            <section class="doc-section">
                <h2>Introducción</h2>
                <p>Este sistema utiliza lógica difusa (fuzzy logic) para controlar la velocidad de un ventilador basándose en la temperatura ambiente. A diferencia de los sistemas de control convencionales que operan con valores exactos, la lógica difusa permite manejar la incertidumbre y trabajar con conceptos lingüísticos como "frío", "templado" o "caliente".</p>
                <p>El sistema toma como entrada la temperatura ambiente y determina como salida la velocidad del ventilador para mantener condiciones de confort.</p>
            </section>
            
            <section class="doc-section">
                <h2>Componentes del Sistema</h2>
                <h3>Variables de Entrada</h3>
                <ul>
                    <li><strong>Temperatura Ambiente (°C):</strong> Rango de 0 a 40 grados Celsius</li>
                </ul>
                
                <h3>Variables de Salida</h3>
                <ul>
                    <li><strong>Velocidad del Ventilador (RPM):</strong> Rango de 0 a 3000 revoluciones por minuto</li>
                </ul>
                
                <h3>Conjuntos Difusos</h3>
                <h4>Temperatura:</h4>
                <ul>
                    <li>Baja (fría): 0-20°C (máximo en 0°C)</li>
                    <li>Media (templada): 15-30°C (máximo en 25°C)</li>
                    <li>Alta (caliente): 25-40°C (máximo en 40°C)</li>
                </ul>
                
                <h4>Velocidad del Ventilador:</h4>
                <ul>
                    <li>Lenta: 0-500 RPM (máximo en 0 RPM)</li>
                    <li>Media: 300-1500 RPM (máximo en 1000 RPM)</li>
                    <li>Rápida: 1200-3000 RPM (máximo en 3000 RPM)</li>
                </ul>
            </section>
            
            <section class="doc-section">
                <h2>Reglas Difusas</h2>
                <p>El sistema utiliza las siguientes reglas para determinar la velocidad del ventilador:</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Antecedente</th>
                            <th>Consecuente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Si Temperatura es Baja</td>
                            <td>Velocidad del ventilador es Lenta</td>
                        </tr>
                        <tr>
                            <td>Si Temperatura es Media</td>
                            <td>Velocidad del ventilador es Media</td>
                        </tr>
                        <tr>
                            <td>Si Temperatura es Alta</td>
                            <td>Velocidad del ventilador es Rápida</td>
                        </tr>
                    </tbody>
                </table>
            </section>
            
            <section class="doc-section">
                <h2>Funcionamiento del Sistema</h2>
                <ol>
                    <li><strong>Fusificación:</strong> La entrada de temperatura se convierte en grados de pertenencia a los conjuntos difusos correspondientes.</li>
                    <li><strong>Evaluación de reglas:</strong> Se aplican las reglas difusas para determinar los grados de activación de los conjuntos de salida.</li>
                    <li><strong>Defusificación:</strong> Se convierten los conjuntos difusos de salida en un valor concreto mediante el método del centroide.</li>
                </ol>
                
                <h3>Ejemplo</h3>
                <p>Si la temperatura es 28°C (pertenece parcialmente a "Media" y "Alta"), el sistema evaluará las reglas aplicables y combinará los resultados para obtener una velocidad del ventilador aproximada de 2000 RPM.</p>
            </section>
            
            <section class="doc-section">
                <h2>Estructura del Proyecto</h2>
                <p>El proyecto está organizado de la siguiente manera:</p>
                
                <pre class="code-block">fuzzy-fan-control/
│
├── index.php                  # Página principal y formulario de entrada
├── ayuda.php                  # Esta página de documentación
├── css/
│   └── style.css              # Estilos para la interfaz
│
├── js/
│   ├── fuzzy.js               # Implementación de lógica difusa en cliente
│   └── charts.js              # Código para generar gráficos
│
├── lib/
│   ├── FuzzySystem.php        # Clase principal del sistema difuso
│   ├── FuzzyVariable.php      # Clase para variables difusas
│   ├── FuzzySet.php           # Clase para conjuntos difusos
│   ├── FuzzyRule.php          # Clase para reglas difusas
│   └── FuzzyController.php    # Controlador principal</pre>
            </section>
            
            <section class="doc-section">
                <h2>Uso del Sistema</h2>
                <ol>
                    <li>Ajuste el control deslizante para establecer la temperatura ambiente deseada.</li>
                    <li>El sistema calculará automáticamente la velocidad adecuada del ventilador.</li>
                    <li>Los resultados se mostrarán en forma de barra de progreso y en la animación del ventilador.</li>
                    <li>Los gráficos mostrarán las funciones de membresía y los valores actuales.</li>
                </ol>
            </section>
            
            <section class="doc-section">
                <h2>Referencias</h2>
                <ul>
                    <li>Zadeh, L.A. (1965). "Fuzzy sets". Information and Control, 8(3), 338-353.</li>
                    <li>Mamdani, E.H. & Assilian, S. (1975). "An experiment in linguistic synthesis with a fuzzy logic controller". International Journal of Man-Machine Studies, 7(1), 1-13.</li>
                    <li>Cox, E. (1994). "The fuzzy systems handbook: A practitioner's guide to building, using, and maintaining fuzzy systems". Academic Press.</li>
                </ul>
            </section>
        </main>

        <footer>
            <p><a href="index.php" class="nav-link">← Volver al Sistema</a></p>
            <p>Sistema de Control Difuso para Ventilador &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>
</body>
</html>