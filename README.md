# Sistema de Control Difuso para la Calidad del Aire

Este proyecto implementa un sistema de control difuso (Fuzzy Logic) para obtener la calidad del aire en base a los gases del ambiente. La calidad del aire se obtiene mediante la evaluación de por lo menos 4 gases diferentes.

## Características

- Interfaz web interactiva con control deslizante para cada uno de los gases.
- Visualización en tiempo real de los resultados
- Gráficas de funciones de membresía
- Implementación completa de lógica difusa en PHP
- Documentación detallada del sistema

## Requisitos de Sistema

- PHP 7.0 o superior
- Servidor web (Apache, Nginx, etc.)
- Navegador web moderno con soporte para JavaScript

## Instalación

1. Clone o descargue este repositorio en su servidor web:

```bash
git clone https://github.com/ChrisUBS/fuzzy-air-quality-control
```

2. Asegúrese de que el directorio tenga permisos adecuados:

```bash
chmod -R 755 fuzzy-air-quality-control
```

3. Acceda al sistema a través de su navegador web:

```
http://localhost/fuzzy-air-quality-control/
```

## Estructura del Proyecto

```
fuzzy-air-quality-control/
│
├── index.php                  # Página principal y formulario de entrada
├── ayuda.php                  # Página de documentación
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
│   └── FuzzyController.php    # Controlador principal
```

## Uso del Sistema

1. Ajuste el control deslizante para establecer los valores de los gases presentes en el medio.
2. El sistema evaluará automáticamente la calidad del aire.
3. Los resultados se mostrarán en la pantalla principal.
4. Los gráficos mostrarán las funciones de membresía.
5. Para obtener más información sobre el funcionamiento del sistema, acceda a la página de documentación a través del enlace de información.

## Componentes del Sistema

### Variables de Entrada (Párticulas por Millón)
- **CO2:** Rango de 900-1046
- **H2:** Rango de 500-540
- **CO:** Rango de 490-520
- **O2:** Rango de 4-8

### Variables de Salida
- **Índice de la Calidad del Aire:** Rango de 0 a 500

### Conjuntos Difusos
- **CO2, H2, CO, O2:** Nocivo, Medio Nocivo, Regular, Medio Bueno, Optimo.
- **Calidad del Aire:** Nocivo, Medio Nocivo, Regular, Medio Bueno, Optimo.

## Reglas Difusas

El sistema utiliza las siguientes reglas para determinar el índice de la calidad del aire:

▪ Si CO2 es alto o CO es alto, entonces ICA es nocivo.
▪ Si CO2 es medio y H2 es alto, entonces ICA es medio nocivo.
▪ Si CO2 es bajo y CO es bajo, entonces ICA es regular.
▪ Si CO2 es bajo y O2 es alto, entonces ICA es medio bueno.
▪ Si CO2 es bajo y CO es bajo y O2 es alto, entonces ICA es óptimo.

## Funcionalidades Técnicas

- **Fusificación:** Conversión de valores nítidos a conjuntos difusos
- **Evaluación de reglas:** Aplicación de reglas difusas con operadores AND y OR
- **Defusificación:** Método del centroide para convertir resultados difusos en valores nítidos
- **Visualización:** Gráficos interactivos de funciones de membresía

## Personalización

El sistema puede ser personalizado modificando los siguientes archivos:

- **lib/FuzzyController.php:** Ajuste de conjuntos difusos y reglas
- **css/style.css:** Personalización de la apariencia
- **js/charts.js:** Modificación de gráficos y visualizaciones

## Licencia

Este proyecto está licenciado bajo [MIT License](LICENSE).