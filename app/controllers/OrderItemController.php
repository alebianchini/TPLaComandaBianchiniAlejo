<?php

require_once './models/Status.php';
require_once './models/OrderItem.php';
require_once './models/Order.php';
require_once './models/Product.php';
require_once './models/EmployeeType.php';
require_once './interfaces/IApiUsable.php';

use App\Models\Status as Status;
use App\Models\OrderItem as OrderItem;
use App\Models\Order as Order;
use App\Models\Product as Product;
use App\Models\EmployeeType as EmployeeType;

class OrderItemController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    $record = OrderItem::find($args['id']);
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
    $status = OrderItem::all();

    $response->getBody()->write($status->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    if(array_key_exists("product",$parametros) && $parametros['product'] != null &&
    array_key_exists("status",$parametros) && $parametros['status'] != null &&
    array_key_exists("order_id",$parametros) && $parametros['order_id'] != null){
      $createdId = OrderItem::insertGetId($parametros);
      $record = OrderItem::find($createdId);
      $response->getBody()->write($record->toJson());
    } else {
      $response->getBody()->write("Mal ingresados los parametros de la consulta");
    }
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $ret = OrderItem::find($args['id']);
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
    $record = OrderItem::find($args['id']);
    $parametros = $request->getParsedBody();
    if ($record !== null) {
      if(array_key_exists("product",$parametros) && $parametros['product'] != null){
        $record->product = $request->getParsedBody()['product'];
      }
      
      if(array_key_exists("status",$parametros) && $parametros['status'] != null){
        $record->status = $request->getParsedBody()['status'];
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
    $ret = OrderItem::onlyTrashed()
    ->firstWhere('id', $args['id']);
    if ($ret != null) {
      $ret->restore();
      $response->getBody()->write("El Item $ret->id fue dado de alta nuevamente");
    } else {
      $response->getBody()->write("No hay un item de baja con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosPorTipoDeEmpleado($request, $response, $args)
  {
    //$employeeType = AutentificadorJWT::ObtenerEmployeeType(trim(explode("Bearer", $request->getHeaderLine('Authorization'))[1]));
    $employeeType = EmployeeType::where("name", AutentificadorJWT::ObtenerEmployeeType(trim(explode("Bearer", $request->getHeaderLine('Authorization'))[1])))->first();
    $products = Product::select('id')->where("employee_type", $employeeType['id']);
    $orderItems = OrderItem::whereIn('product',$products)->where("status", 1)->get();

    $response->getBody()->write($orderItems->toJson());
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function PonerEnPreparacion($request, $response, $args)
  {
    $record = OrderItem::find($args['id']);
    if($record != null) {
      $employeeType = EmployeeType::where("name", AutentificadorJWT::ObtenerEmployeeType(trim(explode("Bearer", $request->getHeaderLine('Authorization'))[1])))->first();
      $product = Product::find($record->product);
      if($employeeType['id'] == $product->employee_type){
        $record2 = Status::where('name', 'En preparacion')->first();
        $record->status = $record2->id;
  
        $product = Product::find($record->product);
  
        $time = new DateTime('America/Argentina/Buenos_Aires');
        $time->add(new DateInterval('PT' . $product->eta . 'M'));
  
        $record->eta = $time->format('Y-m-d H:i');
        $record->save();
  
        $response->getBody()->write($record->toJson());
      } else {
        $response->getBody()->write("El item seleccionado no le corresponde al empleado logueado.");
      }
    } else {
      $response->getBody()->write("No existe un item con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function PonerListoParaServir($request, $response, $args)
  {
    $record = OrderItem::find($args['id']);
    if($record != null) {
      $employeeType = EmployeeType::where("name", AutentificadorJWT::ObtenerEmployeeType(trim(explode("Bearer", $request->getHeaderLine('Authorization'))[1])))->first();
      $product = Product::find($record->product);
      
      if($employeeType['id'] == $product->employee_type){
        $record2 = Status::where('name', 'Listo para Servir')->first();
        $record->status = $record2->id;
        
        $time = new DateTime('America/Argentina/Buenos_Aires');
        $record->completed_time = $time->format('Y-m-d H:i');
        $record->save();
  
        $order = Order::find($record->order_id);
        $orderItemsPending = OrderItem::where('order_id', $order->id)
        ->where('status', '!=', $record2->id)->get();
        if(count($orderItemsPending) == 0){
          $order->status = 3;
          $order->save();
        }

        $response->getBody()->write($record->toJson());
      } else {
        $response->getBody()->write("El item seleccionado no le corresponde al empleado logueado.");
      }
    } else {
      $response->getBody()->write("No existe status con ese id");
    }

    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
