<?php
header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST"); 
header("Access-Control-Allow-Headers: Content-Type");

require './auth.php';
require './calculadora.php';

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Manejar la solicitud de autenticación y cálculo
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verificar si se están enviando datos de cálculo
    if (isset($input['uab']) && isset($input['bandera']) && isset($input['BCV'])) {
        // Obtener las credenciales de la cabecera Authorization
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "code" => 401,
                "message" => "Credenciales no proporcionadas.",
                "details" => "Por favor, incluya las credenciales en la cabecera de autorización."
            ]);
            exit;
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Autenticación
        if (!authenticate($username, $password)) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "code" => 401,
                "message" => "Credenciales inválidas.",
                "details" => "Asegúrese de que el nombre de usuario y la contraseña sean correctos."
            ]);
            exit;
        }
        
        // Si la autenticación es correcta, procesar el cálculo
        $result = calcularAlicuota($input);
        echo json_encode([
            "status" => "success",
            "code" => 200,
            "data" => $result
        ]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "code" => 400,
            "message" => "Datos de cálculo incompletos.",
            "details" => "Asegúrese de que los campos 'uab', 'bandera' y 'BCV' estén presentes."
        ]);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "code" => 405,
        "message" => "Método no permitido.",
        "details" => "Este endpoint solo acepta solicitudes POST."
    ]);
}
?>
