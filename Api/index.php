<?php 
declare(strict_types= 1);

define('ROOT_DIR' ,"C:/xampp/htdocs/noautoloader");
define('API_DIR', ROOT_DIR . "/Api");
define('SRC_DIR', ROOT_DIR. "/src");

require SRC_DIR . "/Database.php";
require SRC_DIR . "/UserController.php"; 
require SRC_DIR . "/ErrorHandler.php";
require SRC_DIR . "/UserGateway.php";



ini_set("display_errors", "On");


set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$parts = explode('/', $path);

$resource = $parts[3];

$id = $parts[4] ?? null;

if($resource !="users"){
    http_response_code(404);
    exit;
}

header("Content-Type: application/json; charset=UTF-8");
$database = new Database("localhost", "webprogapi", "root", "");



$user_gateway = new UserGateway($database);

$controller = new UserController($user_gateway);

$method = $_SERVER["REQUEST_METHOD"];

$controller->processRequest($method, $id);

?>