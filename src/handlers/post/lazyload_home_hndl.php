<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'lazyload_post.php';
require_once 'handlers/twig_functions.php';

class LazyLoadHomeHandler extends BaseHandler {
    use LazyLoadPost;
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    private Database $users_db;

    public function __construct()
    {
        parent::__construct();
        $this->lazy_load_post_construct();
        $this->users_db = $this->db->table_name('users');
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
        $this->posts = get_subscriptions_posts($uid, $post_id, 2, $this->posts_db);
    }

    public function handle(): void
    {
        $this->get();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->render('includes/post.html', array(
            'posts' => $this->posts,
            'users_db' => $this->users_db,
            'post_image_db' => $this->post_images_db,
            'db_post_like' => $this->db_post_like,
            'db_comments' => $this->db_comments,
        ));
    }

}