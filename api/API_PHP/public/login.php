<?php
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
        $stmt = $db->prepare("SELECT usuarios.psw, empleados.name, cuentas.emailC as emailEmp, profiles.id as profile FROM usuarios
                                    INNER JOIN empleados on usuarios.idEmpleado = empleados.id
                                    INNER JOIN cuentas on  usuarios.idCuenta = cuentas.id
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

            // Verificar si se encontró un usuario con ese correo
            if ($user = $result->fetch_assoc()) {
                // Comparar la contraseña proporcionada con el hash almacenado
                if (password_verify($data['psw'], hash: $user['psw'])) {
                    // Usuario autenticado correctamente
                    // PERFIL DE OPERACIONES
                    if($user['profile'] == 1){
                        // Ahora puedes usar $_SESSION para almacenar datos
                        $_SESSION["email"] = $data['email'];
                        $_SESSION['profile'] = $user['profile'];
                        $response = array('message' => 'success', 'url' => 'http://localhost/dsbOperaciones/FRONTEND/src/pages/operaciones/index.php');
                        echo json_encode($response);
                        // PERFIL DE M2CAPITAL
                    }elseif($user['profile'] == 2){
                        $_SESSION["email"] = $data['email'];
                        $_SESSION['profile'] = $user['profile'];
                    
                        $response = array('message' => 'success', 'url' => 'http://localhost/dsbOperaciones/FRONTEND/src/pages/m2capital/');
                        echo json_encode($response);
                        // PERFIL DE SISTEMAS
                    }elseif($user['profile'] == 3){
                        $_SESSION["email"] = $data['email'];
                        $_SESSION['profile'] = $user['profile'];
                    
                        $response = array('message' => 'success', 'url' => 'http://localhost/dsbOperaciones/FRONTEND/src/pages/sistemas/');
                        echo json_encode($response);
                        
                    }elseif($user['profile'] == 4){

                    }else{
                        $response = array('message' => 'error', 'details' => 'ERROR PASSWORD');
                        echo json_encode($response);
                    }   
                    
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

            // Cerrar la consulta preparada
            $stmt->close();
        } else {
            // Error en la preparación de la consulta
            http_response_code(500); // Código 500 Internal Server Error
            $response = array('message' => 'error', 'details' => 'Error en la consulta');
            echo json_encode($response);
            exit;
        }
    } else {
        // Datos vacíos o email inválido
        http_response_code(400); // Código 400 Bad Request
        $response = array(
            'message' => 'error',
            'details' => 'Datos vacíos o email inválido'
        );
        echo json_encode($response);
    }
}

