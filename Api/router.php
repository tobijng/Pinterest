<?php

require_once __DIR__ . '/../vendor/autoload.php';// Autoload der Dependencies

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

// Sicherstellen, dass man Controllers/Models nur über den router rufen kann
define('API_ACCESS', true);

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Definiere die Route für getUser
    $r->addRoute('GET', '/get_user/{id:\d+}', 'Controllers\User\getUserController@getUser');
    $r->addRoute('GET', '/get_countries', 'Controllers\Dropdowns\getCountryController@getCountries');
    $r->addRoute('GET', '/get_pronouns', 'Controllers\Dropdowns\getPronounController@getPronoun');
    $r->addRoute('DELETE', '/delete_user/{id:\d+}', 'Controllers\User\deleteUserController@deleteUser');
    $r->addRoute('POST', '/login', 'Controllers\Auth\loginController@login');
    $r->addRoute('POST', '/register', 'Controllers\Auth\registerController@register');
    $r->addRoute('POST', '/updateUser', 'Controllers\User\updateUserController@updateUser');
});

// HTTP-Methode und URI holen
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Basis-Pfad, der entfernt werden muss (Pfad zur Router-Datei)
$basePath = '/pinterest/api/router.php';


// Prüfen, ob `$basePath` in der URI enthalten ist und entfernen
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath)); // Entfernt den Base-Path
}

// Falls die URI noch Query-Parameter enthält, entfernen wir sie
$uri = strtok($uri, '?');

// Falls die URI mit einem `/` beginnt, entfernen wir es
$uri = '/' . ltrim($uri, '/');

// Dispatcher aufrufen
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {

    // Route existiert nicht
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Route Not Found"]);
        break;

    // Falsche Anfrage für bestehende Route
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method Not Allowed For This Route"]);
        break;

    // Falls gefunden:
    case Dispatcher::FOUND:
        // Aufteilung der Route (Controller + Methode, Zusatzinfo)
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Aufteilung von Controller und Methode
        list($controllerName, $methodName) = explode('@', $handler);

        // Fehler wenn Controllerklasse nicht existiert
        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Controller '$controllerName' not found."]);
            exit;
        }

        // Neue Instanz der Klasse
        $controller = new $controllerName();

        // Methode existiert nicht in der Controllerklasse
        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Method '$methodName' not found in controller '$controllerName'."]);
            exit;
        }

        // Prüfen, ob eine ID übergeben wurde oder nicht
        $response = !empty($vars) ? $controller->$methodName($vars['id']) : $controller->$methodName();

        // Sicherstellen, dass nur gültiger JSON ausgegeben wird
        if (is_string($response)) {
            echo $response;
        }

        break;

}
