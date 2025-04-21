<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';
require_once '../models/Task.php';

$database = new Db();
$db = $database->connect();

$task = new Task($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Получить ID задачи или project_id из параметров запроса
        $task->id = isset($_GET['id']) ? $_GET['id'] : null;
        $task->project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;

        if ($task->id) {
            // Получить одну задачу по ID
            $result = $task->read();
            if ($result) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Задача не найдена"));
            }
        } elseif ($task->project_id) {
            // Получить все задачи по project_id
            $stmt = $task->readByProject();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($tasks) {
                http_response_code(200);
                echo json_encode($tasks);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Задачи не найдены"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Не указан ID задачи или project_id"));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $task->project_id = $data->project_id;
        $task->title = $data->title;
        $task->description = $data->description;
        $task->status = $data->status ?? 'todo';
        $task->assigned_to = $data->assigned_to;

        if ($task->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Задача создана"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Невозможно создать задачу"));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $task->id = $data->id;
        $task->status = $data->status;

        if ($task->updateStatus()) {
            echo json_encode(array("message" => "Задача обновлена"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Невозможно обновить задачу"));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $task->id = $data->id;

        if ($task->delete()) {
            echo json_encode(array("message" => "Задача удалена"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Невозможно удалить задачу"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}