<?php
function calcularAlicuota($data) {
    // Obtener los datos de entrada
    $uab = $data['uab'];
    $bandera = $data['bandera'];
    $BCV = $data['BCV'];
    $result = [];

    // Calcular según el valor de UAB
    if ($uab <= 500) {
        $result = manejarMenorIgual500($uab, $bandera);
    } else {
        $result = manejarMayor501($uab, $bandera, $BCV);
    }

    return $result;
}

function manejarMenorIgual500($uab, $bandera) {
    $bs = 0;
    $totalEuros = 0;

    // Calcular los montos según la bandera
    if ($bandera === 'venezolana') {
        $bs = 1315.00;
        $totalEuros = 26.30;
    } else if ($bandera === 'extranjera') {
        $bs = 2630.50;
        $totalEuros = 52.61;
    }

    return [
        "bs" => number_format($bs, 2, ',', '.'),
        "totalEuros" => number_format($totalEuros, 2, ',', '.')
    ];
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

    // Determinar la alícuota
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
       // "bs" => number_format($total, 2, ',', '.'),
        "Monto total en Euros €" => number_format($totalEuros, 2, ',', '.'),
        "Monto total en bs" => number_format($totalFinal, 2, ',', '.')
    ];
}
?>
