<?php

require_once './models/Employee.php';
require_once './models/EmployeeType.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Employee as Employee;
use App\Models\EmployeeType as EmployeeType;

class EmployeeController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = Employee::where('uuid', $args['uuid'])->first();
    if($record != null) {
      $employeeType = EmployeeType::find($record->type);
      $record->type = $employeeType->name;
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No existe empleado con ese uuid");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $employees = Employee::all();
    foreach ($employees as $value) {
      $employeeType = EmployeeType::find($value['type']);
      $value->type = $employeeType->name;
    }

    $response->getBody()->write($employees->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(array_key_exists("uuid",$parametros) && $parametros['uuid'] != null &&
    array_key_exists("full_name",$parametros) && $parametros['full_name'] != null &&
    array_key_exists("password",$parametros) && $parametros['password'] != null &&
    array_key_exists("type",$parametros) && $parametros['type'] != null){
      if(EmployeeType::find($parametros['type']) != null){
        if(Employee::where('uuid', $parametros['uuid'])->exists()){
          $response->getBody()->write("Ya existe un usuario con ese uuid.");
        } else {
          $createdId = Employee::insertGetId(json_decode($request->getBody(), true));
          $record = Employee::find($createdId);
          $response->getBody()->write($record->toJson());
        }
      } else {
        $response->getBody()->write("Ese Type no existe.");
      }
    } else {
      $response->getBody()->write("Estan mal cargados los parametros de la consulta.");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = Employee::find($args['id']);
    if ($ret != null) {
      $ret->delete();
      $response->getBody()->write("El siguiente empleado fue dado de baja: ");
      $response->getBody()->write($ret->toJson());
    } else {
      $response->getBody()->write("Empleado no encontrado");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $record = Employee::find($args['id']);
    $parametros = $request->getParsedBody();
    if ($record != null) {
      if(array_key_exists("uuid",$parametros) && $parametros['uuid'] != null){
        if(Employee::where('uuid', $parametros['uuid'])->exists()){
          $response->getBody()->write("Ya existe un usuario con ese uuid.");
        } else {
          $record->uuid = $parametros['uuid'];
        }
      }

      if(array_key_exists("full_name",$parametros) && $parametros['full_name'] != null){
        $record->full_name = $parametros['full_name'];
      }

      if(array_key_exists("password",$parametros) && $parametros['password'] != null){
        $record->password = $parametros['password'];
      }

      if(array_key_exists("type",$parametros) && $parametros['type'] != null){
        $record->type = $parametros['type'];
      }

      $record->save();

      $payload = json_encode(array("mensaje" => "Empleado modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Empleado no encontrado"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function RestaurarUno($request, $response, $args)
  {
    $ret = Employee::onlyTrashed()
    ->firstWhere('uuid', $args['uuid']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("El empleado $ret->uuid fue dado de alta nuevamente");
    } else {
      $response->getBody()->write("No hay un empleado de baja con ese uuid");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
