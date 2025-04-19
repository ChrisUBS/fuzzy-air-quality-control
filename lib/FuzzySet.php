<?php
/**
 * Clase para representar un conjunto difuso
 */
class FuzzySet {
    private $nombre;
    private $tipo;
    private $parametros;
    
    // Constantes para los tipos de conjuntos difusos
    const TRIANGULAR = 'triangular';
    const TRAPEZOIDAL = 'trapezoidal';
    const GAUSSIANO = 'gaussiano';
    const SINGLETON = 'singleton';
    
    /**
     * Constructor del conjunto difuso
     * @param string $nombre Nombre del conjunto
     * @param string $tipo Tipo de función de membresía (triangular, trapezoidal, gaussiano, singleton)
     * @param array $parametros Parámetros de la función de membresía
     */
    public function __construct($nombre, $tipo, array $parametros) {
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->parametros = $parametros;
        
        // Validar los parámetros según el tipo
        $this->validarParametros();
    }
    
    /**
     * Valida que los parámetros sean correctos según el tipo de conjunto
     * @throws Exception Si los parámetros no son válidos
     */
    private function validarParametros() {
        switch ($this->tipo) {
            case self::TRIANGULAR:
                if (count($this->parametros) != 3) {
                    throw new Exception("Un conjunto triangular requiere 3 parámetros (a, b, c)");
                }
                break;
                
            case self::TRAPEZOIDAL:
                if (count($this->parametros) != 4) {
                    throw new Exception("Un conjunto trapezoidal requiere 4 parámetros (a, b, c, d)");
                }
                break;
                
            case self::GAUSSIANO:
                if (count($this->parametros) != 2) {
                    throw new Exception("Un conjunto gaussiano requiere 2 parámetros (centro, desviación)");
                }
                break;
                
            case self::SINGLETON:
                if (count($this->parametros) != 1) {
                    throw new Exception("Un conjunto singleton requiere 1 parámetro (valor)");
                }
                break;
                
            default:
                throw new Exception("Tipo de conjunto difuso no reconocido: {$this->tipo}");
        }
    }
    
    /**
     * Calcula el grado de membresía de un valor en este conjunto difuso
     * @param float $x Valor para calcular membresía
     * @return float Grado de membresía entre 0 y 1
     */
    public function calcularMembresia($x) {
        switch ($this->tipo) {
            case self::TRIANGULAR:
                return $this->triangular($x, $this->parametros[0], $this->parametros[1], $this->parametros[2]);
                
            case self::TRAPEZOIDAL:
                return $this->trapezoidal($x, $this->parametros[0], $this->parametros[1], $this->parametros[2], $this->parametros[3]);
                
            case self::GAUSSIANO:
                return $this->gaussiano($x, $this->parametros[0], $this->parametros[1]);
                
            case self::SINGLETON:
                return $this->singleton($x, $this->parametros[0]);
                
            default:
                return 0;
        }
    }
    
    /**
     * Función de membresía triangular: f(x,a,b,c) = max(min((x-a)/(b-a), (c-x)/(c-b)), 0)
     */
    private function triangular($x, $a, $b, $c) {
        if ($x <= $a || $x >= $c) {
            return 0;
        } elseif ($x == $b) {
            return 1;
        } elseif ($x > $a && $x < $b) {
            return ($x - $a) / ($b - $a);
        } else {
            return ($c - $x) / ($c - $b);
        }
    }
    
    /**
     * Función de membresía trapezoidal: f(x,a,b,c,d)
     */
    private function trapezoidal($x, $a, $b, $c, $d) {
        if ($x <= $a || $x >= $d) {
            return 0;
        } elseif ($x >= $b && $x <= $c) {
            return 1;
        } elseif ($x > $a && $x < $b) {
            return ($x - $a) / ($b - $a);
        } else {
            return ($d - $x) / ($d - $c);
        }
    }
    
    /**
     * Función de membresía gaussiana: f(x,c,σ) = exp(-(x-c)²/(2σ²))
     */
    private function gaussiano($x, $centro, $desviacion) {
        $exponent = -pow($x - $centro, 2) / (2 * pow($desviacion, 2));
        return exp($exponent);
    }
    
    /**
     * Función singleton: f(x,a) = 1 si x=a, 0 en otro caso
     */
    private function singleton($x, $valor) {
        return ($x == $valor) ? 1 : 0;
    }
    
    /**
     * Obtiene el nombre del conjunto
     * @return string Nombre del conjunto
     */
    public function getNombre() {
        return $this->nombre;
    }
    
    /**
     * Obtiene el tipo de función de membresía
     * @return string Tipo de función
     */
    public function getTipo() {
        return $this->tipo;
    }
    
    /**
     * Obtiene los parámetros de la función de membresía
     * @return array Parámetros
     */
    public function getParametros() {
        return $this->parametros;
    }
}