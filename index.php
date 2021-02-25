<?php

/**
 * API requirement:
 * 
 * Return a JSON response for all APIs and allow caching where appropiate
 * A task has an ID, title, description, deadline date, completion status
 * 
 * Route:
 * GET: / Get all tasks
 * GET: /task/{id}
 * POST: /
 * DELETE: /{id}
 * PATCH: /{id}
 * GET: /complete
 * GET: /incomplete
 */

require 'vendor/autoload.php';

use Model\Response;
use Validate\validateTaskRequest;
use Validate\validateUserRequest;
use Auth\JwtAuthentication;

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
  header('Access-Control-Allow-Origin: *');
  Header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
  header("Access-Control-Max-Age: 3600");    
  header("Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  return 200;
}


class Section { public const AUTH = 'auth'; }

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
  $r->addRoute('GET', '/', [\Controller\TaskController::class, 'tasks']);
  $r->addRoute('GET', '/{id:\d+}', [\Controller\TaskController::class, 'task']);
  $r->addRoute('GET', '/complete', [\Controller\TaskController::class, 'tasksComplete']);
  $r->addRoute('GET', '/incomplete', [\Controller\TaskController::class, 'tasksIncomplete']);
  $r->addRoute('POST', '/', [\Controller\TaskController::class, 'createTask', Section::AUTH]);
  $r->addRoute('DELETE', '/{id:\d+}', [\Controller\TaskController::class, 'deleteTask', Section::AUTH]);
  $r->addRoute('PATCH', '/{id:\d+}', [\Controller\TaskController::class, 'patchTask', Section::AUTH]);
  $r->addRoute('POST', '/user/create', [\Controller\UserController::class, 'createUser']);
  $r->addRoute('POST', '/user/login', [\Controller\UserController::class ,'loginUser']);
  $r->addRoute('DELETE', '/user/delete', [\Controller\UserController::class, 'deleteUser', Section::AUTH]);
  $r->addRoute('GET','/user/confirm', [\Controller\UserController::class, 'confirmUser', Section::AUTH]);
});

$response = new Response();
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
  $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
  case FastRoute\Dispatcher::NOT_FOUND:
    $response->setAttributes(false, 404, 'Route not found', [], true)->send();
    break;
  case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
    $allowMethods = $routeInfo[1];
    $response->setAttributes(false, 405, 'Method not allowed (missing parameter?)', [], true)->send();
    break;
  case FastRoute\Dispatcher::FOUND:
    $vars = $routeInfo[2];
    $class = $routeInfo[1][0]; 
    $method = $routeInfo[1][1]; 
    $section = $routeInfo[1][2] ?? null;
    $authentication = null;
    if($section && $section === Section::AUTH) {
      try {
        $authentication = (new JwtAuthentication)->authentication($auth);
        if($authentication == NULL) throw new \Exception("Pls login");
        $vars['authentication'] = $authentication->data;
      } catch(Exception $e) {
        $response->setAttributes(false, 401, $e->getMessage(), [], false)->send();
      }
    }

    if($httpMethod === "POST" || $httpMethod === "PATCH") {
      if($httpMethod !== "DELETE" && file_get_contents('php://input') === "") {
        $response->setAttributes(true, 200, 'No data', [], false)->send();
      }
      // data is json
      $data = json_decode(file_get_contents('php://input'), true);
      // if not, data is x-www-form-urlencoded
      if(!$data) parse_str(file_get_contents('php://input'), $data);

      if($class === 'Controller\TaskController') {
        $data = (new validateTaskRequest($data, $httpMethod))->validateFunc();
        $vars['task'] = $data;
      }
      if($class === 'Controller\UserController') {
        $data = (new validateUserRequest($data, $httpMethod, $method))->validateFunc();
        $vars['user'] = $data;
      }
    }
    call_user_func(array(new $class, $method), $vars);
}