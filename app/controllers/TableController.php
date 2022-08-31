<?php

require_once './models/Table.php';
require_once './models/Status.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Table as Table;
use App\Models\Status as Status;

class TableController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = Table::where('number', $args['number'])->first();
    if($record != null) {
      $status = Status::find($record->status);
      $record->status = $status->name;
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No existe mesa con ese numero");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $tables = Table::all();
    foreach ($tables as $value) {
      $status = Status::find($value['status']);
      $value->status = $status->name;
    }

    $response->getBody()->write($tables->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    if(array_key_exists("number",$request->getParsedBody()) && $request->getParsedBody()['number'] != null){
      $toInsertTable = $request->getParsedBody();
      $toInsertTable['status'] = 4;
      if(Table::firstWhere('number', $toInsertTable['number']) != null){
        $response->getBody()->write("Ya existe una mesa con ese numero");
      } else {
        $createdId = Table::insertGetId($toInsertTable);
        $record = Table::find($createdId);
        $response->getBody()->write($record->toJson());
      }
    } else {
      $response->getBody()->write("Mal ingresados los parametros de la consulta");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = Table::find($args['id']);

    if ($ret != null) {
      $ret->delete();
      $response->getBody()->write($ret->toJson());
    } else {
      $response->getBody()->write("Mesa no encontrada");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $record = Table::find($args['id']);

    if ($record !== null) {
      if(array_key_exists("number",$request->getParsedBody()) && $request->getParsedBody()['number'] != null){
        if(Table::firstWhere('number', $request->getParsedBody()['number']) != null){
          $response->getBody()->write("Ya existe una mesa con ese numero");
        } else {
          $record->number = $request->getParsedBody()['number'];
        }
      }
      
      if(array_key_exists("status",$request->getParsedBody()) && $request->getParsedBody()['status'] != null){
        $status = Status::find($request->getParsedBody()['status']);
        if($status != null && $status->type == "table"){
          $record->status = $request->getParsedBody()['status'];
        } else {
          $response->getBody()->write("Ese Status no existe.");
        }
      };

      $record->save();

      $payload = $record->toJson();
    } else {
      $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function RestaurarUno($request, $response, $args)
  {
    $ret = Table::onlyTrashed()
    ->firstWhere('id', $args['id']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("La mesa $ret->id fue dada de alta nuevamente");
    } else {
      $response->getBody()->write("No hay un empleado de baja con ese uuid");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
