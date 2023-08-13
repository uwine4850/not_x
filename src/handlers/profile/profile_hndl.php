<?php
require_once "utils/handler.php";
require_once 'utils/database.php';
require_once 'profile_utils.php';

class HomeHndl extends BaseHandler {
    public function handle(): void
    {
        $this->render("profile.html", array(
            'user' => get_user_data(),
            'is_current_user_profile' => is_current_user_profile(),
        ));
    }
}
