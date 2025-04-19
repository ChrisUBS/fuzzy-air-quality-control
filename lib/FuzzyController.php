<?php
/**
 * Controlador difuso específico para el sistema de ventilador
 */
class FuzzyController {
    private $sistema;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->sistema = new FuzzySystem();
        $this->configurarSistema();
    }
    
    /**
     * Configura el sistema difuso con las variables y reglas para el ventilador
     */
    private function configurarSistema() {
        // 1. Configurar variables de entrada
        
        // Variable de temperatura ambiente (0-40 °C)
        // Ajustamos las funciones de membresía para cubrir mejor todo el rango
        $temperatura = new FuzzyVariable('temperatura', 0, 40);
        $temperatura->agregarConjunto(new FuzzySet('baja', FuzzySet::TRIANGULAR, [0, 0, 20]))
                   ->agregarConjunto(new FuzzySet('media', FuzzySet::TRIANGULAR, [15, 25, 30]))
                   ->agregarConjunto(new FuzzySet('alta', FuzzySet::TRIANGULAR, [25, 40, 40]));
        
        // 2. Configurar variables de salida
        
        // Variable de velocidad del ventilador (0-3000 RPM)
        // Ajustamos las funciones para tener rangos más definidos
        $velocidad = new FuzzyVariable('velocidad', 0, 3000);
        $velocidad->agregarConjunto(new FuzzySet('lenta', FuzzySet::TRIANGULAR, [0, 0, 500]))
                 ->agregarConjunto(new FuzzySet('media', FuzzySet::TRIANGULAR, [300, 1000, 1500]))
                 ->agregarConjunto(new FuzzySet('rapida', FuzzySet::TRIANGULAR, [1200, 3000, 3000]));
        
        // 3. Agregar variables al sistema
        $this->sistema->agregarVariableEntrada($temperatura)
                      ->agregarVariableSalida($velocidad);
        
        // 4. Configurar reglas difusas con pesos ajustados para priorizar temperaturas extremas
        
        // Regla 1: Si la temperatura es baja, entonces la velocidad del ventilador es lenta
        $this->sistema->agregarRegla(new FuzzyRule(
            ['temperatura' => ['baja' => '']],
            ['velocidad' => 'lenta'],
            1.0 // Peso máximo para esta regla
        ));
        
        // Regla 2: Si la temperatura es media, entonces la velocidad del ventilador es media
        $this->sistema->agregarRegla(new FuzzyRule(
            ['temperatura' => ['media' => '']],
            ['velocidad' => 'media'],
            1.0
        ));
        
        // Regla 3: Si la temperatura es alta, entonces la velocidad del ventilador es rápida
        $this->sistema->agregarRegla(new FuzzyRule(
            ['temperatura' => ['alta' => '']],
            ['velocidad' => 'rapida'],
            1.0 // Peso máximo para esta regla
        ));
        
        // Reglas adicionales para reforzar comportamiento en extremos
        // Si temperatura es muy baja, velocidad muy lenta
        if (!isset($this->sistema->agregarRegla)) {
            $this->sistema->agregarRegla(new FuzzyRule(
                ['temperatura' => ['baja' => '']],
                ['velocidad' => 'lenta'],
                1.5
            ));
        }
        
        // Si temperatura es muy alta, velocidad muy rápida
        if (!isset($this->sistema->agregarRegla)) {
            $this->sistema->agregarRegla(new FuzzyRule(
                ['temperatura' => ['alta' => '']],
                ['velocidad' => 'rapida'],
                1.5
            ));
        }
    }
    
    /**
     * Procesa las entradas y devuelve las salidas en formato adecuado para la interfaz
     * @param float $temperatura Temperatura ambiente en °C
     * @return array Resultados formateados
     */
    public function procesar($temperatura) {
        // Asegurar que los valores están en el rango correcto
        $temperatura = max(0, min(40, floatval($temperatura)));
        
        // Para temperaturas extremas, asignar valores extremos directamente
        if ($temperatura <= 5) {
            // Para temperaturas muy bajas, usar directamente velocidad mínima
            $velocidadRPM = $temperatura * 40; // 0-200 RPM para temperaturas 0-5°C
            $porcentajeVelocidad = round(($velocidadRPM / 3000) * 100);
            
            return [
                'velocidad_rpm' => $velocidadRPM,
                'velocidad_porcentaje' => $porcentajeVelocidad
            ];
        } else if ($temperatura >= 35) {
            // Para temperaturas muy altas, usar directamente velocidad máxima
            $velocidadRPM = 2500 + (($temperatura - 35) / 5) * 500; // 2500-3000 RPM para temperaturas 35-40°C
            $porcentajeVelocidad = round(($velocidadRPM / 3000) * 100);
            
            return [
                'velocidad_rpm' => $velocidadRPM,
                'velocidad_porcentaje' => $porcentajeVelocidad
            ];
        }
        
        // Evaluar el sistema difuso para temperaturas intermedias
        $resultado = $this->sistema->evaluar([
            'temperatura' => $temperatura
        ]);
        
        // Verificar si los resultados son válidos
        if (!isset($resultado['velocidad'])) {
            // Si hay algún problema, usar valores predeterminados basados en la temperatura
            return $this->calcularValoresPredeterminados($temperatura);
        }
        
        // Formatear los resultados para la interfaz
        $velocidadRPM = round($resultado['velocidad']);
        $porcentajeVelocidad = round(($velocidadRPM / 3000) * 100); // Convertir a porcentaje (0-100%)
        
        return [
            'velocidad_rpm' => $velocidadRPM,
            'velocidad_porcentaje' => $porcentajeVelocidad
        ];
    }
    
    /**
     * Calcula valores predeterminados en caso de error
     * @param float $temperatura Temperatura ambiente en °C
     * @return array Resultados calculados manualmente
     */
    private function calcularValoresPredeterminados($temperatura) {
        // Valores predeterminados según la temperatura usando una función lineal
        // para asegurar que 0°C -> 0 RPM y 40°C -> 3000 RPM
        $velocidadRPM = round(($temperatura / 40) * 3000);
        $porcentajeVelocidad = round(($velocidadRPM / 3000) * 100);
        
        return [
            'velocidad_rpm' => $velocidadRPM,
            'velocidad_porcentaje' => $porcentajeVelocidad
        ];
    }
    
    /**
     * Obtiene los datos para las gráficas
     * @return array Datos para las gráficas
     */
    public function obtenerDatosGraficas() {
        return $this->sistema->obtenerDatosGraficas();
    }
    
    /**
     * Obtiene el sistema difuso
     * @return FuzzySystem Sistema difuso
     */
    public function getSistema() {
        return $this->sistema;
    }
}