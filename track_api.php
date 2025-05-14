<?php

// Encabezados CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Configuración de la base de datos
$host = "localhost";
$username = "root";
$password = "root";
$dbname = "trackDB";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión con la base de datos fallida: " . mysqli_connect_error()]);
    exit();
}

// Manejo de preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Rutas principales
$resource = $segments[1] ?? null; // index.php sería el [0], lo ignoramos

switch ($method) {
    case 'GET':
        if ($resource === 'orders') {
            $user_id = 1;
            getUserOrders($conn, $user_id);
        } elseif ($resource === 'qr_orders') {
            $description = $_GET['description'] ?? null;
            getOrderByQR($conn, $description);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Ruta no encontrada"]);
        }
        break;

    case 'POST':
        if ($resource === 'register') {
            register($conn);
        } elseif ($resource === 'login') {
            login($conn);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Ruta no encontrada"]);
        }
        break;

    case 'PATCH':
        if ($resource === 'qr_orders') {
            $data = json_decode(file_get_contents("php://input"), true);
            $description = $data['description'] ?? null;
            checkOrderByQR($conn, $description);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Ruta no encontrada"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}


// Funciones auxiliares

function getUserOrders($conn, $user_id) {
    $query = "SELECT order_id, latitude, longitude, delivered FROM user_orders WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    echo json_encode($orders);
}

function getOrderByQR($conn, $description) {
    if (empty($description)) {
        http_response_code(400);
        echo json_encode(["error" => "QR requerido"]);
        return;
    }

    $check_query = "SELECT order_id FROM qr_orders WHERE description = ?";
    $stmt_check = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt_check, 's', $description);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if ($row_check = mysqli_fetch_assoc($result_check)) {
        $order_id = $row_check['order_id'];

        $query = "SELECT order_id, latitude, longitude, delivered FROM user_orders WHERE order_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            http_response_code(200);
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Orden no encontrada"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "QR no encontrado"]);
    }
}

function register($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        http_response_code(400);
        echo json_encode(["error" => "Nombre de usuario y contraseña requeridos"]);
        return;
    }

    $query = "SELECT user_id FROM user WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        http_response_code(409);
        echo json_encode(["error" => "El username ya está registrado."]);
        return;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insert = "INSERT INTO user (username, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insert);
    mysqli_stmt_bind_param($stmt, 'ss', $name, $hashed);

    if (mysqli_stmt_execute($stmt)) {
        http_response_code(201);
        echo json_encode(["message" => "Usuario registrado exitosamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error insertando el usuario."]);
    }
}

function login($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['username'] ?? null;
    $password = $data['password'] ?? null;

    if (!$name || !$password) {
        http_response_code(400);
        echo json_encode(["error" => "Nombre de usuario y contraseña requeridos"]);
        return;
    }

    $query = "SELECT user_id, password FROM user WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);

    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($password, $hashed_password)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Inicio de sesión exitoso.",
                "user" => [
                    "id" => $user_id,
                    "username" => $name,
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Credenciales inválidas."]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Usuario no encontrado."]);
    }
}

function checkOrderByQR($conn, $description) {
    if (!$description) {
        http_response_code(400);
        echo json_encode(["error" => "QR requerido"]);
        return;
    }

    $query = "UPDATE user_orders uo
              JOIN qr_orders qo ON uo.order_id = qo.order_id
              SET uo.delivered = TRUE
              WHERE qo.description = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $description);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Orden marcada como entregada"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró ninguna orden con ese QR"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar la orden"]);
    }
}
?>
