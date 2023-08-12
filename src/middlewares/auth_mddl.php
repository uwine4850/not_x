<?php
require_once "utils/middleware.php";

class AuthMddl extends Middleware{
    public function run(): void
    {
        if (!isset($_COOKIE['UID']) && $_GET["url_pattern"] != "/login" && $_GET["url_pattern"] != "/register"){
            header("Location: /login");
        }
        if (isset($_COOKIE['UID'])){
            if ($_GET["url_pattern"] == '/login' || $_GET["url_pattern"] == '/register'){
                header("Location: /");
            }
        }
    }
}
