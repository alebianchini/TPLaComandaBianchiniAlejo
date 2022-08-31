<?php

require_once './models/Status.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Status as Status;

class StatusController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = Status::find($args['id']);
    if($record != null) {
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No existe status con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $status = Status::all();

    $response->getBody()->write($status->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(array_key_exists("name",$parametros) && $parametros['name'] != null &&
    array_key_exists("type",$parametros) && $parametros['type'] != null){

      $createdId = Status::insertGetId($parametros);
      $record = Status::find($createdId);
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("Mal ingresados los parametros de la consulta");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = Status::find($args['id']);
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
    $record = Status::find($args['id']);
    $parametros = $request->getParsedBody();
    if ($record !== null) {
      if(array_key_exists("name",$parametros) && $parametros['name'] != null){
        $record->name = $request->getParsedBody()['name'];
        }
      
      if(array_key_exists("type",$parametros) && $parametros['type'] != null){
        $record->type = $request->getParsedBody()['type'];
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
    $ret = Status::onlyTrashed()
    ->firstWhere('id', $args['id']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("El Status $ret->id fue dado de alta nuevamente");
    } else {
      $response->getBody()->write("No hay un status de baja con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
