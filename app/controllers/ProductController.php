<?php

require_once './models/Product.php';
require_once './models/EmployeeType.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Product as Product;
use App\Models\EmployeeType as EmployeeType;

class ProductController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = Product::find($args['id']);
    if($record != null) {
      $employeeType = EmployeeType::find($record->employee_type);
      $record->employee_type = $employeeType->name;
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No existe producto con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $products = Product::all();
    foreach ($products as $value) {
      $employeeType = EmployeeType::find($value['employee_type']);
      $value->employee_type = $employeeType->name;
    }

    $response->getBody()->write($products->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(array_key_exists("name",$parametros) && $parametros['name'] != null &&
    array_key_exists("price",$parametros) && $parametros['price'] != null &&
    array_key_exists("eta",$parametros) && $parametros['eta'] != null &&
    array_key_exists("employee_type",$parametros) && $parametros['employee_type'] != null){
      if(EmployeeType::find($parametros['employee_type']) != null){
        if(Product::where('name', $parametros['name'])->exists()){
          $response->getBody()->write("Ya existe un producto con ese nombre.");
        } else {
          $createdId = Product::insertGetId(json_decode($request->getBody(), true));
          $record = Product::find($createdId);
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
    $ret = Product::find($args['id']);
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
    $record = Product::find($args['id']);
    $parametros = $request->getParsedBody();

    if ($record != null) {
      if(array_key_exists("name",$parametros) && $parametros['name'] != null){
        if(Product::where('name', $parametros['name'])->exists()){
          $response->getBody()->write("Ya existe un producto con ese nombre.");
        } else {
          $record->name = $parametros['name'];
        }
      }

      if(array_key_exists("price",$parametros) && $parametros['price'] != null){
        $record->price = $parametros['price'];
      }

      if(array_key_exists("eta",$parametros) && $parametros['eta'] != null){
        $record->eta = $parametros['eta'];
      }

      if(array_key_exists("employee_type",$parametros) && $parametros['employee_type'] != null){
        $record->employee_type = $parametros['employee_type'];
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
    $ret = Product::onlyTrashed()
    ->firstWhere('id', $args['id']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("El producto $ret->id fue dado de alta nuevamente");
    } else {
      $response->getBody()->write("No hay un empleado de baja con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
