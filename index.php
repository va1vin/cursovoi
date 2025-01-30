<?php

// Устанавливаем заголовки для поддержки CORS (Cross-Origin Resource Sharing)
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Подключаем необходимые файлы
require_once 'db.php';
require_once 'Project.php';
require_once 'Task.php';
require_once 'ProjectController.php';
require_once 'TaskController.php';

// Получаем путь из URL
$request_uri = $_SERVER['REQUEST_URI'];
$uri_parts = explode('/', trim($request_uri, '/'));

// Определяем базовый путь (например, "task_tracker")
$base_path = 'task_tracker'; // Замените на ваш базовый путь, если он есть

// Убираем базовый путь из URI
if ($uri_parts[0] === $base_path) {
    array_shift($uri_parts);
}

// Определяем ресурс (например, "projects" или "tasks")
$resource = $uri_parts[0] ?? '';

// Определяем ID (если есть)
$id = $uri_parts[1] ?? null;

// Определяем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

// Маршрутизация запросов
switch ($resource) {
    case 'projects':
        include 'ProjectController.php';
        break;

    case 'tasks':
        include 'TaskController.php';
        break;

    default:
        // Если ресурс не найден, возвращаем ошибку 404
        http_response_code(404);
        echo json_encode(array("message" => "Resource not found."));
        break;
}