<?php
require_once "utils/router.php";

$router = new XRouter(get_requested_url());
$router->add("/", 'home_hndl.php');
$router->add("/profile", 'profile_hndl.php');
$router->add("/profile-edit", 'profile_edit_hndl.php');
$router->add("/login", 'login_hndl.php');
$router->add("/register", 'register_hndl.php');
$router->add("/create-post", 'create_post_hndl.php');
$router->add("/edit-post", 'post_edit_hndl.php');
$router->route();
