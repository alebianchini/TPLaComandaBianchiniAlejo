<?php
require_once './interfaces/IApiUsable.php';
require_once './models/Usuario.php';
require_once './models/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];
    $clave = $parametros['clave'];

    $newUsr = new Usuario();
    $newUsr->mail = $mail;
    $newUsr->tipo = $tipo;
    $newUsr->clave = $clave;
    $newId = $newUsr->crearUsuario();

    $payload = json_encode(array("mensaje" => "Usuario creado con exito, con el id: " . $newId));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ChequearUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $payload = json_encode(array("tipo" => $parametros['tipo']));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];
    $clave = $parametros['clave'];

    $usuario = Usuario::obtenerUsuario($mail, $tipo, $clave);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $listaUsuarios = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $listaUsuarios));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $body = $request->getBody();
    $parametros = json_decode($body, true);
    $id = $parametros['id'];
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];
    $clave = $parametros['clave'];

    if ($id == null || $mail == null || $tipo == null || $clave == null) {
      $response->getBody()->write("Los Parametros estan mal cargados");
      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
    }
    $usuario = new Usuario();
    $usuario->mail = $mail;
    $usuario->tipo = $tipo;
    $usuario->clave = $clave;
    Usuario::modificarUsuario($usuario, $id);

    $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $body = $request->getBody();
    $parametros = json_decode($body, true);
    $usuarioId = $parametros["id"];

    if (Usuario::bajarUsuario($usuarioId) > 0) {
      $payload = json_encode(array("mensaje" => "Usuario bajado con exito."));
    } else {
      $payload = json_encode(array("mensaje" => "No hay usuario con ese id."));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function Login($request, $response, $args)
  {
    $dataRequest = $request->getParsedBody();
    $datos = array('mail' => $dataRequest['mail'], 'tipo' => $dataRequest['tipo']);
    $token = AutentificadorJWT::CrearToken($datos);

    $payload = "Usuario logueado, tome su token: ";
    $payload .= json_encode(array('jwt' => $token));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}
