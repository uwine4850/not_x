<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/lazyload_post.php';
require_once 'handlers/twig_functions.php';

class HomeHandler extends BaseHandler{
    use HandlerUtils;
    use LazyLoadPost;
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    private Database $posts_db;
    private Database $post_images_db;
    private array $posts;
    private array $user;

    public function __construct()
    {
        parent::__construct();
        $this->set_current_url_pattern();
        $this->posts_db = new Database('posts');
        $this->post_images_db = new Database('post_image');
    }

    private function get(){
        $uid = $_GET['user_g']['id'];
        $this->posts = $this->get_subscriptions_posts($uid, 0, 2);
        $this->user = $_GET['user_g'];
    }

    public function handle(): void{
        $this->get();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->render('home.html', array(
            'posts' => $this->posts,
        ));
    }
}
