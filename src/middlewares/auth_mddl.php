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
                require_once 'utils/router.php';
                render_403();
                exit();
            }
        }
        if (isset($_COOKIE['UID'])){
            $db = new Database('users');
            $uid = $_COOKIE['UID'];
            $user = $db->all_where("id=$uid")[0];
            unset($user['password']);
            $_GET['user_g'] = $user;
        }
    }
}
