<?php
require_once "utils/middleware.php";
require_once "utils/database.php";

class EditPostPermissionMddl extends Middleware{

    public function run(): void
    {
        if ($_GET['url_pattern'] != '/edit-post/{post_id}'){
            return;
        }
        $posts_db = new Database('posts');
        $p_id = $_GET['post_id'];
        $post = $posts_db->all_where("id=$p_id");
        if (empty($post)){
            header("Location: /");
        }
        if ($post[0]['user'] != $_GET['user_g']['id']){
            header("Location: /");
        }
    }
}
