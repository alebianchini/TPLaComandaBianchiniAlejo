<?php

require_once './models/EmployeeType.php';
require_once './interfaces/IApiUsable.php';

use App\Models\EmployeeType as EmployeeType;

class EmployeeTypeController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = EmployeeType::find($args['id']);
    if($record != null) {
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No existe EmployeeType con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $records = EmployeeType::all();

    $response->getBody()->write($records->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(array_key_exists("name",$parametros) && $parametros['name'] != null){

      $createdId = EmployeeType::insertGetId($parametros);
      $record = EmployeeType::find($createdId);
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("Mal ingresados los parametros de la consulta");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = EmployeeType::find($args['id']);
    if ($ret != null) {
      $ret->delete();
      $response->getBody()->write($ret->toJson());
    } else {
      $response->getBody()->write("Id no encontrado");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $record = EmployeeType::find($args['id']);
    $parametros = $request->getParsedBody();
    if ($record !== null) {
      if(array_key_exists("name",$parametros) && $parametros['name'] != null){
        $record->name = $request->getParsedBody()['name'];
        }

      $record->save();
      $payload = $record->toJson();
    } else {
      $payload = json_encode(array("mensaje" => "id no encontrado"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function RestaurarUno($request, $response, $args)
  {
    $ret = EmployeeType::onlyTrashed()
    ->firstWhere('id', $args['id']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("El EmployeeType $ret->id fue dado de alta nuevamente");
    } else {
      $response->getBody()->write("No hay un EmployeeType de baja con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
