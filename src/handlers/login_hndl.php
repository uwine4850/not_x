<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class LoginHandler extends BaseHandler{
    private $form_error;
    private const form_field = array('profile-username', 'profile-password');
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database('users');
    }

    private function post(): void{
        // validate data
        $post_data = array();
        try {
            $post_data = validate_post_data(self::form_field);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        try {
            if ($post_data){
                $username = get_not_empty_value($post_data, 'profile-username');
                $password = get_not_empty_value($post_data, 'profile-password');
                $user = $this->db->all_where("username='$username'");
                if (empty($user)){
                    $this->form_error = "User $username not exist.";
                    return;
                }
                if (password_verify($password, $user[0]['password'])){
                    setcookie('UID', $user[0]['id']);
                    header('Location: /');
                }
            }
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }

    }

    public function handle(): void
    {
        $this->post();
        $this->render('login.html', array('form_error' =>$this->form_error));
    }
}
