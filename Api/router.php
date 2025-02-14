<?php

// Load all dependencies using Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

// Define API access constant to ensure controllers/models are only accessible via the router
define('API_ACCESS', true);

// Initialize the FastRoute dispatcher and define API routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // User-related routes
    $r->addRoute('GET', '/get_user/{id:\d+}', 'Controllers\User\getUserController@getUser');
    $r->addRoute('DELETE', '/delete_user/{id:\d+}', 'Controllers\User\deleteUserController@deleteUser');

    // Authentication routes
    $r->addRoute('POST', '/login', 'Controllers\Auth\loginController@login');
    $r->addRoute('POST', '/register', 'Controllers\Auth\registerController@register');

    // User update route
    $r->addRoute('POST', '/updateUser', 'Controllers\User\updateUserController@updateUser');

    // Dropdown data routes
    $r->addRoute('GET', '/get_countries', 'Controllers\Dropdowns\getCountryController@getCountries');
    $r->addRoute('GET', '/get_pronouns', 'Controllers\Dropdowns\getPronounController@getPronoun');

    // Password reset route
    $r->addRoute('POST', '/reset_password', 'Controllers\Auth\resetPasswordController@resetPassword');

});

// Retrieve the HTTP method and request URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Define the base path of the router file to be removed from the request URI
$basePath = '/pinterest/api/router.php';

// Remove base path if present in the URI
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Remove query parameters from the URI
$uri = strtok($uri, '?');

// Ensure the URI starts with a single forward slash
$uri = '/' . ltrim($uri, '/');

// Dispatch the request using FastRoute
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        // Return a 404 response if the requested route does not exist
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Route Not Found"]);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        // Return a 405 response if the request method is not allowed for the existing route
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method Not Allowed For This Route"]);
        break;

    case Dispatcher::FOUND:
        // Extract controller and method from the matched route handler
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($controllerName, $methodName) = explode('@', $handler);

        // Ensure the controller class exists
        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Controller '$controllerName' not found."]);
            exit;
        }

        // Instantiate the controller
        $controller = new $controllerName();

        // Ensure the method exists within the controller class
        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Method '$methodName' not found in controller '$controllerName'."]);
            exit;
        }

        // Call the controller method and pass parameters if necessary
        $response = !empty($vars) ? $controller->$methodName($vars['id']) : $controller->$methodName();

        // Ensure the response is properly formatted as JSON
        if (is_string($response)) {
            echo $response;
        }
        break;
}
