<?php

/* Este archivo se va a pegar en /var/www/html para que funcione en sus servidores.
 * Se puede pegar con el siguiente comando en linux:
 * 		sudo cp /dir_del_archivo/track_api.php /var/www/html/
 */

// Cambiar estas variables segun las suyas de MariaDB o MySQL.
$host = "localhost";
$username = "root";
$password = "root";
$dbname = "trackDB"

$conn = mysqli_connect($host, $username);

if (!$conn) {
	die("Conexion con la base de datos fallida: " . mysqli_connect_error());
;}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'))

switch ($method) {
    case 'GET':
        if ($uri[0] === 'user') {
            getUser($conn);
		} elseif ($uri[0] === 'orders') {
			getUserOrders($conn);
		} elseif ($uri[0] === 'qr_orders') {
			getOrderByQR($conn);
		}
        break;

    case 'POST':
        if ($uri[0] === 'register') {
            register($conn);
        } elseif ($uri[0] === 'login') {
            login($conn);
        }
		break;

    case 'PATCH':
        if ($uri[0] === 'orders') {
            checkOrderByQR($conn);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Metodo no permitido"]);
        break;
}

?>
