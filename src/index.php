<?php
require_once "utils/router.php";

$router = new XRouter(get_requested_url());
$router->add("/", 'home_hndl.php');
$router->add("/profile/{username}", '/profile/profile_hndl.php');
$router->add("/profile/{username}/edit", '/profile/profile_edit_hndl.php');
$router->add("/login", 'login_hndl.php');
$router->add("/register", 'register_hndl.php');
$router->add("/create-post", '/post/create_post_hndl.php');
$router->add("/edit-post/{post_id}", '/post/post_edit_hndl.php');
$router->add("/post-like", '/post/post_like_hndl.php');
$router->add("/post/{post_id}", '/post/post_view_hndl.php');
$router->add("/post-load", '/post/lazyload_post_hndl.php');
$router->add("/subscriptions", 'subscriptions_hndl.php');
$router->route();
