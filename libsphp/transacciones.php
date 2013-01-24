<?php

$action = $_GET['actio'];

$action();

function NewUser()
{
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $datos = $data->datos;

    $nombre = $datos->clieNom;
    $cuit = $datos->clieDetCuit;
    $cod = $datos->clieCod;
    $dom1 = $datos->clieDetDom1;
    $dom2 = $datos->clieDetDom2;
    $tel1 = $datos->clieDetTel1;
    $tel2 = $datos->clieDetTel2;
    $mov1 = $datos->clieDetMovil1;
    $mov2 = $datos->clieDetMovil2;
    $email1 = $datos->clieDetEmail1;
    $email2 = $datos->clieDetEmail2;
    $sitio = $datos->clieDetSitio;
    $obs = $datos->clieDetObs;

    if (!empty($nombre) && !empty($cuit) && !empty($cod) && !empty($dom1)) {
        $pdo=DBConn();
        $sqlInsert = <<<JAR
INSERT INTO `clientes` (`clientes_cod`, `clientes_nombre`) VALUES
(:cod,:nombre);
JAR;
        $sqlInsert1 = <<<JAR
insert into `clientes_detalles`( `clientes_codigo`,`domicilio1`,`domicilio2`,`telefono1`,`telefono2`,`movil1`,`movil2`,`email`,`email_alternativo`,`sitio_web`,`cuit`,`observaciones`) VALUES
(:cod,:dom1,:dom2,:tel1,:tel2,:mov1,:mov2,:email1,:email2,:sitio,:cuit,:obs);
JAR;

        $pdo->beginTransaction();
        try {
            $sqlAction = $pdo->prepare($sqlInsert);
            $sqlAction->bindParam(':cod', $cod, PDO::PARAM_INT, 11);
            $sqlAction->bindParam(':nombre', $nombre, PDO::PARAM_STR, 50);
            $sqlAction->execute();

            $sqlAction =$pdo->prepare($sqlInsert1);
            $sqlAction->bindParam(':cod', $cod, PDO::PARAM_INT, 11);
            $sqlAction->bindParam(':dom1', $dom1, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':dom2', $dom2, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':tel1', $tel1, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':tel2', $tel, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':mov1', $mov1, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':mov2', $mov2, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':email1', $email1, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':email2', $email2, PDO::PARAM_STR, 50);
            $sqlAction->bindParam(':sitio', $sitio, PDO::PARAM_STR, 50);
        	$sqlAction->bindParam(':cuit', $cuit, PDO::PARAM_STR, 255);
            $sqlAction->bindParam(':obs', $obs, PDO::PARAM_STR, 255);
        	$sqlAction->execute();

            $pdo->commit();
            echo "guardado exitosamente";
        }

        catch(PDOException $e) {
            $sfc = ($e->getMessage());
            echo soloMessage($sfc);
            $pdo->rollBack();
        	echo "Pelotudo";

        }
    }
}

function FillSelect(){
	$pdo=DBConn();
	$sqlStrQuery=<<<JAR
SELECT operadoras_id, operadoras_nombre FROM operadoras WHERE 1;
JAR;
	$sqlQuery=$pdo->query($sqlStrQuery, PDO::FETCH_ASSOC);
	header("Content-type: application/json");
	print json_encode($sqlQuery->fetchAll());
}
/**
* Funciones Auxiliares
*
*
* */

function DBConn(){
	$pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=juan;port=3306', 'root', '',
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $pdo;
}
function soloMessage($message)
{
    if (strstr($message, 'SQLSTATE[')) {
        preg_match('/SQLSTATE\[(\w+)\]:(.*)/', $message, $matches);
        $finalMessage = $matches[2];
        return $finalMessage;
    }
}

?>