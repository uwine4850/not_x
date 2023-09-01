<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/lazyload_post.php';
require_once 'handlers/twig_functions.php';

class HomeHandler extends BaseHandler{
    use HandlerUtils;
//    use LazyLoadPost;
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    private Database $posts_db;
    private Database $post_images_db;
    private array $posts;
    private array $user;
    private Database $users_db;
    private Database $db;
    private Database $db_post_like;
    private Database $db_comments;

    public function __construct()
    {
        parent::__construct();
//        $this->lazy_load_post_construct();
        $this->set_current_url_pattern();
        $this->db = new Database();
        $this->posts_db = clone $this->db->table_name('posts');
        $this->post_images_db = clone $this->db->table_name('post_image');
        $this->users_db = clone $this->db->table_name('users');
        $this->db_post_like = clone $this->db->table_name('post_like');
        $this->db_comments = clone $this->db->table_name('comments');
    }

    public function __destruct()
    {
        $this->db->close();
    }

    private function get(){
        $uid = $_GET['user_g']['id'];
        $this->posts = get_subscriptions_posts($uid, 0, 2, $this->posts_db);
        $this->user = $_GET['user_g'];
    }

    public function handle(): void{
        $this->get();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->render('home.html', array(
            'posts' => $this->posts,
            'users_db' => $this->users_db,
            'post_image_db' => $this->post_images_db,
            'db_post_like' => $this->db_post_like,
            'db_comments' => $this->db_comments,
        ));
    }
}
