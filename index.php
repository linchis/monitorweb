<?php
header('Content-Type: text/html; charset=utf-8');
$host = $_SERVER['HTTP_HOST'];
$host_name = "Monitor conexiones";
setlocale(LC_TIME, "es_ES.utf8");
date_default_timezone_set('Europe/Madrid');

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
 echo '<script> var ipcliente = "'.$ip.'";</script>';

$cliente= "DEFAULT";
if(isset($_GET['cliente']) && $_GET['cliente']!=""){
    $cliente = $_GET['cliente'];
    echo '<script> var jscliente = "'.$cliente.'";</script>';
}
?>

<html>
<head>
    <title>Monitor conexiones | TEST</title>
</head>
<body>

<div id="wrapper">
    <h1>Última comprobación <span id="last_connect"></span></h1>    
</div>

</body>
</html>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

$(document).ready(function(){

    console.log("JSCLIENTE: " + jscliente);
    init(jscliente);

    function init(jscliente){
        info_turnos(jscliente);
        auto_reload(60000, jscliente);
    }

    function auto_reload(interval,jscliente){
        var refreshId = setInterval(function(){            
            console.log("AUTO_RELOAD");
            info_turnos(jscliente);
        }, interval);
    }

    function info_turnos(jscliente){    
        
            var resultados = null;
            var method = 'POST';                                
            var json = {
                'action':'alive', 
                'id':jscliente,
                'ip':ipcliente    
            }; 
            $.ajax({
                url : 'http://proyectosxs.xyz/webdev/monitor/monitor.php',
                type : method,
                data: json,     
                dataType:'json',                
                success : function(data) {
                    console.log("SUCCESS:::"+JSON.stringify(data));                    
                    resultados = data;                    
                },
                error : function(request,error)
                {
                    console.log("ERROR:::"+JSON.stringify(request)+">>>"+error);
                },
                complete: function() {
                    if(resultados != null){
                        showResponse(resultados);                        
                    }else{
                        console.log("SIN RESULTADO")
                    }
                }
            });                        
        }

    function showResponse(obj){
        $("#last_connect").text(obj.timestamp);        
    }
    
});
</script>