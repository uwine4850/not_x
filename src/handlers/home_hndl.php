<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/post_utils.php';
require_once 'handlers/profile/profile_utils.php';
require_once 'handlers/post/lazyload_post.php';

class HomeHandler extends BaseHandler{
    use HandlerUtils;
    use LazyLoadPost;
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

    public function get_post_image(int $post_id): array{
        return $this->post_images_db->all_where("parent_post=$post_id");
    }

    private function get(){
        $uid = $_GET['user_g']['id'];
        $this->posts = $this->get_subscriptions_posts($uid, 0, 2);
        $this->user = $_GET['user_g'];
    }

    public function handle(): void{
        $this->get();
        $this->twig->addFunction((new \Twig\TwigFunction("get_post_user", "get_user_by_id")));
        $this->twig->addFunction((new \Twig\TwigFunction("comments_count", "get_count_of_comment_by_post_id")));
        $this->twig->addFunction((new \Twig\TwigFunction("post_like_count", "post_like_count")));
        $this->twig->addFunction((new \Twig\TwigFunction("is_liked", "is_liked")));
        $this->twig->addFunction((new \Twig\TwigFunction("media_img", [$this, "get_path_to_media_image"])));
        $this->twig->addFunction((new \Twig\TwigFunction('get_post_image', [$this, 'get_post_image'])));
        $this->render('home.html', array(
            'posts' => $this->posts,
        ));
    }
}
