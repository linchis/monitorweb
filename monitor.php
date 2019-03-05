<?php
header('Content-Type: text/html; charset=utf-8');
$host = $_SERVER['HTTP_HOST'];
$host_name = "Monitor conexiones";
setlocale(LC_TIME, "es_ES.utf8");
date_default_timezone_set('Europe/Madrid');

require '../../db/Medoo.php';
use Medoo\Medoo;

require_once '../../lib/KLogger.php';
$log = new KLogger ( "log_monitor.txt" , KLogger::DEBUG );
$action = "";
$cliente = "";
$response_action = "";

//recoger parametros URL
if(isset($_GET['action']) && $_GET['action']!=""){
    $action = $_GET['action'];
    $clienteid = isset($_GET['cliente'])?$_GET['cliente']:"";
    $init = isset($_GET['init'])?$_GET['init']:"";
    $end = isset($_GET['end'])?$_GET['end']:""; 
    $log->LogInfo("MONITOR:GET:ACTION::".$action.">>>".$init."-".$end);         
    if ($action == "conexiones"){
        $result = getAllItems(null, $clienteid, $init,$end);

    }else if ($action == "clientes"){
        $result = getClientes(null);
    }

}else if(isset($_POST['action']) && $_POST['action'] != "" &&
    isset($_POST['id']) && $_POST['id'] != ""){
    $action = $_POST['action'];
    $clienteid = $_POST['id'];
    $clienteip = $_POST['ip'];
    $log->LogInfo("MONITOR:POST:ACTION::".$action);   
    $result = addItem(null, $clienteid,$clienteip); 
}

$curDate = date('Y-m-d H:i:s');  
$result['timestamp'] = $curDate;    
echo json_encode($result);              //PUNTO DE SALIDA

//DB Medoo
function getConnection() {
    $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'xxxxxxxxxxxxx',
            'server' => 'xxxxxxxxxxxxx',
            'username' => 'xxxxxxxxxxxxx',
            'password' => 'xxxxxxxxxxxxx'
        ]);         
    return $database;
}


function getClientes($token) {
    global $log;

    $log->LogInfo("**** Peticion: Consultar clientes");
    if (esTokenValido($token)){

        if (isset($ini) && $ini !="" )

        $filtro_cliente = "";
        $sql = 'select distinct ORIGEN from conexion;';           
        try {
            $db = getConnection();
            $items = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);            
            $db = null;
            if(isset($items)){               
                $log->LogInfo(sizeof($items)." clientes.");
            }else{
                $log->LogInfo("no hay clientes.");
            }
        } catch(PDOException $e) {            
            $log->LogError("Ver clientes ERROR:::". $e->getMessage());
        }
         return array(
            "action" => "_GETCLIENTES",
            "resp" => $items,
            "timestamp" => ""         
        );      
    }else{
        return array(
            "action" => "_GETCLIENTES",
            "resp" => null,
            "timestamp" => ""         
        );            
    }
}

function getAllItems($token, $clienteid, $ini, $end) {
    global $log;

    $log->LogInfo("**** Peticion: Consultar Items de: " . $clienteid);
    if (esTokenValido($token)){

        if ($clienteid != ""){          
            $sql = "select c.DATETIME as Day, hour(c.DATETIME) as Hour, count(*) as Conex from conexion c where c.ORIGEN = '".$clienteid."' group by day(c.DATETIME), hour(c.DATETIME) order by c.DATETIME ASC;";
        }

        $items = array();        
        $log->LogInfo("GET_ALL_ITEMS:Query:::".$sql);
        try {
            $db = getConnection();
            $items = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
            
            //var_dump( $db->error() );
            $db = null;
            if(isset($items)){               
                $log->LogInfo(sizeof($items)." items.");
            }else{
                $log->LogInfo("no hay items.");
            }
        } catch(PDOException $e) {            
            $log->LogError("Ver items ERROR:::". $e->getMessage());
        }

        return array(
            "action" => "_SELECTALL",
            "resp" => $items,
            "timestamp" => ""         
        );            

    }else{
        return array(
            "action" => "_SELECTALL",
            "resp" => null,
            "timestamp" => ""         
        );            
    }
}


/**
 * Funcion para añadir un item en BBDD (POST)
 * @param $id, $token
 */
function addItem($token, $clienteid, $clienteip){
    global $log;
    $log->LogInfo("**** Peticion: Insertar Item:" . $clienteid .':'.$clienteip);

    if (esTokenValido($token)){
                       
        //date('Y-m-d H:i:s')                       
        try {
            $db = getConnection();
            $item = $db->insert(
                "conexion",[ 
                    'DATETIME' => date('Y-m-d H:i:s'), 
                    'ORIGEN' => $clienteid,
                    'IP'    => $clienteip
                ]
            );
            $log->LogInfo("Insertando item: ".$db->id());
            $db = null;
            $estado = "OK";
            $mensaje = "Item insertado!";

        } catch(PDOException $e) {
            $log->LogError("Insertar item: ".$id." ERROR:::". $e->getMessage());
            $estado = "FALLO";
            $mensaje = "No se ha insertado item: " . $e;
        }

        $log->LogInfo("FIN Insert item");
        return array(
            "action" => "_INSERT",
            "resp" => $estado,
            "timestamp" => ""         
        );        
        
    }else{
        return array(
            "action" => "_INSERT",
            "resp" => "ERROR",
            "timestamp" => ""         
        );                
    }
}


function esTokenValido($token){
    return true;
}
?>
