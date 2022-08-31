<?php

require_once './models/Survey.php';
require_once './models/Order.php';
require_once './models/Table.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Survey as Survey;
use App\Models\Order as Order;
use App\Models\Table as Table;

class SurveyController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = Survey::select('survey.*', 'orders.number as associated_order', 'tables.number as associated_table')
    ->join("orders", function($join){
      $join->on("survey.associated_order", "=", "orders.id");
    })
    ->join("tables", function($join){
      $join->on("survey.associated_table", "=", "tables.id");
    })
    ->where('survey.id', $args['id'])
    ->get();

    if($record != null) {
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No exoste survey con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $records = Survey::select('survey.*', 'orders.number as associated_order', 'tables.number as associated_table')
    ->join("orders", function($join){
      $join->on("survey.associated_order", "=", "orders.id");
    })
    ->join("tables", function($join){
      $join->on("survey.associated_table", "=", "tables.id");
    })
    ->get();

    $response->getBody()->write($records->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(array_key_exists("table_points",$parametros) && $parametros['table_points'] != null &&
    array_key_exists("restaurant_points",$parametros) && $parametros['restaurant_points'] != null &&
    array_key_exists("cook_points",$parametros) && $parametros['cook_points'] != null &&
    array_key_exists("waiter_points",$parametros) && $parametros['waiter_points'] != null &&
    array_key_exists("comment",$parametros) && $parametros['comment'] != null &&
    array_key_exists("associated_table",$parametros) && $parametros['associated_table'] != null &&
    array_key_exists("associated_order",$parametros) && $parametros['associated_order'] != null){

      $table = Table::where('number',$parametros['associated_table'])->first();
      $order = Order::where('number',$parametros['associated_order'])->first();

      $surveyToCreate = $request->getParsedBody();
      $surveyToCreate['associated_table'] = $table['id'];
      $surveyToCreate['associated_order'] = $order['id'];

      $createdId = Survey::insertGetId($surveyToCreate);

      $record = Survey::select('survey.*', 'orders.number as associated_order', 'tables.number as associated_table')
      ->join("orders", function($join){
        $join->on("survey.associated_order", "=", "orders.id");
      })
      ->join("tables", function($join){
        $join->on("survey.associated_table", "=", "tables.id");
      })
      ->where('survey.id', $createdId)
      ->get();
      $response->getBody()->write($record->toJson());

    } else {
      $response->getBody()->write("Estan mal cargados los parametros de la consulta.");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = Survey::find($args['id']);
    if ($ret != null) {
      $ret->delete();
      $response->getBody()->write("La siguiente encuesta fue dada de baja: ");
      $response->getBody()->write($ret->toJson());
    } else {
      $response->getBody()->write("Encuesta no encontrada");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $record = Survey::find($args['id']);
    $parametros = $request->getParsedBody();

    if ($record != null) {
      if(array_key_exists("table_points",$parametros) && $parametros['table_points'] != null){
          $record->table_points = $parametros['table_points'];
      }

      if(array_key_exists("restaurant_points",$parametros) && $parametros['restaurant_points'] != null){
        $record->restaurant_points = $parametros['restaurant_points'];
      }

      if(array_key_exists("cook_points",$parametros) && $parametros['cook_points'] != null){
        $record->cook_points = $parametros['cook_points'];
      }

      if(array_key_exists("waiter_points",$parametros) && $parametros['waiter_points'] != null){
        $record->waiter_points = $parametros['waiter_points'];
      }

      if(array_key_exists("comment",$parametros) && $parametros['comment'] != null){
        $record->comment = $parametros['comment'];
      }

      if(array_key_exists("associated_table",$parametros) && $parametros['associated_table'] != null){
        $record->associated_table = $parametros['associated_table'];
      }

      if(array_key_exists("associated_order",$parametros) && $parametros['associated_order'] != null){
        $record->associated_order = $parametros['associated_order'];
      }

      $record->save();

      $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Producto no encontrado"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function RestaurarUno($request, $response, $args)
  {
    $ret = Survey::onlyTrashed()
    ->firstWhere('id', $args['id']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("La encuesta $ret->id fue dada de alta nuevamente");
    } else {
      $response->getBody()->write("No hay una encuesta de baja con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}
