<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/post_utils.php';
require_once 'handlers/profile/profile_utils.php';

class LazyLoadPostHandler extends BaseHandler{
    use HandlerUtils;
    private Database $posts_db;
    private array $posts;
    private array $user;
    private Database $post_images_db;

    public function __construct()
    {
        parent::__construct();
        $this->posts_db = new Database('posts');
        $this->post_images_db = new Database('post_image');
    }

    private function get(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'GET'){
            return;
        }
        $post_id = $_GET['last_post_id'];
        $uid = $_GET['user_id'];
        $this->posts = load_user_posts($uid, $post_id, 2);
        $this->user = get_user_by_id($uid);
    }

    public function get_post_image(int $post_id): array{
        return $this->post_images_db->all_where("parent_post=$post_id");
    }

    public function handle(): void
    {
        $this->get();
        $this->twig->addFunction((new \Twig\TwigFunction("comments_count", "get_count_of_comment_by_post_id")));
        $this->twig->addFunction((new \Twig\TwigFunction("post_like_count", "post_like_count")));
        $this->twig->addFunction((new \Twig\TwigFunction("is_liked", "is_liked")));
        $this->twig->addFunction((new \Twig\TwigFunction("media_img", [$this, "get_path_to_media_image"])));
        $this->twig->addFunction((new \Twig\TwigFunction('get_post_image', [$this, 'get_post_image'])));
        $this->render('post.html', array(
            'posts' => $this->posts,
            'user' => $this->user,
        ));
    }
}