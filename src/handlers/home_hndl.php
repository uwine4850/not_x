<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/lazyload_post.php';
require_once 'handlers/twig_functions.php';
require_once 'config.php';

class HomeHandler extends BaseHandler{
    use HandlerUtils;
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;
    use ConnectToAllTables;

    private array $posts;
    private array $user;
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->set_current_url_pattern();
        $this->db = new Database();
        $this->connect_to_all_tables($this->db);
    }

    public function __destruct()
    {
        $this->db->close();
    }

    private function get(){
        $uid = $_GET['user_g']['id'];
        $this->posts = get_subscriptions_posts($uid, 0, config\LOAD_POST_COUNT, $this->db_posts);
        $this->user = $_GET['user_g'];
    }

    public function handle(): void{
        $this->get();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->render('home.html', array(
            'posts' => $this->posts,
        ) + $this->get_post_tables());
    }
}
