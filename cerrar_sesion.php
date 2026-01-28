<?php
// Inicia la sesión (necesario para acceder a las variables de sesión)
session_start();

// Elimina todas las variables de sesión
$_SESSION = array();

// Si se desea destruir completamente la sesión, también se debe borrar el cookie de sesión.
// Nota: ¡Esto destruirá la sesión y no podrás acceder a ninguna variable de sesión después!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión
session_destroy();

// Redirige al usuario a la página de inicio o login
header("Location: index.php"); // Cambia index.php por tu página principal
exit();
?>
