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

?>

<html>
<head>
<title>Estadisticas conexion | CONEXIONES</title>
<!-- For ease i'm just using a JQuery version hosted by JQuery- you can download any version and link to it locally -->
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" 
	src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js"></script>
<script type="text/javascript" 
	src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.js"></script>

<style>
    .client {
        width: 300px;
        height: 400px;
        float:left;
    }
    .conex {
        width: 300px;
        height: 300px;
        float:left;
        overflow: scroll;
        scroll-behavior: auto;    
        margin-right: 20px;
        margin-top: 20px     
    }
    .c_rojo{
        color: red;
    }
    .c_negro{
        color: green;
    }
    
</style>
</head>
<body>

<div id="wrapper">
    <h1>Estadísticas monitor de conexiones - <span id="fecha_actualizacion"></span></h1>    
    <div class="charts">        
    </div>
    <hr>
    <div id="texto_action"></div>
    <div class="listados">        
    </div>        
</div>


</body>
</html>


<script>

$(document).ready(function(){

    var clientes = new Array();

    get_clientes("");

    //ver_conexiones("");
    //loadCharts();
   
    
    function get_clientes(){    
        
        var resultados = null;
        var method = 'GET';                                
        var init = $("#fini");
        var end = $("#fend");
        $.ajax({
            url : 'http://proyectosxs.xyz/webdev/monitor/monitor.php?action=clientes',
            type : method,
            //data: json,     //JSON.stringify(json),
            dataType:'json',
            //contentType: "application/json",
            success : function(data) {
                console.log("SUCCESS:::"+JSON.stringify(data));
                //resultados = jQuery.parseJSON(data);
                resultados = data;                    
            },
            error : function(request,error)
            {
                console.log("ERROR:::"+JSON.stringify(request)+">>>"+error);
            },
            complete: function() {
                if(resultados != null){                                            
                    console.log("Clientes:"+JSON.stringify(resultados));
                    clientes = resultados['resp'];
                    procesar_clientes();

                }else{
                    console.log("SIN RESULTADO")
                    return null;
                }
            }
        });                        
    }

    function procesar_clientes(){

        console.log("----Listado de clientes----" + clientes.length);
        if (clientes.length > 0){
            for(var i=0;i<clientes.length;i++){
                //console.log("Cliente: " + JSON.stringify(clientes[i]));
                console.log("Cliente: " + clientes[i]['ORIGEN']);

                ver_conexiones(clientes[i]['ORIGEN']);
            }
        }
    }

    function ver_conexiones(cliente){    
        
        var resultados = null;
        var method = 'GET';                                
        var init = $("#fini");
        var end = $("#fend");
        $.ajax({
            url : 'http://proyectosxs.xyz/webdev/monitor/monitor.php?action=conexiones'+
                '&cliente='+cliente+
                '&init='+init+
                '&end='+end,
            type : method,
            //data: json,     //JSON.stringify(json),
            dataType:'json',
            //contentType: "application/json",
            success : function(data) {
                console.log("SUCCESS:::"+JSON.stringify(data));
                //resultados = jQuery.parseJSON(data);
                resultados = data;                    
            },
            error : function(request,error)
            {
                console.log("ERROR:::"+JSON.stringify(request)+">>>"+error);
            },
            complete: function() {
                if(resultados != null){                        
                    loadChart(cliente,resultados);
                    showDataText(cliente,resultados);

                }else{
                    console.log("SIN RESULTADO")
                }
            }
        });                        
    }
    

    function loadChart(cliente,data){

        var html_canvas = '<canvas id="myChart_XXX" width="300px" height="100px"></canvas>';        
        var ctx = $(".charts");
        new_canvas = html_canvas.replace("XXX",cliente);
        ctx.append(new_canvas);
        
        //var ctx = document.getElementById("myChart").getContext('2d');
        var newctx = $("#myChart_"+cliente);
        
        //PROCESAR data
        var mylabels = new Array();
        var myvalues = new Array();        
        data.resp.forEach(procesa);

        function procesa(value){       				        
    		mylabels.push(value.Hour);
    		myvalues.push(value.Conex);	        	        
        }
       
        var chartColor = getRandomColor();
        console.log("Color del grafico: " + chartColor);
        var myChart = new Chart(newctx, {
            type: 'line',
            data: {                
                labels: mylabels,
                datasets: [{
                    label: cliente,
                    data: myvalues,
                    backgroundColor: chartColor,
                    //data: [12, 19, 3, 5, 2, 3],                   
                    /*
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],*/
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                },
                elements: {
		            line: {
		                tension: 0, // disables bezier curves
		            }
		        },
		         tooltips: {
		            mode: 'dataset'
		        }
            }
        });    
    }

    function showDataText(cliente,data){
        
        $("#fecha_actualizacion").text(data.timestamp);
    	$("#texto_action").text("ACTION: " + data.action + " AT: " + data.timestamp);
        text1 = "<div class='client'>";
        text1 += "<h3>"+cliente+"</h3>";        
    	text1 += "<div class='conex' id='"+cliente+"_INFO'>"        
        text1 += "<ul>";    	
    	data.resp.forEach(muestra)    	
    	text1 += "</ul>";
        text1 += "</div>";    	
        text1 += "</div>";        
    	
    	$(".listados").append(text1);    	
    }

    function muestra(value){    	
        var color = "c_negro";
        if (value.Conex < 60){
            color="c_rojo";
        }    
        text1 += "<li><span class="+color+">" + value.Day + " - " + value.Hour + ": " + value.Conex + "</span></li>";  
        
   		
    }


    function getRandomColor(){
        //var patron = 'rgba(255, 99, 132, 0.2)';
        var patron = "rgba(RR, GG, BB, 0.2)";
        // returns a random integer from 1 to 250
        var val1 = Math.floor(Math.random() * 250) + 1; 
        var val2 = Math.floor(Math.random() * 250) + 1; 
        var val3 = Math.floor(Math.random() * 250) + 1; 
        patron = patron.replace("RR",val1);
        patron = patron.replace("GG",val2);
        patron = patron.replace("BB",val3);

        return patron;
    }
});

</script>