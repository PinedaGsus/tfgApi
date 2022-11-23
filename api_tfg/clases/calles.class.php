<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class calles extends conexion {

    private $table = "calles";
    private $idCalle = "";
    private $latitud = "";
    private $longitud = "";
    private $nombre = "";
    private $tipoVia = "";
    private $token = "";
//912bc00f049ac8464472020c5cd06759

    //Consulta paginada de las calles
    public function listaCallesPaginada($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT idCalle,longitud,latitud,nombre,tipoVia FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function listaCalles(){
        $query = "SELECT idCalle,longitud,latitud,nombre,tipoVia FROM " . $this->table;
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    //Calle unica (obtener datos)
    public function obtenerCalle($id){
        $query = "SELECT * FROM " . $this->table . " WHERE idCalle = '$id'";
        return parent::obtenerDatos($query);
    }

    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        
        if(!isset($datos['token'])){
                return $_respuestas->error_401($datos);
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['idCalle']) || !isset($datos['longitud']) || !isset($datos['latitud'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idCalle = $datos['idCalle'];
                    $this->longitud = $datos['longitud'];
                    $this->latitud = $datos['latitud'];
                    if(isset($datos['nombre'])) { $this->nombre = $datos['nombre']; }
                    if(isset($datos['tipoVia'])) { $this->tipoVia = $datos['tipoVia']; }
                    $resp = $this->insertarCalle();
                    //throw new Exception($response);
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idCalle" => $resp
                        );
                        return [$respuesta];
                    }else{
                        return $_respuestas->error_500($resp);
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }
    }


    private function insertarCalle(){
        $query = "INSERT INTO " . $this->table . " (idCalle,latitud,longitud,nombre,tipoVia)
        values
        ('" . $this->idCalle . "','" . $this->latitud . "','" . $this->longitud . "','" . $this->nombre . "','" . $this->tipoVia ."')"; 
        $resp = parent::nonQueryId($query);
        //$check = obtenerCalle($this->idCalle);
        //throw new Exception($resp);
        if($resp == 0){
             return $this->idCalle;
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
                if(!isset($datos['idCalle'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idCalle = $datos['idCalle'];
                    if(isset($datos['longitud'])) { $this->longitud = $datos['longitud']; }
                    if(isset($datos['latitud'])) { $this->latitud = $datos['latitud']; }
                    if(isset($datos['nombre'])) { $this->nombre = $datos['nombre']; }
                    if(isset($datos['tipoVia'])) { $this->nombre = $datos['tipoVia']; }
                    $resp = $this->modificarCalle();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idCalle" => $this->idCalle
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


    private function modificarCalle(){
        $query = "UPDATE " . $this->table . " SET longitud ='" . $this->longitud . "', latitud = '" . $this->latitud . "',nombre = '" . $this->nombre . "',tipoVia = '" . $this->tipoVia ."' WHERE idCalle = '" . $this->idCalle . "'"; 
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

                if(!isset($datos['idCalle'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idCalle = $datos['idCalle'];
                    $resp = $this->eliminarCalle();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idCalle" => $this->idCalle
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


    private function eliminarCalle(){
        $query = "DELETE FROM " . $this->table . " WHERE idCalle= '" . $this->idCalle . "'";
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