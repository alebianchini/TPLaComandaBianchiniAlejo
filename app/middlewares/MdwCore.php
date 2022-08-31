<?php
require_once './models/AutentificadorJWT.php';
require_once './models/Employee.php';

use App\Models\Employee as Employee;

use GuzzleHttp\Psr7\Response;

class MdwCore
{
    public static function VerificarUsuarioV2($request, $handler)
    {
        $response = new Response();
        $usuario = Employee::where('uuid',$request->getParsedBody()['uuid'])
                            ->where('password', $request->getParsedBody()['password'])
                            ->withTrashed()->first();

        if ($usuario !== null) {
            if ($usuario['deleted_at'] !== null) {
                $response->getBody()->write(json_encode(["ERROR" => "Usuario dado de baja."]));
                $response = $response->withStatus(403);
            } else {
                $response = $handler->handle($request);
            }
        } else {
            $response->getBody()->write(json_encode(["ERROR" => "Credenciales incorrectas."]));
            $response = $response->withStatus(401);
        }
        return $response;
    }
}
