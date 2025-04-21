<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/db.php';
require_once '../models/Project.php';

$database = new Db();
$db = $database->connect();

$project = new Project($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $project->read();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($projects);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $project->name = $data->name;
        $project->description = $data->description;

        if ($project->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Проект создан"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Невозможно создать проект"));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $project->id = $data->id;

        if ($project->delete()) {
            echo json_encode(array("message" => "Проект удален."));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Невозможно удалить проект."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}