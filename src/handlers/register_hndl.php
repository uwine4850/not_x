<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class RegisterHandler extends BaseHandler{
    private $form_error;
    private const form_fields = array('profile-name', 'profile-username', 'profile-password-reg', 'profile-password-reg-again');
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database('users');
    }

    private function validate_username(string $username): bool{
        $res = $this->db->all_where("username='$username'");
        if (empty($res)){
            return true;
        } else{
            return false;
        }
    }

    private function validate_password(string $password): bool{
        if (strlen($password) < 6){
            return false;
        }
        return true;
    }

    private function post(): void
    {
        //Form Data Validation.
        $post_data = array();
        try {
            $post_data = validate_post_data(self::form_fields);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // Obtaining data for insertion, its validation and processing.
        $insert_values = array();
            try {
            if ($post_data){
                $insert_values = array_to_db_assoc_array($post_data, array(
                    FormDbField::make('profile-name', 'name'),
                    FormDbField::make('profile-username', 'username'),
                    FormDbField::make('profile-password-reg', 'password')->is_password_hash(),
                ));
            }
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        if ($post_data){
            // If there is the right data in the form, validation of some fields.
            // If all is well, create a new user and redirect to the login page.
            $username = $insert_values['username'];
            $password = $post_data['profile-password-reg'];
            $username_ok = $this->validate_username($username);
            $password_ok = $this->validate_password($password);
            if (!$username_ok){
                $this->form_error = "Username $username already exist.";
                return;
            }
            if (!$password_ok){
                $this->form_error = "Password length is less than six.";
                return;
            }
            if (strcmp($post_data['profile-password-reg'], $post_data['profile-password-reg-again']) == 1){
                $this->form_error = "Passwords mismatch.";
                return;
            }
            if ($this->db->insert($insert_values)){
                header("Location: /login");
            }
        }
    }
    public function handle(): void
    {
        $this->post();
        $this->render('register.html', array('form_error' =>$this->form_error));
    }
}
