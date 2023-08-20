<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/profile/profile_utils.php';
require_once 'handlers/twig_functions.php';

class PostViewHandler extends BaseHandler{
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    private Database $posts_db;
    private Database $users_db;
    private Database $comments_db;
    private Database $comments_answer_db;
    private array $post;
    private array $user;
    private string $form_error = '';
    private array $comments;

    public function __construct()
    {
        parent::__construct();
        $this->posts_db = new Database('posts');
        $this->users_db = new Database('users');
        $this->comments_db = new Database('comments');
        $this->comments_answer_db = new Database('comments_answer');
        $this->post = $this->get_post();
        if ($this->post){
            $this->user = get_user_by_id($this->post['user']);
            $post_id = $this->post['id'];
            $this->comments = $this->posts_db->all_fk('comments',
                'parent_post_id', where: "posts.id=$post_id");
        }
    }

    private function get_post(): array{
        $post_id = $_GET['post_id'];
        $post = $this->posts_db->all_where("id=$post_id");
        if (empty($post)){
            return array();
        }
        return $post[0];
    }

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        $post_data = array();
        try {
            $post_data = validate_post_data(['answer_id', 'comment-text-input']);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        $post_data['parent_post_id'] = $this->post['id'];
        $post_data['user_id'] = $_GET['user_g']['id'];
        $insert_data = array();
        try {
            if(empty($post_data['answer_id'])){
                $insert_data = array_to_db_assoc_array($post_data, array(
                    FormDbField::make('comment-text-input', 'text'),
                    FormDbField::make('user_id', 'user_id'),
                    FormDbField::make('parent_post_id', 'parent_post_id'),
                ));
            } else{
                $insert_data = array_to_db_assoc_array($post_data, array(
                    FormDbField::make('comment-text-input', 'text'),
                    FormDbField::make('user_id', 'user_id'),
                    FormDbField::make('answer_id', 'answer_for_comment_id'),
                ));
            }
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // Writing a new comment to the database.
        if (isset($insert_data['parent_post_id'])){
            if (!$this->parent_post_exist($insert_data['parent_post_id'])){
                return;
            }
            if ($this->comments_db->insert($insert_data)){
                $post_id = $this->post['id'];
                header("Location: /post/$post_id");
            }
        }

        // Writing a response to a comment in the database.
        if (isset($insert_data['answer_for_comment_id'])){
            if (!$this->comment_exist($insert_data['answer_for_comment_id'])){
                $this->form_error = 'Answer comment not exist';
                return;
            }
            if ($this->comments_answer_db->insert($insert_data)){
                $post_id = $this->post['id'];
                header("Location: /post/$post_id");
            };
        }
    }

    /**
     * Checks to see if the comment exists.
     * @param int $comment_id
     * @return bool
     */
    private function comment_exist(int $comment_id): bool{
        if (empty($this->comments_db->all_where("id=$comment_id"))){
            return false;
        }
        return true;
    }

    /**
     * Checks if a post exists to link a comment to it.
     * @param int $parent_post_id ID of the post to which the comment is bound.
     * @return bool
     */
    private function parent_post_exist(int $parent_post_id): bool{
        if (empty($this->posts_db->all_where("id=$parent_post_id"))){
            return false;
        }
        return true;
    }

    public function handle(): void
    {
        if (!$this->post){
            return;
        }
        $this->post();
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->twig->addFunction((new \Twig\TwigFunction("get_answer_comments", "get_answer_comments")));
        $this->render('post/post_view.html', array(
            'post' => $this->post,
            'user' => $this->user,
            'error' => $this->form_error,
            'comments' => $this->comments,
        ));
    }
}
