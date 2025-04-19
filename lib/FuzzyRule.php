<?php
/**
 * Clase para representar una regla difusa
 */
class FuzzyRule {
    private $antecedentes = [];
    private $consecuentes = [];
    private $peso = 1.0;
    
    /**
     * Constructor de la regla difusa
     * @param array $antecedentes Array asociativo [variable => [conjunto => operador]]
     * @param array $consecuentes Array asociativo [variable => conjunto]
     * @param float $peso Peso de la regla (entre 0 y 1)
     */
    public function __construct(array $antecedentes, array $consecuentes, $peso = 1.0) {
        $this->antecedentes = $antecedentes;
        $this->consecuentes = $consecuentes;
        $this->peso = max(0, min(1, $peso)); // Limitar el peso entre 0 y 1
    }
    
    /**
     * Evalúa la regla con los valores difusos proporcionados
     * @param array $valoresDifusos Array asociativo [variable => [conjunto => grado]]
     * @return array Grado de activación para cada variable consecuente y conjunto
     */
    public function evaluar(array $valoresDifusos) {
        // Calcular el grado de activación de los antecedentes
        $gradoActivacion = $this->evaluarAntecedentes($valoresDifusos);
        
        // Aplicar el peso de la regla
        $gradoActivacion *= $this->peso;
        
        // Aplicar el grado de activación a los consecuentes
        $resultado = [];
        foreach ($this->consecuentes as $variable => $conjunto) {
            $resultado[$variable][$conjunto] = $gradoActivacion;
        }
        
        return $resultado;
    }
    
    /**
     * Evalúa los antecedentes de la regla
     * @param array $valoresDifusos Array asociativo con los grados de membresía
     * @return float Grado de activación entre 0 y 1
     */
    private function evaluarAntecedentes(array $valoresDifusos) {
        $valores = [];
        
        // Obtener los valores de activación de cada antecedente
        foreach ($this->antecedentes as $variable => $condiciones) {
            foreach ($condiciones as $conjunto => $operador) {
                // Verificar que exista el valor difuso para esta variable y conjunto
                if (!isset($valoresDifusos[$variable][$conjunto])) {
                    // Si no existe, la regla no se activa
                    return 0;
                }
                
                $grado = $valoresDifusos[$variable][$conjunto];
                
                // Aplicar operador NOT si es necesario
                if ($operador === 'NOT') {
                    $grado = 1 - $grado;
                }
                
                $valores[] = $grado;
            }
        }
        
        // Si no hay valores, la regla no se activa
        if (empty($valores)) {
            return 0;
        }
        
        // Aplicar operador AND (mínimo) entre todos los antecedentes
        return min($valores);
    }
    
    /**
     * Obtiene los antecedentes de la regla
     * @return array Antecedentes
     */
    public function getAntecedentes() {
        return $this->antecedentes;
    }
    
    /**
     * Obtiene los consecuentes de la regla
     * @return array Consecuentes
     */
    public function getConsecuentes() {
        return $this->consecuentes;
    }
    
    /**
     * Obtiene el peso de la regla
     * @return float Peso
     */
    public function getPeso() {
        return $this->peso;
    }
    
    /**
     * Establece el peso de la regla
     * @param float $peso Peso entre 0 y 1
     * @return FuzzyRule Instancia actual para encadenamiento
     */
    public function setPeso($peso) {
        $this->peso = max(0, min(1, $peso));
        return $this;
    }
    
    /**
     * Convierte la regla a una cadena de texto legible
     * @return string Representación de la regla
     */
    public function __toString() {
        $strAntecedentes = [];
        foreach ($this->antecedentes as $variable => $condiciones) {
            foreach ($condiciones as $conjunto => $operador) {
                $strAntecedentes[] = ($operador === 'NOT' ? 'NO ' : '') . "$variable ES $conjunto";
            }
        }
        
        $strConsecuentes = [];
        foreach ($this->consecuentes as $variable => $conjunto) {
            $strConsecuentes[] = "$variable ES $conjunto";
        }
        
        return "SI " . implode(" Y ", $strAntecedentes) . " ENTONCES " . implode(" Y ", $strConsecuentes);
    }
}