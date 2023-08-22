<?php
require_once "utils/handler.php";
require_once 'utils/database.php';
require_once 'handlers/twig_functions.php';

class SearchProfileHandler extends BaseHandler{
    use \TwigFunc\GlobalFunc;

    private Database $db_users;
    private array $users = array();

    public function __construct()
    {
        parent::__construct();
        $this->db_users = new Database('users');
    }

    private function post(){
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            return;
        }
        $post_data = array();
        try {
            $post_data = validate_post_data(['search-username']);
        } catch (FormFieldNotExist $e) {
            return;
        }

        $username = $post_data['search-username'];
        $this->users = $this->get_users($username);
    }

    private function get_users(string $username): array{
        $db_users = $this->db_users->all_where("username LIKE '%$username%'", 10);
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
        ));
    }
}
