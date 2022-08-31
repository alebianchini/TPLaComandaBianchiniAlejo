<?php

use Slim\Psr7\Response;

require_once './models/AutentificadorJWT.php';

class MdwJWT
{
    public static function ValidarToken($request, $handler)
    {
        $response = new Response();
        $headerPeticion = $request->getHeaderLine('Authorization');
        if (empty($headerPeticion)) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(401);
        }

        $token = trim(explode("Bearer", $headerPeticion)[1]);

        if(AutentificadorJWT::VerificarToken($token) == 1){
            $response->getBody()->write("El Token ingresado es invalido.");
            return $response->withStatus(401);
        }
        $response = $handler->handle($request);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarTokenSocio($request, $handler)
    {

        $response = new Response();
        $headerPeticion = $request->getHeaderLine('Authorization');
        if (empty($headerPeticion)) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(401);
        }

        $token = trim(explode("Bearer", $headerPeticion)[1]);

        if(AutentificadorJWT::VerificarToken($token) == 1){
            $response->getBody()->write("El Token ingresado es invalido.");
            return $response->withStatus(401);
        }
        $dataToken = AutentificadorJWT::ObtenerData($token);
        if ($dataToken->type == "SOCIO") {
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write("El usuario logueado no es socio.");
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarTokenMozo($request, $handler)
    {
        $response = new Response();

        if (empty($request->getHeaderLine('Authorization'))) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(420);
        }

        if (MdwJWT::ValidarTokenType($request, $handler, 'MOZO')) {
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write("El usuario logueado no es socio.");
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarTokenBartender($request, $handler)
    {
        $response = new Response();

        if (empty($request->getHeaderLine('Authorization'))) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(420);
        }

        if (MdwJWT::ValidarTokenType($request, $handler, 'BARTENDER')) {
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write("El usuario logueado no es socio.");
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarTokenCocinero($request, $handler)
    {
        $response = new Response();

        if (empty($request->getHeaderLine('Authorization'))) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(420);
        }

        if (MdwJWT::ValidarTokenType($request, $handler, 'COCINERO')) {
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write("El usuario logueado no es socio.");
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarTokenCervercero($request, $handler)
    {
        $response = new Response();

        if (empty($request->getHeaderLine('Authorization'))) {
            $response->getBody()->write("El header Authorization esta vacio.");
            return $response->withStatus(420);
        }

        if (MdwJWT::ValidarTokenType($request, $handler, 'CERVERCERO')) {
            $response = $handler->handle($request);
        } else {
            $response->getBody()->write("El usuario logueado no es socio.");
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    private static function ValidarTokenType($request, $handler, $type)
    {
        AutentificadorJWT::VerificarToken(trim(explode("Bearer", $request->getHeaderLine('Authorization'))[1]));
        $dataToken = AutentificadorJWT::ObtenerData(trim(explode("Bearer", $request->getHeaderLine('Authorization'))[1]));

        if ($dataToken->type == $type) {
            return true;
        }

        return false;
    }
}
