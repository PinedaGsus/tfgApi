<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class valoraciones extends conexion {

    private $table = "valoraciones";
    private $idValoraciones = "";
    private $idCalle = "";
    private $fechaValoracion = "";
    private $diaNoche = "";
    private $luminosidad = "";
    private $salidas = "";
    private $transito = "";
    private $idUsuario = "";
    private $token = "";
//912bc00f049ac8464472020c5cd06759

    //Consulta paginada de las valoraciones
    public function listaValoracionesPaginada($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT idValoraciones,fechaValoracion,idCalle,diaNoche,luminosidad,salidas,transito,idUsuario FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function listaValoraciones(){
        $query = "SELECT idValoraciones,fechaValoracion,idCalle,diaNoche,luminosidad,salidas,transito,idUsuario FROM " . $this->table;
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    //Valoracion unica (obtener datos)
    public function obtenerValoracion($id){
        $query = "SELECT * FROM " . $this->table . " WHERE idValoraciones = '$id'";
        return parent::obtenerDatos($query);
    }

    //Valoracion unica (obtener datos)
    public function obtenerValoracionesUsuario($idUsuario){
        $query = "SELECT * FROM " . $this->table . " WHERE idUsuario = '$idUsuario'";
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
                if(!isset($datos['idCalle'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idCalle = $datos['idCalle'];
                    if(isset($datos['diaNoche'])) { $this->diaNoche = $datos['diaNoche']; }
                    if(isset($datos['luminosidad'])) { $this->luminosidad = $datos['luminosidad']; }
                    if(isset($datos['salidas'])) { $this->salidas = $datos['salidas']; }
                    if(isset($datos['fechaValoracion'])) { $this->fechaValoracion = $datos['fechaValoracion']; }
                    if(isset($datos['transito'])) { $this->transito = $datos['transito']; }
                    if(isset($datos['idUsuario'])) { $this->idUsuario = $datos['idUsuario']; }
                    $resp = $this->insertarValoracion();
                    
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idValoraciones" => $resp
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


    private function insertarValoracion(){
        $query = "INSERT INTO " . $this->table . " (idvaloraciones,idCalle,fechaValoracion,diaNoche,luminosidad,salidas,transito,idUsuario)
        values
        ('0','" . $this->idCalle . "',STR_TO_DATE('". $this->fechaValoracion ."', '%m-%d-%Y %H:%i:%s'),'" . $this->diaNoche . "','$this->luminosidad','" . $this->salidas . "','" . $this->transito . "','" . $this->idUsuario ."')"; 
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
                if(!isset($datos['idValoraciones'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idValoraciones = $datos['idValoraciones'];
                    if(isset($datos['fechaValoracion'])) { $this->fechaValoracion = $datos['fechaValoracion']; }
                    if(isset($datos['idCalle'])) { $this->idCalle = $datos['idCalle']; }
                    if(isset($datos['diaNoche'])) { $this->diaNoche = $datos['diaNoche']; }
                    if(isset($datos['luminosidad'])) { $this->diaNoche = $datos['luminosidad']; }
                    if(isset($datos['salidas'])) { $this->luminosidad = $datos['salidas']; }
                    if(isset($datos['transito'])) { $this->luminosidad = $datos['transito']; }
                    if(isset($datos['idUsuario'])) { $this->luminosidad = $datos['idUsuario']; }
                    $resp = $this->modificarValoracion();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idValoraciones" => $this->idValoraciones
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


    private function modificarValoracion(){
        $query = "UPDATE " . $this->table . " SET fechaValoracion ='" . $this->fechaValoracion . "', idCalle = '" . $this->idCalle . "',diaNoche = '" . $this->diaNoche . "',luminosidad = '" . $this->luminosidad . "',salidas = '" . $this->salidas . "',transito = '" . $this->transito . "',idUsuario = '" . $this->idUsuario ."' WHERE idValoraciones = '" . $this->idValoraciones . "'"; 
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

                if(!isset($datos['idValoraciones'])){
                    return $_respuestas->error_400();
                }else{
                    $this->idValoraciones = $datos['idValoraciones'];
                    $resp = $this->eliminarValoracion();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idValoraciones" => $this->idValoraciones
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


    private function eliminarValoracion(){
        $query = "DELETE FROM " . $this->table . " WHERE idValoraciones= '" . $this->idValoraciones . "'";
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