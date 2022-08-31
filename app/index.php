<?php
//php -S localhost:666 -t app
//https://laravel.com/docs/9.x/eloquent-collections#available-methods
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/EmployeeController.php';
require_once './controllers/TableController.php';
require_once './controllers/ProductController.php';
require_once './controllers/StatusController.php';
require_once './controllers/EmployeeTypeController.php';
require_once './controllers/OrderController.php';
require_once './controllers/OrderItemController.php';
require_once './controllers/LoginController.php';
require_once './middlewares/MdwCore.php';
require_once './middlewares/MdwJWT.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Eloquent
$container=$app->getContainer();
require './conf/bootstrap.php';

// Routes
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("TP Comanda Bianchini Alejo");
    return $response;
});

$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \LoginController::class . ':Login');
})->add(\MdwCore::class . ':VerificarUsuarioV2');

$app->group('/employee', function (RouteCollectorProxy $group) {
    $group->get('/{uuid}', \EmployeeController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \EmployeeController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarToken');
    $group->post('[/]', \EmployeeController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \EmployeeController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \EmployeeController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{uuid}', \EmployeeController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
});

$app->group('/table', function (RouteCollectorProxy $group) {
    $group->get('/{number}', \TableController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \TableController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('[/]', \TableController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \TableController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \TableController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{id}', \TableController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
});

$app->group('/product', function (RouteCollectorProxy $group) {
    $group->get('/{id}', \ProductController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \ProductController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('[/]', \ProductController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \ProductController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \ProductController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{id}', \ProductController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->get('/test/supertest', \ProductController::class . ':ObtenerDeMasVendidoAMenos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
});

$app->group('/status', function (RouteCollectorProxy $group) {
    $group->get('/{id}', \StatusController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \StatusController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('[/]', \StatusController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \StatusController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \StatusController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{id}', \StatusController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
});

$app->group('/employeetype', function (RouteCollectorProxy $group) {
    $group->get('/{id}', \EmployeeTypeController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \EmployeeTypeController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('[/]', \EmployeeTypeController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \EmployeeTypeController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \EmployeeTypeController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{id}', \EmployeeTypeController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
});

$app->group('/orders', function (RouteCollectorProxy $group) {
    $group->get('/{number}', \OrderController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \OrderController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('[/]', \OrderController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \OrderController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \OrderController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{id}', \OrderController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('/picture', \OrderController::class . ':TomarFoto' )->add(\MdwJWT::class . ':ValidarTokenSocio');
});

$app->group('/orderitem', function (RouteCollectorProxy $group) {
    $group->get('/{id}', \OrderItemController::class . ':TraerUno' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('/todos/employee', \OrderItemController::class . ':TraerTodosPorTipoDeEmpleado' )->add(\MdwJWT::class . ':ValidarToken');
    $group->get('[/]', \OrderItemController::class . ':TraerTodos' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->post('[/]', \OrderItemController::class . ':CargarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('[/{id}]', \OrderItemController::class . ':ModificarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->delete('[/{id}]', \OrderItemController::class . ':BorrarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/restore/{id}', \OrderItemController::class . ':RestaurarUno' )->add(\MdwJWT::class . ':ValidarTokenSocio');
    $group->put('/start/{id}', \OrderItemController::class . ':PonerEnPreparacion' )->add(\MdwJWT::class . ':ValidarToken');
    $group->put('/end/{id}', \OrderItemController::class . ':PonerListoParaServir' )->add(\MdwJWT::class . ':ValidarToken');
});

$app->group('/customer', function (RouteCollectorProxy $group) {
    $group->get('[/]', \OrderController::class . ':ConsultarDemora' );
    //$group->post('[/]', \EmployeeController::class . ':CargarUno' );
});

$app->run();
