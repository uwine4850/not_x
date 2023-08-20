<?php
require_once "utils/handler.php";
require_once 'utils/database.php';
require_once 'profile_utils.php';
require_once 'handlers/twig_functions.php';

class ProfileHndl extends BaseHandler {
    use \TwigFunc\PostFunc;
    use \TwigFunc\GlobalFunc;

    use HandlerUtils;
    private Database $sub_db;
    private Database $users_db;
    private Database $posts_db;
    private Database $post_images_db;
    private array $current_user_data;

    public function __construct()
    {
        parent::__construct();
        $this->set_current_url_pattern();
        $this->sub_db = new Database('subscriptions');
        $this->users_db = new Database('users');
        $this->posts_db = new Database('posts');
        $this->post_images_db = new Database('post_image');
        $this->current_user_data = get_user_data();
    }

    /**
     * Check if the user of the current session is subscribed to another user.
     * @return bool
     */
    public function user_subscribed(): bool{
        $subscriber_id = $_GET['user_g']['id'];
        $profile_id = $this->current_user_data['id'];
        if (empty(get_subscription($this->sub_db, $subscriber_id, $profile_id))){
            return false;
        }
        return true;
    }

    /**
     * Checks if the user accessed in the URL parameters exists.
     * @return bool
     */
    private function check_user_exist(): bool{
        $username = $_GET['username'];
        if (empty($this->users_db->all_where("username='$username'"))){
            return false;
        }
        return true;
    }

    /**
     * Returns the number of subscribers.
     * @return int
     */
    private function get_subscribers(): int{
        $id = $this->current_user_data['id'];
        return $this->sub_db->count("profile_id=$id")[0];
    }

    /**
     * @return array All publications by this user.
     */
    private function get_user_posts(): array{
        $user_id = $this->current_user_data['id'];
        return $this->posts_db->all_where("id <= (SELECT MAX(id) FROM posts) AND user=$user_id ORDER BY posts.id DESC", 2);
    }

    /**
     * @param int $post_id Post ID.
     * @return array List of images of this post.
     */
    public function get_post_image(int $post_id): array{
        return $this->post_images_db->all_where("parent_post=$post_id");
    }

    public function handle(): void
    {
        if (!$this->check_user_exist()){
            return;
        }
        $subscribe = new Subscribe($this->sub_db, $this->users_db, $this->current_user_data);
        $form_error = $subscribe->post_subscribe();

        if ($this->is_ajax()){
            echo $this->ajax_response(array('error' => $form_error));
            return;
        }
        $this->enable_post_func($this->twig);
        $this->enable_global_func($this->twig);
        $this->twig->addFunction((new \Twig\TwigFunction('user_subscribed', [$this, 'user_subscribed'])));
        $this->render("profile/profile.html", array(
            'user' => $this->current_user_data,
            'is_current_user_profile' => is_current_user_profile(),
            'error' => $form_error,
            'subscribers' => $this->get_subscribers(),
            'posts' => $this->get_user_posts(),
        ));
    }
}

class Subscribe{
    private string $form_error = '';
    private Database $sub_db;
    private Database $users_db;
    private array $current_user_data;
    public function __construct(Database $sub_db, Database $users_db, array $current_user_data){
        $this->sub_db = $sub_db;
        $this->users_db = $users_db;
        $this->current_user_data = $current_user_data;
    }

    public function post_subscribe(): string{
        if (!key_exists('is-sub', $_POST)){
            return $this->form_error;
        }

        // Post fields validation
        $post_data = array();
        try {
            $post_data = validate_post_data(['sub-profile-id']);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return $this->form_error;
        }

        // If the $post_data variable is empty, then the form does not exist.
        // Therefore, the execution is terminated.
        if (empty($post_data)){
            return $this->form_error;
        }

        // Check if the id number from the form matches the open user page.
        try {
            $this->validate_id($post_data);
        } catch (ErrorFormIdNotMatch $e) {
            $this->form_error = $e->getMessage();
            return $this->form_error;
        }

        // Validation and conversion of form data to database.
        $post_data['user_g'] = $_GET['user_g']['id'];
        $insert_array = array();
        try {
            $insert_array = array_to_db_assoc_array($post_data,
                array(
                    FormDbField::make('user_g', 'subscriber_id'),
                    FormDbField::make('sub-profile-id', 'profile_id'),
                )
            );
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return $this->form_error;
        }

        $this->subscribe($insert_array);
        return $this->form_error;
    }

    /**
     * Subscribe if it doesn't exist and unsubscribe if it does.
     * @param array $insert_array Table data to be inserted.
     * @return void
     */
    private function subscribe(array $insert_array): void{
        $subscriber_id = $insert_array['subscriber_id'];
        $profile_id = $insert_array['profile_id'];
        $sub_id = get_subscription($this->sub_db, $subscriber_id, $profile_id);
        if (!empty($sub_id)){
            $this->sub_db->delete($sub_id['id']);
        } else{
            $this->sub_db->insert($insert_array);
        }
    }

    /**
     * Check if the id from the form matches the id of the open user.
     * @param array $post_data Form Data.
     * @return void
     * @throws ErrorFormIdNotMatch
     */
    private function validate_id(array $post_data): void
    {
        if ($post_data['sub-profile-id'] != $this->current_user_data['id']){
            throw new ErrorFormIdNotMatch();
        }
    }

}

/**
 * Checks if there is a subscription record for current users in the database.
 * @param Database $sub_db Connection to the table.
 * @param string $subscriber_id id of the person who subscribes to the user.
 * @param string $profile_id id of the person being subscribed to.
 * @return array A row with subscription data. If found.
 */
function get_subscription(Database $sub_db, string $subscriber_id, string $profile_id): array{
    $subscriptions = $sub_db->all_where("subscriber_id='$subscriber_id' AND profile_id='$profile_id'");
    if (!empty($subscriptions)){
        return $subscriptions[0];
    }
    return array();
}

class ErrorFormIdNotMatch extends Exception{
    public function __construct(string $message = "Id from the form does not match the real id.", int $code = 0,
                                ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
