<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/twig_functions.php';

class LoginHandler extends BaseHandler{
    use \TwigFunc\GlobalFunc;

    private string $form_error = '';
    private const form_field = array('profile-username', 'profile-password');
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database('users');
    }

    public function __destruct()
    {
        $this->db->close();
    }

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        // validate data
        try {
            validate_csrf_token($_POST);
        } catch (ErrInvalidCsrfToken $e) {
            $this->form_error = $e->getMessage();
            return;
        }

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
        $this->enable_global_func($this->twig);
        $this->post();
        $this->render('login.html', array('form_error' =>$this->form_error));
    }
}
