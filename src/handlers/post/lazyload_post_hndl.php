<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/profile/profile_utils.php';
require_once 'lazyload_post.php';
require_once 'handlers/twig_functions.php';

class LazyLoadPostHandler extends BaseHandler {
    use LazyLoadPost;
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    public function __construct()
    {
        parent::__construct();
        $this->lazy_load_post_construct();
    }

    public function get(): void
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET'){
            return;
        }
        $post_id = $_GET['last_post_id'];
        $uid = $_GET['user_id'];
        $this->posts = load_user_posts($uid, $post_id, 2);
        $this->user = get_user_by_id($uid);
    }

    public function handle(): void
    {
        $this->get();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->render('includes/post.html', array(
            'posts' => $this->posts,
            'user' => $this->user,
        ));
    }

}