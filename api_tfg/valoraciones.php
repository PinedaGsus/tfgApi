<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/valoraciones.class.php';

$_respuesta = new respuestas;
$_valoraciones = new valoraciones;

if($_SERVER['REQUEST_METHOD']  == "GET"){
    if(isset($_GET['id'])){
        $idValoraciones = $_GET['id'];
        $datosValoracion = $_valoraciones->obtenerValoracion($idValoraciones);
        header("Content-Type: application/json");
        echo json_encode($datosValoracion);
        http_response_code(200);
    }elseif(isset($_GET['idUser'])){
        $idUsuario = $_GET['idUser'];
        $datosValoracion = $_valoraciones->obtenerValoracionesUsuario($idUsuario);
        header("Content-Type: application/json");
        echo json_encode($datosValoracion);
        http_response_code(200);
    }else{
        $listavaloraciones = $_valoraciones->listaValoraciones();
        header("Content-Type: application/json");
        echo json_encode($listavaloraciones);
        http_response_code(200);
    }

}else if($_SERVER['REQUEST_METHOD']  == "POST"){
        //recibimos los datos enviados
        $postBody = stripslashes(html_entity_decode(file_get_contents("php://input")));;
        //enviamos los datos al manejador
        $datosArray = $_valoraciones->post($postBody);
        //delvovemos una respuesta 
         header('Content-Type: application/json');
         if(isset($datosArray["result"]["error_id"])){
             $responseCode = $datosArray["result"]["error_id"];
             http_response_code($responseCode);
         }else{
             http_response_code(200);
         }
         echo json_encode([$datosArray]);

}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos datos al manejador
    $datosArray = $_valoraciones->put($postBody);
      //delvovemos una respuesta 
   header('Content-Type: application/json');
   if(isset($datosArray["result"]["error_id"])){
       $responseCode = $datosArray["result"]["error_id"];
       http_response_code($responseCode);
   }else{
       http_response_code(200);
   }
   echo json_encode($datosArray);

}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

      $headers = getallheaders();
      if(isset($headers["token"]) && isset($headers["idValoraciones"])){
          //recibimos los datos enviados por el header
          $send = [
              "token" => $headers["token"],
              "idValoraciones" =>$headers["idValoraciones"]
          ];
          $postBody = json_encode($send);
      }else{
          //recibimos los datos enviados
          $postBody = file_get_contents("php://input");
      }
      
      //enviamos datos al manejador
      $datosArray = $_valoraciones->delete($postBody);
      //delvovemos una respuesta 
      header('Content-Type: application/json');
      if(isset($datosArray["result"]["error_id"])){
          $responseCode = $datosArray["result"]["error_id"];
          http_response_code($responseCode);
      }else{
          http_response_code(200);
      }
      echo json_encode($datosArray);
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}



?>