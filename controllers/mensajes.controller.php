<?php

class MensajesController {

  private $db = null;

  function __construct($conexion) {
    $this->db = $conexion;
  }

  public function leerMensajes() {
    if (IDUSER) {
      $eval = "SELECT * FROM mensajes WHERE id_destinatario=?";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([IDUSER]);
      $resultado = $peticion->fetchAll(PDO::FETCH_OBJ);
      exit(json_encode($resultado));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));
    }
  }
  
  public function enviarMensaje() {
    if (IDUSER) {
      $mensaje = json_decode(file_get_contents("php://input"));
      if (!isset($mensaje->id_destinatario) || !isset($mensaje->mensaje)) {
        http_response_code(400);
        exit(json_encode(["error" => "No se han enviado todos los parametros"]));
      }
      $eval = "INSERT INTO mensajes(id_remitente, id_destinatario, mensaje) VALUES(?,?,?)";
      $peticion = $this->db->prepare($eval);
      $peticion->execute([IDUSER, $mensaje->id_destinatario, $mensaje->mensaje]);
      http_response_code(201);
      exit(json_encode("Mensaje enviado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));
    }
  }

  public function eliminarMensaje($id) {
    if(empty($id)) {
      http_response_code(400);
      exit(json_encode(["error" => "Peticion mal formada"]));    
    }
    if(IDUSER) {
      $eval = "DELETE FROM mensajes WHERE id=? AND id_destinatario=?";
      $peticion = $this->db->prepare($eval);
      $resultado = $peticion->execute([$id,IDUSER]);
      http_response_code(200);
      exit(json_encode("Mensaje eliminado correctamente"));
    } else {
      http_response_code(401);
      exit(json_encode(["error" => "Fallo de autorizacion"]));            
    }
  }
}