<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/db.php';
include_once '../models/User.php';

$database = new Db();
$db = $database->connect();

$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Регистрация или авторизация
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->action)) {
            if ($data->action === 'register') {
                // Регистрация
                $user->username = $data->username;
                $user->email = $data->email;
                $user->password = $data->password;

                if ($user->register()) {
                    http_response_code(201);
                    echo json_encode(array("message" => "User registered successfully."));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Unable to register user."));
                }
            } elseif ($data->action === 'login') {
                // Авторизация
                $user->username = $data->username;
                $user->password = $data->password;

                $userId = $user->login();
                if ($userId) {
                    session_start();
                    $_SESSION['user_id'] = $userId;
                    echo json_encode(array("message" => "Login successful.", "user_id" => $userId));
                } else {
                    http_response_code(401);
                    echo json_encode(array("message" => "Login failed."));
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid action."));
        }
        break;
        
    case 'GET':
        $_SESSION['user_id'] = "1";
        // Получить информацию о пользователе
        session_start();
        if (isset($_SESSION['user_id'])) {
            $user->id = $_SESSION['user_id'];
            $userData = $user->readById();
            if ($userData) {
                echo json_encode($userData);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "User not found."));
            }
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Unauthorized."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}