<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class SubscriptionsHandler extends BaseHandler{
    use HandlerUtils;
    private Database $sub_db;
    private Database $user_db;

    public function __construct()
    {
        parent::__construct();
        $this->sub_db = new Database('subscriptions');
        $this->user_db = new Database('users');
    }

    /**
     * Returns a list of all subscriptions.
     * @return array
     */
    private function get_subscriptions(): array{
        $id = $_GET['user_g']['id'];
        return $this->sub_db->all_where("subscriber_id=$id");
    }

    /**
     * Data about the profile to which the user is subscribed by id.
     * @param string $id
     * @return mixed
     */
    public function get_subscribe_profile_by_id(string $id): array{
        $user = $this->user_db->all_where("id=$id")[0];
        unset($user['password']);
        return $user;
    }

    public function handle(): void
    {
        $this->twig->addFunction((new \Twig\TwigFunction("media_img", [$this, "get_path_to_media_image"])));
        $this->twig->addFunction((new \Twig\TwigFunction('get_subscribe_profile_by_id', [$this, 'get_subscribe_profile_by_id'])));
        $this->render('subscriptions.html', array('subscriptions' => $this->get_subscriptions()));
    }
}