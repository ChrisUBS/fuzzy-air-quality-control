<?php
/**
 * Clase principal del sistema difuso
 */
class FuzzySystem {
    private $variablesEntrada = [];
    private $variablesSalida = [];
    private $reglas = [];
    
    /**
     * Agrega una variable de entrada al sistema
     * @param FuzzyVariable $variable Variable difusa de entrada
     * @return FuzzySystem Instancia actual para encadenamiento
     */
    public function agregarVariableEntrada(FuzzyVariable $variable) {
        $this->variablesEntrada[$variable->getNombre()] = $variable;
        return $this;
    }
    
    /**
     * Agrega una variable de salida al sistema
     * @param FuzzyVariable $variable Variable difusa de salida
     * @return FuzzySystem Instancia actual para encadenamiento
     */
    public function agregarVariableSalida(FuzzyVariable $variable) {
        $this->variablesSalida[$variable->getNombre()] = $variable;
        return $this;
    }
    
    /**
     * Agrega una regla al sistema
     * @param FuzzyRule $regla Regla difusa
     * @return FuzzySystem Instancia actual para encadenamiento
     */
    public function agregarRegla(FuzzyRule $regla) {
        $this->reglas[] = $regla;
        return $this;
    }
    
    /**
     * Evalúa el sistema con los valores de entrada proporcionados
     * @param array $entradas Array asociativo [nombreVariable => valorCrisp]
     * @return array Valores defusificados de salida [nombreVariable => valorCrisp]
     */
    public function evaluar(array $entradas) {
        // Paso 1: Fusificar las entradas
        $valoresFusificados = [];
        foreach ($entradas as $nombreVariable => $valorCrisp) {
            if (isset($this->variablesEntrada[$nombreVariable])) {
                $valoresFusificados[$nombreVariable] = $this->variablesEntrada[$nombreVariable]->fusificar($valorCrisp);
            }
        }
        
        // Paso 2: Evaluar todas las reglas
        $valoresActivacion = $this->evaluarReglas($valoresFusificados);
        
        // Paso 3: Defusificar las salidas
        $resultados = $this->defusificar($valoresActivacion);
        
        return $resultados;
    }
    
    /**
     * Evalúa todas las reglas con los valores fusificados
     * @param array $valoresFusificados Valores fusificados de entrada
     * @return array Valores de activación para cada variable de salida y conjunto
     */
    private function evaluarReglas(array $valoresFusificados) {
        $valoresActivacion = [];
        
        // Inicializar estructura para valores de activación
        foreach ($this->variablesSalida as $nombreVariable => $variable) {
            $valoresActivacion[$nombreVariable] = [];
            foreach ($variable->getConjuntos() as $nombreConjunto => $conjunto) {
                $valoresActivacion[$nombreVariable][$nombreConjunto] = 0;
            }
        }
        
        // Evaluar cada regla
        foreach ($this->reglas as $regla) {
            $resultadoRegla = $regla->evaluar($valoresFusificados);
            
            // Agregar los resultados usando el operador OR (máximo)
            foreach ($resultadoRegla as $nombreVariable => $conjuntos) {
                foreach ($conjuntos as $nombreConjunto => $grado) {
                    if (isset($valoresActivacion[$nombreVariable][$nombreConjunto])) {
                        $valoresActivacion[$nombreVariable][$nombreConjunto] = 
                            max($valoresActivacion[$nombreVariable][$nombreConjunto], $grado);
                    }
                }
            }
        }
        
        return $valoresActivacion;
    }
    
    /**
     * Defusifica los valores de activación utilizando el método del centroide
     * @param array $valoresActivacion Valores de activación para cada variable y conjunto
     * @return array Valores crisp de salida [nombreVariable => valorCrisp]
     */
    private function defusificar(array $valoresActivacion) {
        $resultados = [];
        
        // Defusificar cada variable de salida
        foreach ($this->variablesSalida as $nombreVariable => $variable) {
            if (!isset($valoresActivacion[$nombreVariable])) {
                continue;
            }
            
            // Método del centroide (Center of Gravity)
            $numerador = 0;
            $denominador = 0;
            
            // Número de puntos para la discretización
            $numPuntos = 100;
            $min = $variable->getMinValue();
            $max = $variable->getMaxValue();
            $paso = ($max - $min) / $numPuntos;
            
            for ($i = 0; $i <= $numPuntos; $i++) {
                $x = $min + ($i * $paso);
                
                // Encontrar el máximo grado de membresía para este punto (regla de agregación)
                $maxGrado = 0;
                foreach ($valoresActivacion[$nombreVariable] as $nombreConjunto => $gradoActivacion) {
                    if ($gradoActivacion > 0) {
                        $conjunto = $variable->getConjunto($nombreConjunto);
                        if ($conjunto) {
                            $grado = min($gradoActivacion, $conjunto->calcularMembresia($x));
                            $maxGrado = max($maxGrado, $grado);
                        }
                    }
                }
                
                $numerador += $x * $maxGrado;
                $denominador += $maxGrado;
            }
            
            // Evitar división por cero
            if ($denominador > 0) {
                $resultados[$nombreVariable] = $numerador / $denominador;
            } else {
                // Si no hay activación, usar un valor predeterminado (centro del rango)
                $resultados[$nombreVariable] = ($min + $max) / 2;
            }
        }
        
        return $resultados;
    }
    
    /**
     * Obtiene las variables de entrada
     * @return array Variables de entrada
     */
    public function getVariablesEntrada() {
        return $this->variablesEntrada;
    }
    
    /**
     * Obtiene las variables de salida
     * @return array Variables de salida
     */
    public function getVariablesSalida() {
        return $this->variablesSalida;
    }
    
    /**
     * Obtiene las reglas del sistema
     * @return array Reglas
     */
    public function getReglas() {
        return $this->reglas;
    }
    
    /**
     * Obtiene los datos para las gráficas de todas las variables
     * @return array Datos para gráficas
     */
    public function obtenerDatosGraficas() {
        $datos = [
            'entrada' => [],
            'salida' => []
        ];
        
        foreach ($this->variablesEntrada as $nombre => $variable) {
            $datos['entrada'][$nombre] = $variable->obtenerPuntosGrafica();
        }
        
        foreach ($this->variablesSalida as $nombre => $variable) {
            $datos['salida'][$nombre] = $variable->obtenerPuntosGrafica();
        }
        
        return $datos;
    }
}