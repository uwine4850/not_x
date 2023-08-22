<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/chat/chat_utils.php';
require_once 'handlers/twig_functions.php';
require_once 'handlers/profile/profile_utils.php';

class ChatListHandler extends BaseHandler{
    use \TwigFunc\GlobalFunc;
    use HandlerUtils;

    private Database $db_chat_rooms;
    private Database $db_chat_messages;

    public function __construct(){
        parent::__construct();
        $this->db_chat_rooms = new Database('chat_rooms');
        $this->db_chat_messages = new Database('chat_messages');
        $this->set_current_url_pattern();
    }

    public function get_last_msg(int $room_id): array{
        return $this->db_chat_messages->all_where("parent_chat=$room_id ORDER BY id DESC", 1)[0];
    }

    public function handle(): void
    {
        $this->enable_global_func($this->twig);
        $this->twig->addFunction((new Twig\TwigFunction('get_last_msg', [$this, 'get_last_msg'])));
        $this->twig->addFunction((new Twig\TwigFunction('interlocutor', 'get_user_interlocutor')));
        $this->twig->addFunction((new Twig\TwigFunction('get_user_by_id', 'get_user_by_id')));
        $this->render('chat/chat_list.html', array(
            'rooms' => get_user_chat_rooms($_GET['user_g']['id']),
        ));
    }
}