<?php
// Credenciales de usuario
define('USERNAME', 'asyhub');
define('PASSWORD', '@syhub1n34$');

// Función para autenticar al usuario
function authenticate($username, $password) {
    return $username === USERNAME && $password === PASSWORD;
}
?>
