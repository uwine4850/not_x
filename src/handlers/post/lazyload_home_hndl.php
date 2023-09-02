<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'lazyload_post.php';
require_once 'handlers/twig_functions.php';
require_once 'config.php';

class LazyLoadHomeHandler extends BaseHandler {
    use LazyLoadPost;
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    public function __construct()
    {
        parent::__construct();
        $this->lazy_load_post_construct();
    }

    public function __destruct()
    {
        $this->close_db();
    }

    public function get(): void
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET'){
            return;
        }
        $post_id = $_GET['last_post_id'];
        $uid = $_GET['user_g']['id'];
        $this->posts = get_subscriptions_posts($uid, $post_id, config\LOAD_POST_COUNT, $this->db_posts);
    }

    public function handle(): void
    {
        $this->get();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->render('includes/post.html', array(
            'posts' => $this->posts,
        ) + $this->get_post_tables());
    }

}