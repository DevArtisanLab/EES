<?php
function logout() {
    // Start the session if it's not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Unset all session variables
    $_SESSION = [];

    // Destroy the session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Redirect to login page or homepage
    header("Location: ../index.php"); // Change to your target page
    exit();
}

// Call the logout function
logout();
?>
