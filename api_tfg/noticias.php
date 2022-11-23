<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/noticias.class.php';

$_respuesta = new respuestas;
$_noticias = new noticias;

if($_SERVER['REQUEST_METHOD']  == "GET"){
    if(isset($_GET['id'])){
        $idNoticias = $_GET['id'];
        $datosNoticia = $_noticias->obtenerNoticia($idNoticias);
        header("Content-Type: application/json");
        echo json_encode($datosNoticia);
        http_response_code(200);
    }else{
        $listanoticias = $_noticias->listaNoticias();
        header("Content-Type: application/json");
        echo json_encode($listanoticias);
        http_response_code(200);
    }

}else if($_SERVER['REQUEST_METHOD']  == "POST"){
        //recibimos los datos enviados
        $postBody = file_get_contents("php://input");
        //enviamos los datos al manejador
        $datosArray = $_noticias->post($postBody);
        //delvovemos una respuesta 
         header('Content-Type: application/json');
         if(isset($datosArray["result"]["error_id"])){
             $responseCode = $datosArray["result"]["error_id"];
             http_response_code($responseCode);
         }else{
             http_response_code(200);
         }
         echo json_encode($datosArray);

}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos datos al manejador
    $datosArray = $_noticias->put($postBody);
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
      if(isset($headers["token"]) && isset($headers["idNoticia"])){
          //recibimos los datos enviados por el header
          $send = [
              "token" => $headers["token"],
              "idNoticia" =>$headers["idNoticia"]
          ];
          $postBody = json_encode($send);
      }else{
          //recibimos los datos enviados
          $postBody = file_get_contents("php://input");
      }
      
      //enviamos datos al manejador
      $datosArray = $_noticias->delete($postBody);
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