<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class noticias extends conexion {

    private $table = "noticias";
    private $idNoticia = "";
    private $titulo = "";
    private $cuerpo = "";
    private $autor = "";
    private $token = "";
//912bc00f049ac8464472020c5cd06759

    //Consulta paginada de las noticias
    public function listaNoticiasPaginada($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT idNoticia,cuerpo,titulo,autor FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function listaNoticias(){
        $query = "SELECT idNoticia,cuerpo,titulo,autor FROM " . $this->table;
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    //Noticia unica (obtener datos)
    public function obtenerNoticia($id){
        $query = "SELECT * FROM " . $this->table . " WHERE idNoticia = '$id'";
        return parent::obtenerDatos($query);
    }

    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
                return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['cuerpo']) || !isset($datos['titulo'])){
                    return $_respuestas->error_400();
                }else{
                    $this->cuerpo = $datos['cuerpo'];
                    $this->titulo = $datos['titulo'];
                    if(isset($datos['autor'])) { $this->autor = $datos['autor']; }
                    $resp = $this->insertarNoticia();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idNoticia" => $resp
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }
    }


    private function insertarNoticia(){
        $query = "INSERT INTO " . $this->table . " (titulo,cuerpo,autor)
        values
        ('" . $this->titulo . "','" . $this->cuerpo . "','" . $this->autor ."')"; 
        $resp = parent::nonQueryId($query);
        if($resp){
             return $resp;
        }else{
            return 0;
        }
    }
    
    public function put($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['idNoticia'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idNoticia = $datos['idNoticia'];
                    if(isset($datos['cuerpo'])) { $this->cuerpo = $datos['cuerpo']; }
                    if(isset($datos['titulo'])) { $this->titulo = $datos['titulo']; }
                    if(isset($datos['autor'])) { $this->autor = $datos['autor']; }
                    $resp = $this->modificarNoticia();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idNoticia" => $this->idNoticia
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }


    }


    private function modificarNoticia(){
        $query = "UPDATE " . $this->table . " SET cuerpo ='" . $this->cuerpo . "', titulo = '" . $this->titulo . "',autor = '" . $this->autor ."' WHERE idNoticia = '" . $this->idNoticia . "'"; 
        $resp = parent::nonQuery($query);
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }


    public function delete($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                if(!isset($datos['idNoticia'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idNoticia = $datos['idNoticia'];
                    $resp = $this->eliminarNoticia();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idNoticia" => $this->idNoticia
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }



     
    }


    private function eliminarNoticia(){
        $query = "DELETE FROM " . $this->table . " WHERE idNoticia= '" . $this->idNoticia . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1 ){
            return $resp;
        }else{
            return 0;
        }
    }


    private function buscarToken(){
        $query = "SELECT  TokenId,UsuarioId,Estado from usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }


    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }
}

?>