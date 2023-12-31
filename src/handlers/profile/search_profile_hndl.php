<?php
require_once "utils/handler.php";
require_once 'utils/database.php';
require_once 'handlers/twig_functions.php';
require_once 'config.php';

class SearchProfileHandler extends BaseHandler{
    use \TwigFunc\GlobalFunc;

    private string $error = '';
    private Database $db_users;
    private array $users = array();

    public function __construct()
    {
        parent::__construct();
        $this->db_users = new Database('users');
    }

    public function __destruct()
    {
        $this->db_users->close();
    }

    private function post(): void{
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }

        try {
            validate_csrf_token($_POST);
        } catch (ErrInvalidCsrfToken $e) {
            $this->error = $e->getMessage();
            return;
        }

        $post_data = array();
        try {
            $post_data = validate_post_data(['search-username']);
        } catch (FormFieldNotExist $e) {
            $this->error = $e->getMessage();
            return;
        }

        $username = $post_data['search-username'];
        $this->users = $this->get_users($username);
    }

    private function get_users(string $username): array{
        $db_users = $this->db_users->all_where("username LIKE '%$username%'", config\SEARCH_PROFILE_COUNT);
        $users = array();
        foreach ($db_users as $u){
            unset($u['password']);
            $users[] = $u;
        }
        return $users;
    }

    public function handle(): void{
        $this->enable_global_func($this->twig);
        $this->post();
        $this->render('profile/profile_search.html', array(
            'users' => $this->users,
            'error' => $this->error,
        ));
    }
}
