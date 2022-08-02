<?php
require_once './models/Usuario.php';
require_once './models/AutentificadorJWT.php';

use GuzzleHttp\Psr7\Response;

class MdwCore
{
    public static function VerificarUsuario($request, $handler)
    {
        $dataRequest = $request->getParsedBody();
        $response = new Response();

        $mail = $dataRequest['mail'];
        $tipo = $dataRequest['tipo'];
        $clave = $dataRequest['clave'];
        $usuario = Usuario::obtenerUsuario($mail, $tipo, $clave);

        if ($usuario != null) {
            if (!$usuario->consultarBajaUsuario() != null) {
                $response = $handler->handle($request);
            } else {
                $response->getBody()->write(json_encode(["ERROR" => "Usuario dado de baja."]));
                $response = $response->withStatus(403);
            }
        } else {
            $response->getBody()->write(json_encode(["ERROR" => "Credenciales incorrectas."]));
            $response = $response->withStatus(403);
        }
        return $response;
    }
}
