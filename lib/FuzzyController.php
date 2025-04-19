<?php
/**
 * Controlador difuso específico para el sistema de calidad del aire
 */
class FuzzyController {
    private $sistema;
    private $tipoFuncion;
    
    /**
     * Constructor del controlador
     * @param string $tipoFuncion Tipo de función de membresía a utilizar (triangular, trapezoidal, gaussiano)
     */
    public function __construct($tipoFuncion = 'triangular') {
        $this->tipoFuncion = $tipoFuncion;
        $this->sistema = new FuzzySystem();
        $this->configurarSistema();
    }
    
    /**
     * Configura el sistema difuso con las variables y reglas para la calidad del aire
     */
    private function configurarSistema() {
        // 1. Configurar variables de entrada
        
        // Variable de CO2 (900-1046 PPM)
        $co2 = new FuzzyVariable('co2', 900, 1046);
        
        // Variable de H2 (500-540 PPM)
        $h2 = new FuzzyVariable('h2', 500, 540);
        
        // Variable de CO (490-520 PPM)
        $co = new FuzzyVariable('co', 490, 520);
        
        // Variable de O2 (4-8 PPM)
        $o2 = new FuzzyVariable('o2', 4, 8);
        
        // Configurar conjuntos difusos según el tipo de función seleccionado
        if ($this->tipoFuncion === 'triangular') {
            $this->configurarConjuntosTriangulares($co2, $h2, $co, $o2);
        } elseif ($this->tipoFuncion === 'trapezoidal') {
            $this->configurarConjuntosTrapezioidales($co2, $h2, $co, $o2);
        } elseif ($this->tipoFuncion === 'gaussiano') {
            $this->configurarConjuntosGaussianos($co2, $h2, $co, $o2);
        } else {
            // Por defecto, usar triangular
            $this->configurarConjuntosTriangulares($co2, $h2, $co, $o2);
        }
        
        // 2. Configurar variable de salida: Índice de Calidad del Aire (0-500)
        $calidad = new FuzzyVariable('calidad', 0, 500);
        
        if ($this->tipoFuncion === 'triangular') {
            $calidad->agregarConjunto(new FuzzySet('nocivo', FuzzySet::TRIANGULAR, [400, 500, 500]))
                   ->agregarConjunto(new FuzzySet('medio_nocivo', FuzzySet::TRIANGULAR, [300, 400, 450]))
                   ->agregarConjunto(new FuzzySet('regular', FuzzySet::TRIANGULAR, [200, 300, 400]))
                   ->agregarConjunto(new FuzzySet('medio_bueno', FuzzySet::TRIANGULAR, [100, 200, 300]))
                   ->agregarConjunto(new FuzzySet('optimo', FuzzySet::TRIANGULAR, [0, 0, 150]));
        } elseif ($this->tipoFuncion === 'trapezoidal') {
            $calidad->agregarConjunto(new FuzzySet('nocivo', FuzzySet::TRAPEZOIDAL, [400, 450, 500, 500]))
                   ->agregarConjunto(new FuzzySet('medio_nocivo', FuzzySet::TRAPEZOIDAL, [300, 350, 400, 450]))
                   ->agregarConjunto(new FuzzySet('regular', FuzzySet::TRAPEZOIDAL, [200, 250, 300, 350]))
                   ->agregarConjunto(new FuzzySet('medio_bueno', FuzzySet::TRAPEZOIDAL, [100, 150, 200, 250]))
                   ->agregarConjunto(new FuzzySet('optimo', FuzzySet::TRAPEZOIDAL, [0, 0, 100, 150]));
        } elseif ($this->tipoFuncion === 'gaussiano') {
            $calidad->agregarConjunto(new FuzzySet('nocivo', FuzzySet::GAUSSIANO, [450, 40]))
                   ->agregarConjunto(new FuzzySet('medio_nocivo', FuzzySet::GAUSSIANO, [375, 40]))
                   ->agregarConjunto(new FuzzySet('regular', FuzzySet::GAUSSIANO, [300, 40]))
                   ->agregarConjunto(new FuzzySet('medio_bueno', FuzzySet::GAUSSIANO, [175, 40]))
                   ->agregarConjunto(new FuzzySet('optimo', FuzzySet::GAUSSIANO, [75, 40]));
        }
        
        // 3. Agregar variables al sistema
        $this->sistema->agregarVariableEntrada($co2)
                      ->agregarVariableEntrada($h2)
                      ->agregarVariableEntrada($co)
                      ->agregarVariableEntrada($o2)
                      ->agregarVariableSalida($calidad);
        
        // 4. Configurar reglas difusas
        
        // Regla 1: Si CO2 es alto o CO es alto, entonces ICA es nocivo.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['co2' => ['alto' => ''], 'co' => ['alto' => 'OR']],
            ['calidad' => 'nocivo'],
            1.0
        ));
        
        // Regla 2: Si CO2 es medio y H2 es alto, entonces ICA es medio nocivo.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['co2' => ['medio' => ''], 'h2' => ['alto' => '']],
            ['calidad' => 'medio_nocivo'],
            1.0
        ));
        
        // Regla 3: Si CO2 es bajo y CO es bajo, entonces ICA es regular.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['co2' => ['bajo' => ''], 'co' => ['bajo' => '']],
            ['calidad' => 'regular'],
            1.0
        ));
        
        // Regla 4: Si CO2 es bajo y O2 es alto, entonces ICA es medio bueno.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['co2' => ['bajo' => ''], 'o2' => ['alto' => '']],
            ['calidad' => 'medio_bueno'],
            1.0
        ));
        
        // Regla 5: Si CO2 es bajo y CO es bajo y O2 es alto, entonces ICA es óptimo.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['co2' => ['bajo' => ''], 'co' => ['bajo' => ''], 'o2' => ['alto' => '']],
            ['calidad' => 'optimo'],
            1.0
        ));
        
        // Reglas adicionales para completar el sistema
        
        // Regla 6: Si H2 es alto y CO es alto, entonces ICA es nocivo.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['h2' => ['alto' => ''], 'co' => ['alto' => '']],
            ['calidad' => 'nocivo'],
            0.9
        ));
        
        // Regla 7: Si O2 es bajo, entonces ICA es medio nocivo.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['o2' => ['bajo' => '']],
            ['calidad' => 'medio_nocivo'],
            0.8
        ));
        
        // Regla 8: Si CO2 es medio y O2 es medio, entonces ICA es regular.
        $this->sistema->agregarRegla(new FuzzyRule(
            ['co2' => ['medio' => ''], 'o2' => ['medio' => '']],
            ['calidad' => 'regular'],
            0.7
        ));
    }
    
    /**
     * Configura los conjuntos difusos con funciones triangulares
     */
    private function configurarConjuntosTriangulares($co2, $h2, $co, $o2) {
        // CO2 (900-1046 PPM)
        $co2->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRIANGULAR, [900, 900, 950]))
            ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRIANGULAR, [925, 975, 1025]))
            ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRIANGULAR, [1000, 1046, 1046]));
        
        // H2 (500-540 PPM)
        $h2->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRIANGULAR, [500, 500, 520]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRIANGULAR, [510, 520, 530]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRIANGULAR, [525, 540, 540]));
        
        // CO (490-520 PPM)
        $co->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRIANGULAR, [490, 490, 505]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRIANGULAR, [500, 505, 510]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRIANGULAR, [505, 520, 520]));
        
        // O2 (4-8 PPM)
        $o2->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRIANGULAR, [4, 4, 5.5]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRIANGULAR, [5, 6, 7]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRIANGULAR, [6.5, 8, 8]));
    }
    
    /**
     * Configura los conjuntos difusos con funciones trapezoidales
     */
    private function configurarConjuntosTrapezioidales($co2, $h2, $co, $o2) {
        // CO2 (900-1046 PPM)
        $co2->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRAPEZOIDAL, [900, 900, 925, 950]))
            ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRAPEZOIDAL, [925, 950, 1000, 1025]))
            ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRAPEZOIDAL, [1000, 1025, 1046, 1046]));
        
        // H2 (500-540 PPM)
        $h2->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRAPEZOIDAL, [500, 500, 510, 520]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRAPEZOIDAL, [510, 515, 525, 530]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRAPEZOIDAL, [525, 530, 540, 540]));
        
        // CO (490-520 PPM)
        $co->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRAPEZOIDAL, [490, 490, 495, 505]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRAPEZOIDAL, [495, 500, 505, 510]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRAPEZOIDAL, [505, 510, 520, 520]));
        
        // O2 (4-8 PPM)
        $o2->agregarConjunto(new FuzzySet('bajo', FuzzySet::TRAPEZOIDAL, [4, 4, 4.5, 5.5]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::TRAPEZOIDAL, [5, 5.5, 6.5, 7]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::TRAPEZOIDAL, [6.5, 7, 8, 8]));
    }
    
    /**
     * Configura los conjuntos difusos con funciones gaussianas
     */
    private function configurarConjuntosGaussianos($co2, $h2, $co, $o2) {
        // CO2 (900-1046 PPM)
        $co2->agregarConjunto(new FuzzySet('bajo', FuzzySet::GAUSSIANO, [915, 15]))
            ->agregarConjunto(new FuzzySet('medio', FuzzySet::GAUSSIANO, [975, 20]))
            ->agregarConjunto(new FuzzySet('alto', FuzzySet::GAUSSIANO, [1030, 15]));
        
        // H2 (500-540 PPM)
        $h2->agregarConjunto(new FuzzySet('bajo', FuzzySet::GAUSSIANO, [505, 7]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::GAUSSIANO, [520, 7]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::GAUSSIANO, [535, 7]));
        
        // CO (490-520 PPM)
        $co->agregarConjunto(new FuzzySet('bajo', FuzzySet::GAUSSIANO, [495, 5]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::GAUSSIANO, [505, 5]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::GAUSSIANO, [515, 5]));
        
        // O2 (4-8 PPM)
        $o2->agregarConjunto(new FuzzySet('bajo', FuzzySet::GAUSSIANO, [4.5, 0.5]))
           ->agregarConjunto(new FuzzySet('medio', FuzzySet::GAUSSIANO, [6, 0.7]))
           ->agregarConjunto(new FuzzySet('alto', FuzzySet::GAUSSIANO, [7.5, 0.5]));
    }
    
    /**
     * Procesa las entradas y devuelve las salidas en formato adecuado para la interfaz
     * @param float $co2 Nivel de CO2 en PPM
     * @param float $h2 Nivel de H2 en PPM
     * @param float $co Nivel de CO en PPM
     * @param float $o2 Nivel de O2 en PPM
     * @return array Resultados formateados
     */
    public function procesar($co2, $h2, $co, $o2) {
        // Asegurar que los valores están en el rango correcto
        $co2 = max(900, min(1046, floatval($co2)));
        $h2 = max(500, min(540, floatval($h2)));
        $co = max(490, min(520, floatval($co)));
        $o2 = max(4, min(8, floatval($o2)));
        
        // Evaluar el sistema difuso
        $resultado = $this->sistema->evaluar([
            'co2' => $co2,
            'h2' => $h2,
            'co' => $co,
            'o2' => $o2
        ]);
        
        // Verificar si los resultados son válidos
        if (!isset($resultado['calidad'])) {
            // Si hay algún problema, usar valores predeterminados basados en las entradas
            return $this->calcularValoresPredeterminados($co2, $h2, $co, $o2);
        }
        
        // Obtener el valor de calidad del aire
        $indiceCalidad = round($resultado['calidad']);
        
        // Calcular porcentaje (inverso, ya que menor índice = mejor calidad)
        $porcentajeCalidad = round(($indiceCalidad / 500) * 100);
        
        // Determinar categoría textual de calidad
        $categoriaCalidad = $this->determinarCategoriaCalidad($indiceCalidad);
        
        return [
            'indice_calidad' => $indiceCalidad,
            'porcentaje_calidad' => $porcentajeCalidad,
            'categoria_calidad' => $categoriaCalidad,
            'tipo_funcion' => $this->tipoFuncion
        ];
    }
    
    /**
     * Determina la categoría textual de calidad del aire
     * @param int $indice Índice de calidad del aire
     * @return string Categoría textual
     */
    private function determinarCategoriaCalidad($indice) {
        if ($indice <= 100) {
            return 'Óptimo';
        } elseif ($indice <= 200) {
            return 'Medio Bueno';
        } elseif ($indice <= 300) {
            return 'Regular';
        } elseif ($indice <= 400) {
            return 'Medio Nocivo';
        } else {
            return 'Nocivo';
        }
    }
    
    /**
     * Calcula valores predeterminados en caso de error
     * @param float $co2 Nivel de CO2 en PPM
     * @param float $h2 Nivel de H2 en PPM
     * @param float $co Nivel de CO en PPM
     * @param float $o2 Nivel de O2 en PPM
     * @return array Resultados calculados manualmente
     */
    private function calcularValoresPredeterminados($co2, $h2, $co, $o2) {
        // Normalizar los valores a una escala 0-1 donde 1 es peor
        $co2Norm = ($co2 - 900) / (1046 - 900);
        $h2Norm = ($h2 - 500) / (540 - 500);
        $coNorm = ($co - 490) / (520 - 490);
        $o2Norm = 1 - (($o2 - 4) / (8 - 4)); // Invertido porque más O2 es mejor
        
        // Ponderación de cada gas (puede ajustarse según la importancia relativa)
        $co2Peso = 0.35; // CO2 es muy importante
        $h2Peso = 0.20;
        $coPeso = 0.25;
        $o2Peso = 0.20;
        
        // Calcular un índice ponderado (0-1)
        $indicePonderado = ($co2Norm * $co2Peso) + 
                          ($h2Norm * $h2Peso) + 
                          ($coNorm * $coPeso) + 
                          ($o2Norm * $o2Peso);
        
        // Convertir a la escala 0-500 donde 500 es peor
        $indiceCalidad = round($indicePonderado * 500);
        
        // Determinar categoría
        $categoriaCalidad = $this->determinarCategoriaCalidad($indiceCalidad);
        
        return [
            'indice_calidad' => $indiceCalidad,
            'porcentaje_calidad' => round(($indiceCalidad / 500) * 100),
            'categoria_calidad' => $categoriaCalidad,
            'tipo_funcion' => $this->tipoFuncion
        ];
    }
    
    /**
     * Obtiene el sistema difuso
     * @return FuzzySystem Sistema difuso
     */
    public function getSistema() {
        return $this->sistema;
    }
}