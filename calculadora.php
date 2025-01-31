<?php

// Obtener los datos de entrada
$data = json_decode(file_get_contents('php://input'), true);
$uab = $data['uab'];
$bandera = $data['bandera'];
$BCV = $data['BCV'];
$maniobras = isset($data['maniobras']) ? $data['maniobras'] : 0; // Default a 0 si no se proporciona

// Función para calcular la alícuota
function calcularAlicuota($uab, $bandera, $BCV) {
    if ($uab <= 500) {
        return manejarMenorIgual500($bandera);
    } else {
        return manejarMayor501($uab, $bandera, $BCV);
    }
}

function manejarMenorIgual500($bandera) {
    if ($bandera === 'venezolana') {
        return [
            "bs" => number_format(1315.00, 2, ',', '.'),
            "totalEuros" => number_format(26.30, 2, ',', '.')
        ];
    } else {
        return [
            "bs" => number_format(2630.50, 2, ',', '.'),
            "totalEuros" => number_format(52.61, 2, ',', '.')
        ];
    }
}

function manejarMayor501($uab, $bandera, $BCV) {
    $alicuotas = [
        'b' => 0.0045,
        'c' => 0.0040,
        'd' => 0.0035,
        'e' => 0.0030,
    ];
    $ut = 242.02; // Valor de la Unidad Tributaria
    $tc = 4.60;   // Tasa de cambio

    if ($uab >= 501 && $uab <= 5000) {
        $alicuota = $alicuotas['b'];
    } else if ($uab >= 5001 && $uab <= 20000) {
        $alicuota = $alicuotas['c'];
    } else if ($uab >= 20001 && $uab <= 40000) {
        $alicuota = $alicuotas['d'];
    } else {
        $alicuota = $alicuotas['e'];
    }

    $porcentaje = ($bandera === 'venezolana') ? 0.5 : 1;
    $total = $uab * $ut * $alicuota * $porcentaje;
    $totalEuros = $total / $tc;
    $totalFinal = $totalEuros * $BCV;

    return [
        "Monto total en Euros €" => number_format($totalEuros, 2, ',', '.'),
        "Monto total en bs" => number_format($totalFinal, 2, ',', '.')
    ];
}

// Función para calcular el pilotaje
function calcularPilotaje($uab, $bandera, $maniobras) {
    $pilotajeE = [
        'a' => 817.00, 'b' => 1058.00, 'c' => 1543.00, 'd' => 3145.00, 'e' => 3266.00,
        'f' => 3448.00, 'g' => 3719.00, 'h' => 3810.00, 'i' => 4052.00, 'j' => 4263.00,
        'k' => 4506.00, 'l' => 5080.00, 'm' => 5353.00, 'n' => 5533.00, 'o' => 5776.00,
        'p' => 5866.00,
    ];
    $pilotajeV = [
        'a' => 204.00, 'b' => 265.00, 'c' => 383.00, 'd' => 786.00, 'e' => 816.00,
        'f' => 862.00, 'g' => 930.00, 'h' => 953.00, 'i' => 1013.00, 'j' => 1066.00,
        'k' => 1126.00, 'l' => 1270.00, 'm' => 1338.00, 'n' => 1383.00, 'o' => 1444.00,
        'p' => 1466.00,
    ];

    $costoPilotaje = 0;

    // Calcular costo de pilotaje según bandera
    if ($bandera === 'extranjera') {
        foreach ($pilotajeE as $key => $value) {
            if ($uab <= (2000 * pow(2, array_search($key, array_keys($pilotajeE))))) {
                $costoPilotaje = $value;
                break;
            }
        }
    } else if ($bandera === 'venezolana') {
        foreach ($pilotajeV as $key => $value) {
            if ($uab <= (2000 * pow(2, array_search($key, array_keys($pilotajeV))))) {
                $costoPilotaje = $value;
                break;
            }
        }
    }

    $totalppilotaje = $maniobras * $costoPilotaje;
    return [
        "totalppilotaje" => number_format($totalppilotaje, 2, ',', '.')
    ];
}

// Función para calcular el lanchaje
function calcularLanchaje($uab, $bandera, $maniobras) {
    $lanchajeE = [
        'a' => 574.00, 'b' => 696.00, 'c' => 756.00, 'd' => 817.00, 'e' => 897.00,
        'f' => 978.00, 'g' => 1058.00, 'h' => 1139.00, 'i' => 1220.00, 'j' => 1300.00,
        'k' => 1381.00, 'l' => 1462.00, 'm' => 1543.00, 'n' => 1623.00, 'o' => 1703.00,
        'p' => 1784.00,
    ];
    $lanchajeV = [
        'a' => 172.00, 'b' => 209.00, 'c' => 227.00, 'd' => 245.00, 'e' => 269.00,
        'f' => 293.00, 'g' => 318.00, 'h' => 342.00, 'i' => 366.00, 'j' => 390.00,
        'k' => 414.00, 'l' => 439.00, 'm' => 463.00, 'n' => 487.00, 'o' => 511.00,
        'p' => 535.00,
    ];

    $costolanchaje = 0;

    // Calcular costo de lanchaje según bandera
    if ($bandera === 'extranjera') {
        foreach ($lanchajeE as $key => $value) {
            if ($uab <= (2000 * pow(2, array_search($key, array_keys($lanchajeE))))) {
                $costolanchaje = $value;
                break;
            }
        }
    } else if ($bandera === 'venezolana') {
        foreach ($lanchajeV as $key => $value) {
            if ($uab <= (2000 * pow(2, array_search($key, array_keys($lanchajeV))))) {
                $costolanchaje = $value;
                break;
            }
        }
    }

    $totallanchaje = $maniobras * $costolanchaje;
    return [
        "totallanchaje" => number_format($totallanchaje, 2, ',', '.')
    ];
}
?>
