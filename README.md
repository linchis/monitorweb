# monitorweb
Monitorización de conexión a internet de una máquina y generación de estádisticas.

Este proyecto consta de tres archivos:
+[index.php].
Pagina que se debe abrir en el equipo a controlar, requiere el parámetro: GET >>> cliente="NOMBRE_EQUIPO".
+[monitor.php]
Core que se invoica desde index.php mediente AJAX, requiere los siguientes parametros: POST|GET
        'action':'alive|view', 
                'id':"NOMBRE_EQUIPO|NOMBRE_EQUIPO",
                'ip':"IP"    
            };             
Requiere datos de conexión a base de datos. En este caso se usa la librería Medoo.
+[estadisticas.php]
Web que presenta las estadisticas (gráficas y listado de conexiones acumuladas) de todos los equipos monoitorizados. No requiere parametros.

Todo los archivos tienen dependencia con la librería Looger.
