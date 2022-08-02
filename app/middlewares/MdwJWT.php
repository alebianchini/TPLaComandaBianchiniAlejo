<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/AutentificadorJWT.php';

class MdwJWT
{
    public static function ValidarToken($request, $handler)
    {

        $headerPeticion = $request->getHeaderLine('Authorization');
        if (empty($headerPeticion)) {
            $response = new Response();
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(420);
        }

        $token = trim(explode("Bearer", $headerPeticion)[1]);

        AutentificadorJWT::VerificarToken($token);
        $response = $handler->handle($request);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarTokenAdmin($request, $handler)
    {

        $response = new Response();
        $headerPeticion = $request->getHeaderLine('Authorization');
        if (empty($headerPeticion)) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(420);
        }

        $token = trim(explode("Bearer", $headerPeticion)[1]);

        AutentificadorJWT::VerificarToken($token);
        $dataToken = AutentificadorJWT::ObtenerData($token);
        if ($dataToken->tipo == "admin") {
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write("El usuario logueado no es admin.");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
