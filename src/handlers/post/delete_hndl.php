<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class PostDeleteHandler extends BaseHandler {
    private Database $posts_db;

    public function __construct()
    {
        parent::__construct();
        $this->posts_db = new Database('posts');
    }

    public function __destruct()
    {
        $this->posts_db->close();
    }

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        $post_data = array();
        try {
            $post_data = validate_post_data(['post-del-id']);
        } catch (FormFieldNotExist $e) {
            return;
        }
        if (!$post_data){
            echo "You can't delete this post.";
            return;
        }

        $post_id = $post_data['post-del-id'];
        $uid = $_GET['user_g']['id'];
        if (!empty($this->posts_db->all_where("id=$post_id AND user=$uid"))){
            $this->posts_db->delete($post_id);
            session_start();
            $l = $_SESSION['current_url'];
            header("Location: $l");
        }
    }

    public function handle(): void
    {
        $this->post();
    }
}
