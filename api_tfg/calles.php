<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/calles.class.php';

$_respuesta = new respuestas;
$_calles = new calles;

if($_SERVER['REQUEST_METHOD']  == "GET"){
    if(isset($_GET['id'])){
        $idCalles = $_GET['id'];
        $datosCalle = $_calles->obtenerCalle($idCalles);
        header("Content-Type: application/json");
        echo json_encode($datosCalle);
        http_response_code(200);
    }else{
        $listacalles = $_calles->listaCalles();
        header("Content-Type: application/json");
        echo json_encode($listacalles);
        http_response_code(200);
    }

}else if($_SERVER['REQUEST_METHOD']  == "POST"){
        //recibimos los datos enviados
        $postBody = stripslashes(html_entity_decode(file_get_contents("php://input")));
        //enviamos los datos al manejador
        $datosArray = $_calles->post($postBody);
        //delvovemos una respuesta 
         header('Content-Type: application/json');
         echo json_encode($datosArray);
         if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }

}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos datos al manejador
    $datosArray = $_calles->put($postBody);
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
      if(isset($headers["token"]) && isset($headers["idCalle"])){
          //recibimos los datos enviados por el header
          $send = [
              "token" => $headers["token"],
              "idCalle" =>$headers["idCalle"]
          ];
          $postBody = json_encode($send);
      }else{
          //recibimos los datos enviados
          $postBody = file_get_contents("php://input");
      }
      
      //enviamos datos al manejador
      $datosArray = $_calles->delete($postBody);
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