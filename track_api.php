<?php

/* Este archivo se va a pegar en /var/www/html para que funcione en sus servidores.
 * Se puede pegar con el siguiente comando en linux:
 * 		sudo cp /dir_del_archivo/track_api.php /var/www/html/
 */

// Para evitar problemas del CORS.
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Cambiar estas variables segun las suyas de MariaDB o MySQL.
$host = "localhost";
$username = "root";
$password = "root";
$dbname = "trackDB";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
	die("Conexion con la base de datos fallida: " . mysqli_connect_error());
;}

// para que funcione session
// session_start();

$user_id = 1; 

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($method) {
    case 'GET':
        if ($uri[1] === 'orders') {
			getUserOrders($conn, $user_id);
		} elseif ($uri[1] === 'qr_orders') {
            $qr_url = $_GET['url'] ?? null; // para obtenerlo de la url
			getOrderByQR($conn, $qr_url);
		}
        break;

    case 'POST':
        if ($uri[1] === 'register') {
            register($conn);
        } elseif ($uri[1] === 'login') {
            login($conn);
        }
		break;

    case 'PATCH':
        if ($uri[1] === 'orders') {
            $qr_url = $_GET['url'] ?? null;
            checkOrderByQR($conn, $qr_url);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Metodo no permitido"]);
        break;
}

    // get the orders from the authenticated user.
    function getUserOrders($conn,$user_id) {
        // auth works with session
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Usuario no autenticado"]);
            return;
        }
    
        $user_id = $_SESSION['user_id'];*/
    
        $query = "SELECT order_id, latitude, longitude, delivered FROM user_orders WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
    
        // send json formatted orders back to client.
        echo json_encode($orders);
    }

    // when a user scans a qr, this function identify which 
    function getOrderByQR($conn, $qr_url) {
        if (empty($qr_url)) {
            http_response_code(400);
            echo json_encode(["error" => "QR requerido"]);
            return;
        }

        if (!filter_var($qr_url, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(["error" => "URL no válida"]);
            return;
        }

        $check_query = "SELECT url FROM qr_orders WHERE url = ?";
        $stmt_check = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt_check, 's', $qr_url);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) == 0) {
            // si no existe la URL en qr_orders
            http_response_code(404);
            echo json_encode(["error" => "QR no encontrado en la base de datos"]);
            return;
        }
        
        $query = "SELECT uo.order_id, uo.latitude, uo.longitude, uo.delivered
                  FROM qr_orders qo 
                  JOIN user_orders uo ON qo.order_id = uo.order_id 
                  WHERE qo.url = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $qr_url);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if ($row = mysqli_fetch_assoc($result)) {
            http_response_code(200);
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Orden no encontrada"]);
        }
    }

    // create a new user.
    function register($conn) {
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['username'];
        $password = $data['password'];

        // check if user already exists.
        $query = "SELECT user_id FROM user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            http_response_code(409);
            echo json_encode(["error" => "El username ya esta registrado."]);
            return;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $insert = "INSERT INTO user (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, 'ss', $name, $hashed);
        
        if (mysqli_stmt_execute($stmt)) {
            http_response_code(201);
            echo json_encode(["message" => "El usuario se registro exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error insertando el usuario."]);
        }
    }

    // start session as a user.
    function login($conn) {
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['username'];
        $password = $data['password'];

        // get user from database.
        $query = "SELECT user_id, username, password FROM user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $user_id, $dbname, $hashed_password);
        
        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hashed_password)) {
                http_response_code(200);
                echo json_encode([
                    "message" => "Ha iniciado sesion de forma exitosa.",
                    "user" => [
                        "id" => $user_id,
                        "username" => $name,
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Las credenciales ingresadas no son validas."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado."]);
        }
    }

    // mark orders as delivered.
    function checkOrderByQR($conn, $qr_url) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$qr_url) {
            http_response_code(400);
            echo json_encode(["error" => "QR requerido"]);
            return;
        }

        $query = "UPDATE user_orders uo 
                JOIN qr_orders qo ON uo.order_id = qo.order_id 
                SET uo.delivered = TRUE 
                WHERE qo.url = ?";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $qr_url);

        if (mysqli_stmt_execute($stmt)) {
            http_response_code(200);
            echo json_encode(["message" => "Orden marcada como entregada"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error actualizando la orden"]);
        }
    }

?>
