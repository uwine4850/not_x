<?php
namespace TwigFunc;

require_once 'handlers/post/post_utils.php';
require_once 'handlers/profile/profile_utils.php';
require_once 'utils/database.php';
require_once "utils/csrf.php";
use csrf;
use Database;
use Twig\Environment;

trait PostFunc{
    public function enable_post_func(Environment $twig, Database $db_instance = null): void{
        $twig->addFunction((new \Twig\TwigFunction("get_post_user", "get_user_by_id")));
        $twig->addFunction((new \Twig\TwigFunction("comments_count", "get_count_of_comment_by_post_id")));
        $twig->addFunction((new \Twig\TwigFunction("post_like_count", "post_like_count")));
        $twig->addFunction((new \Twig\TwigFunction("is_liked", "is_liked")));
        $twig->addFunction((new \Twig\TwigFunction('get_post_image', 'get_post_image')));
    }

    /**
     * Return an array with tables to display the post correctly.
     * IMPORTANT: the class must already have tables created.
     * @return array
     */
    function get_post_tables(): array{
        return array(
            'users_db' => $this->db_users,
            'post_image_db' => $this->db_post_image,
            'db_post_like' => $this->db_post_like,
            'db_comments' => $this->db_comments,
        );
    }
}

trait GlobalFunc{
    public function enable_global_func(Environment $twig): void{
        $twig->addFunction((new \Twig\TwigFunction("media_img", "get_path_to_media_image")));
        $twig->addFunction((new \Twig\TwigFunction("csrf_token", "csrf\\get_csrf_input")));
    }
}
