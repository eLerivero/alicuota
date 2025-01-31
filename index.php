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
                "message" => "Credenciales no proporcionadas."
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
                "message" => "Credenciales inválidas."
            ]);
            exit;
        }
        
        // Si la autenticación es correcta, procesar el cálculo
        try {
            $result = calcularAlicuota($input['uab'], $input['bandera'], $input['BCV']);
            $resultadoPilotaje = calcularPilotaje($input['uab'], $input['bandera'], $input['maniobras'] ?? 0);
            $resultadoLanchaje = calcularLanchaje($input['uab'], $input['bandera'], $input['maniobras'] ?? 0);

            // Combina los resultados
            $resultadoFinal = array_merge($result, $resultadoPilotaje, $resultadoLanchaje);

            // Solo devuelve los resultados
            echo json_encode($resultadoFinal, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "code" => 500,
                "message" => "Error en el procesamiento de la solicitud."
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "code" => 400,
            "message" => "Datos de cálculo incompletos."
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "code" => 405,
        "message" => "Método no permitido."
    ]);
}
?>
