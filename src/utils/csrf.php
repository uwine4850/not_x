<?php
namespace csrf;
require_once "config.php";
use config;

function generate_csrf(): array{
    return array(
        'token' => bin2hex(random_bytes(32)),
        'timestamp' => time(),
    );
}

function csrf_lifetime(): void{
    utils_start_session();
    $token_lifetime = config\CSRF_TOKEN_LIFETIME;
    if (key_exists('csrf_token', $_SESSION)){
        $token_timestamp = $_SESSION['csrf_token']['timestamp'];
        $current_timestamp = time();
        if ($current_timestamp - $token_timestamp > $token_lifetime) {
            $_SESSION['csrf_token'] = generate_csrf();
        }
    } else{
        $_SESSION['csrf_token'] = generate_csrf();
    }
}

function get_csrf_token(): string{
    csrf_lifetime();
    if (isset($_SESSION['csrf_token'])){
        return $_SESSION['csrf_token']['token'];
    } else{
        return '';
    }
}

function get_csrf_input(): string{
    $csrf = get_csrf_token();
    return "<input type='hidden' name='csrf_token' value='$csrf'>";
}
