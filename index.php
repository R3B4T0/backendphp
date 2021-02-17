<?php
//Importamos las librerias necesarias.
require_once 'config/db.php';
require_once 'config/cors.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

//Guardamos la url para buscar el controlador y ponemos mensaje de bienvenida.
if(!isset($_GET['url'])) {
  exit(json_encode(["Bienvenido al Backend con routes"]));
}

$url = $_GET['url'];

//Preparamos la conexion con la base de datos
$bd = new db();
$conexion = $bd->getConnection();

//Comprueba si hay algún token valido en la cabecera y obtiene el ID del USER
$idUser = null;
if(!empty($_SERVER['HTTP_AUTHORIZATION'])) {
  $jwt = $_SERVER['HTTP_AUTHORIZATION'];
  try {
    $JWTraw = JWT::decode($jwt, $bd->getClave(), array('HS256'));
    $idUser = $JWTraw->id;
  } catch (Exception $e) { }
}

//Guardamos las variables globales. IDUSER, Metodo, CJWT, DIRECTORIO ROOT.
define('IDUSER', $idUser);
define('METODO', $_SERVER["REQUEST_METHOD"]);
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CJWT', $bd->getClave());

//Procesamos la ruta y los metodos.
$control = explode('/',$url);
switch($control[0]) {

  case "user":
    require_once("controllers/user.controller.php");
    $user = new UserController($conexion);
    switch(METODO) {
      case "GET":
        switch($control[1]) {
          case "list":
            $user->listarUser();
            break;
          case "":
            $user->leerPerfil();
            break;
        }
        break;

      case "POST":
        switch($control[1]) {
          case "login":
            $user->hacerLogin();
            break;
          case "image":
            $user->subirAvatar();
            break;
          case "":
            $user->registrarUser();
        }
        break;

      case "PUT":
        $user->editarUser();
        break;

      case "DELETE":
        $user->eliminarUser();
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));  
    }  
    break;

  case "notas":
    require_once("controllers/notas.controller.php");
    $notas = new NotasController($conexion);
    switch(METODO) {
      case "GET":
        $notas->obtenerNotas();
        break;

      case "POST":
        $notas->publicarNota();
        break;

      case "PUT":
        $notas->editarNota();
        break;

      case "DELETE":
        $notas->eliminarNota($control[1]);
        break;

      default: exit(json_encode(["Bienvenido al Backend con routes"]));
    }
    break;

    case 'mensajes':
      require_once("controllers/mensajes.controller.php");
      $mensajes = new MensajesController($conexion);
      switch (METODO) {
        case 'GET':
          $mensajes->leerMensajes();
          break;
        
        case 'POST':
          $mensajes->enviarMensaje();
          break;
        
        case 'DELETE':
          $mensajes->eliminarMensaje($control[1]);
          break;
        
        default: exit(json_encode(["Bienvenido al Backend con routes"]));
      }
      break;

    default:
    exit(json_encode(["Bienvenido al Backend con routes"]));


}

