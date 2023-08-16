<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class PostLikeHandler extends BaseHandler{
    private Database $post_like_db;
    private Database $posts_db;
    private string $form_error = '';
    private array $user_g;
    public function __construct(){
        parent::__construct();
        $this->post_like_db = new Database('post_like');
        $this->posts_db = new Database('posts');
        $this->user_g = $_GET['user_g'];
    }

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        // Check if the required fields exist.
        $post_data = array();
        try {
            $post_data = validate_post_data(['post_like_id']);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // Convert fields to an array for a database.
        $post_data['user_id'] = $this->user_g['id'];
        $insert_data = array();
        try {
            $insert_data = array_to_db_assoc_array($post_data, array(
                FormDbField::make('post_like_id', 'post_id'),
                FormDbField::make('user_id', 'user_id'),
            ));
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // Checking if the post id of which is submitted exists in the form.
        if (!$this->post_exist($insert_data['post_id'])){
            $this->form_error = 'Post not exist.';
            return;
        }

        // Inserting and deleting data from the database.
        $luser_id = $insert_data['user_id'];
        $lpost_id = $insert_data['post_id'];
        $post_like_id = $this->post_like_db->all_where("user_id=$luser_id AND post_id=$lpost_id");
        if (empty($post_like_id)){
            $this->post_like_db->insert($insert_data);
        } else{
            $this->post_like_db->delete($post_like_id[0]['id']);
        }
    }

    private function post_exist(string $post_id): bool{
        if (!empty($this->posts_db->all_where("id=$post_id"))){
            return true;
        }
        return false;
    }

    public function handle(): void
    {
        $this->post();
        echo $this->ajax_response(array("ok" => true, 'error' => $this->form_error));
    }
}
