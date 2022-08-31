<?php

require_once './models/Employee.php';
require_once './models/EmployeeType.php';

use App\Models\Employee as Employee;
use App\Models\EmployeeType as EmployeeType;

class LoginController
{
  public function Login($request, $response)
  {
    $dataRequest = $request->getParsedBody();

    $record = Employee::where('uuid', $dataRequest['uuid'])->first();
    $record2 = EmployeeType::find($record['type']);

    $datos = array('uuid' => $dataRequest['uuid'], 'type' => $record2['name']);
    $token = AutentificadorJWT::CrearToken($datos);

    $payload = "Usuario logueado, tome su token: ";
    $payload .= json_encode(array('jwt' => $token));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
}