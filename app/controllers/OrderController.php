<?php

require_once './models/Order.php';
require_once './models/Product.php';
require_once './models/OrderItem.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Order as Order;
use App\Models\Product as Product;
use App\Models\OrderItem as OrderItem;

class OrderController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = Order::where('number', $args['number'])->first();
    if($record != null) {
      $orderItems = OrderItem::where('order_id', $record->id);
      $record->items = $orderItems;
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("No existe una orden con ese numero");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $products = Order::all();
    foreach ($products as $value) {
      $employeeType = OrderItem::find($value['employee_type']);
      $value->employee_type = $employeeType->name;
    }

    $response->getBody()->write($products->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    if(array_key_exists("waiter",$parametros) && $parametros['waiter'] != null &&
    array_key_exists("associated_table",$parametros) && $parametros['associated_table'] != null &&
    array_key_exists("amount",$parametros) && $parametros['amount'] != null &&
    array_key_exists("items",$parametros) && $parametros['items'] != null ){

      $recordToCreate = $request->getParsedBody();
      $receivedItems = $parametros['items'];
      unset($recordToCreate['items']);
      $recordToCreate['number'] = substr(str_shuffle('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
      $recordToCreate['status'] = 1;
      $eta = 0;
      $etaTime = 0;

      $createdOrderId = Order::insertGetId($recordToCreate);
      $record = Order::find($createdOrderId);

      foreach ($receivedItems as $value) {
        $productItem = Product::where('name', $value['name'])->first();
        $orderItemToCreate = array('product' => $productItem->id, 'status' => 1, "order_id" => $createdOrderId);

        if($eta < $productItem->eta){
          $eta = $productItem->eta;
        }

        OrderItem::insert($orderItemToCreate);  
      }

      $time = new DateTime('America/Argentina/Buenos_Aires');
      $time->add(new DateInterval('PT' . $eta . 'M'));

      $record->eta = $time->format('Y-m-d H:i');
      $record->save();

      $response->getBody()->write($record->toJson());

    } else {
      $response->getBody()->write("Estan mal cargados los parametros de la consulta.");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = Order::find($args['id']);
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
    $record = Order::find($args['id']);
    $parametros = $request->getParsedBody();

    if ($record != null) {
      if(array_key_exists("name",$parametros) && $parametros['name'] != null){
        if(Order::where('name', $parametros['name'])->exists()){
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
    $ret = Order::onlyTrashed()
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
