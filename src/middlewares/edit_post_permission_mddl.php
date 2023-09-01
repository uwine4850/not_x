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
        $posts_db->close();
        if (empty($post)){
            require_once 'utils/router.php';
            render_403();
            exit();
        }
        if ($post[0]['user'] != $_GET['user_g']['id']){
            require_once 'utils/router.php';
            render_403();
            exit();
        }
    }
}
