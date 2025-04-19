<?php
/**
 * Clase para representar una variable difusa
 */
class FuzzyVariable {
    private $nombre;
    private $conjuntos = [];
    private $minValue;
    private $maxValue;
    
    /**
     * Constructor de la variable difusa
     * @param string $nombre Nombre de la variable
     * @param float $minValue Valor mínimo del rango
     * @param float $maxValue Valor máximo del rango
     */
    public function __construct($nombre, $minValue, $maxValue) {
        $this->nombre = $nombre;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }
    
    /**
     * Agrega un conjunto difuso a la variable
     * @param FuzzySet $conjunto Conjunto difuso a agregar
     * @return FuzzyVariable Retorna la instancia actual para encadenamiento
     */
    public function agregarConjunto(FuzzySet $conjunto) {
        $this->conjuntos[$conjunto->getNombre()] = $conjunto;
        return $this;
    }
    
    /**
     * Obtiene los puntos de las funciones de membresía para graficar
     * @param int $puntos Número de puntos a generar
     * @return array Datos para graficar
     */
    public function obtenerPuntosGrafica($puntos = 100) {
        $datos = [];
        $paso = ($this->maxValue - $this->minValue) / $puntos;
        
        foreach ($this->conjuntos as $nombreConjunto => $conjunto) {
            $datosConjunto = [
                'label' => $nombreConjunto,
                'data' => [],
                'borderColor' => $this->generarColor($nombreConjunto),
                'fill' => false
            ];
            
            for ($i = 0; $i <= $puntos; $i++) {
                $x = $this->minValue + ($i * $paso);
                $y = $conjunto->calcularMembresia($x);
                
                $datosConjunto['data'][] = [
                    'x' => $x,
                    'y' => $y
                ];
            }
            
            $datos[] = $datosConjunto;
        }
        
        return $datos;
    }
    
    /**
     * Genera un color basado en el nombre del conjunto
     * @param string $nombre Nombre del conjunto
     * @return string Color en formato hexadecimal
     */
    private function generarColor($nombre) {
        $colores = [
            'baja' => '#3498db',
            'media' => '#2ecc71',
            'alta' => '#e74c3c',
            'lenta' => '#9b59b6',
            'media' => '#1abc9c',
            'rapida' => '#d35400'
        ];
        
        if (isset($colores[strtolower($nombre)])) {
            return $colores[strtolower($nombre)];
        }
        
        // Color por defecto basado en el hash del nombre
        $hash = md5($nombre);
        return '#' . substr($hash, 0, 6);
    }
    
    /**
     * Obtiene el nombre de la variable
     * @return string Nombre de la variable
     */
    public function getNombre() {
        return $this->nombre;
    }
    
    /**
     * Obtiene el conjunto difuso por nombre
     * @param string $nombre Nombre del conjunto difuso
     * @return FuzzySet|null Conjunto difuso o null si no existe
     */
    public function getConjunto($nombre) {
        return $this->conjuntos[$nombre] ?? null;
    }
    
    /**
     * Obtiene todos los conjuntos difusos
     * @return array Array asociativo de conjuntos difusos
     */
    public function getConjuntos() {
        return $this->conjuntos;
    }
    
    /**
     * Obtiene el valor mínimo del rango
     * @return float Valor mínimo
     */
    public function getMinValue() {
        return $this->minValue;
    }
    
    /**
     * Obtiene el valor máximo del rango
     * @return float Valor máximo
     */
    public function getMaxValue() {
        return $this->maxValue;
    }
    
    /**
     * Fusifica un valor crisp en todos los conjuntos de la variable
     * @param float $valor Valor crisp a fusificar
     * @return array Array asociativo con el grado de membresía en cada conjunto
     */
    public function fusificar($valor) {
        $resultado = [];
        
        foreach ($this->conjuntos as $nombre => $conjunto) {
            $resultado[$nombre] = $conjunto->calcularMembresia($valor);
        }
        
        return $resultado;
    }
}