<?php
require_once "utils/middleware.php";

class LogOutMddl extends Middleware{
    public function run(): void
    {
        if (array_key_exists('log-out', $_POST)){
            setcookie('UID', '', time()-1, '/');
            header("Location: /login");
        }
    }
}