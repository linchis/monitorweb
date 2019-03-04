# monitorweb
Monitorización de conexión a internet de una máquina y generación de estádisticas

Este proyecto consta de tres archivos:

index.php?cliente="NOMBRE_EQUIPO"
  Pagina que se debe abrir en el equipo a controlar.

monitor.php
  Core que se invoica desde index.php mediente AJAX, requiere los siguientes parametros: POST|GET
        'action':'alive|view', 
                'id':"NOMBRE_EQUIPO|NOMBRE_EQUIPO",
                'ip':"IP"    
            }; 
estadisticas.php
  Web que presenta las estadisticas (gráficas y listado de conexiones acumuladas) de todos los equipos monoitorizados. No requiere parametros. 
