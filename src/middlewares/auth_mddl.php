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
        if (isset($_COOKIE['UID'])){
            $db = new Database('users');
            $uid = $_COOKIE['UID'];
            $username = $db->all_where("id=$uid")[0]['username'];
            $_GET['username_g'] = $username;
        }
    }
}
