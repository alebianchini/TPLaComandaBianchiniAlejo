<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
 
$capsule = new Capsule();
 
$capsule->addConnection([
'driver'   => 'mysql',
'host'     => $_ENV['MYSQL_HOST'],
'port'     => $_ENV['MYSQL_PORT'],
'database'  => $_ENV['MYSQL_DB'],
'username'  => $_ENV['MYSQL_USER'],
'password'  => $_ENV['MYSQL_PASS'],
'charset'   => 'utf8',
'collation' => 'utf8_unicode_ci',
'prefix'   => '',
]);

$capsule->setEventDispatcher(new Dispatcher(new Container));

$capsule->setAsGlobal();

$capsule->bootEloquent();
$capsule->bootEloquent();