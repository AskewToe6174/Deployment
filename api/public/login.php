<?php
header("Access-Control-Allow-Origin: *");

// Permitir los métodos HTTP que desees (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Permitir los encabezados necesarios para las solicitudes CORS
header("Access-Control-Allow-Headers: Content-Type, Authorization");
session_start();
require '../src/infraestructure/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer el cuerpo de la solicitud
    $jsonData = file_get_contents('php://input');

    // Decodificar el JSON a un array asociativo
    $data = json_decode($jsonData, true);

    // Verificar si los campos 'email' y 'psw' existen y no están vacíos
    if (
        isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL) &&
        isset($data['psw']) && !empty($data['psw'])
    ) {
        // Preparar la consulta SQL para evitar inyecciones SQL
        $stmt = $db->prepare("SELECT usuarios.psw, cuentas.emailC as emailEmp, profiles.id as profile FROM usuarios
                                INNER JOIN empleados on usuarios.idEmpleado = empleados.id
                                INNER JOIN cuentas on usuarios.idCuenta = cuentas.id
                                INNER JOIN profiles on usuarios.idProfile = profiles.id
                                WHERE cuentas.emailC = ? AND usuarios.status = 1");

        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt) {
            // Vincular el parámetro (email) a la consulta
            $stmt->bind_param("s", $data['email']);

            // Ejecutar la consulta
            $stmt->execute();

            // Obtener el resultado
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                // Comparar la contraseña proporcionada con el hash almacenado
                if (password_verify($data['psw'], $user['psw'])) {
                    // Usuario autenticado correctamente
                    $_SESSION["email"] = $data['email'];
                    $_SESSION['profile'] = $user['profile'];

                    // Redirigir según el perfil del usuario
                    if ($user['profile'] == 1) {
                        $response = array('message' => 'success', 'url' => 'http://localhost/operaciones/src');
                    } elseif ($user['profile'] == 2) {
                        $response = array('message' => 'success', 'url' => 'http://localhost/m2capital/');
                    } elseif ($user['profile'] == 3) {
                        $response = array('message' => 'success', 'url' => 'http://localhost/sistemas/');
                    } elseif ($user['profile'] == 4) {
                        $response = array('message' => 'success', 'url' => 'http://localhost/juridico/');
                    } else {
                        http_response_code(401); // Código 401 Unauthorized
                        $response = array('message' => 'error', 'details' => 'Perfil no reconocido');
                    }
                    // Enviar la respuesta en formato JSON
                    echo json_encode($response);
                } else {
                    // Contraseña incorrecta
                    http_response_code(401); // Código 401 Unauthorized
                    $response = array('message' => 'error', 'details' => 'ERROR PASSWORD');
                    echo json_encode($response);
                    exit;
                }
            } else {
                // El correo no existe o el usuario está inactivo
                http_response_code(404); // Código 404 Not Found
                $response = array('message' => 'error', 'details' => 'Usuario no identificado o inactivo');
                echo json_encode($response);
                exit;
            }
        } else {
            // Error al preparar la consulta
            http_response_code(500); // Error interno del servidor
            $response = array('message' => 'error', 'details' => 'Error en la consulta SQL');
            echo json_encode($response);
        }
    } else {
        // Enviar una respuesta si los datos no son válidos
        http_response_code(400); // Solicitud incorrecta
        $response = array('message' => 'error', 'details' => 'Datos no válidos');
        echo json_encode($response);
    }
}


