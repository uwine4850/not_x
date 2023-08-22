<?php
require_once "utils/middleware.php";
require_once 'handlers/profile/profile_utils.php';

class ProfileEditMddl extends Middleware{
    public function run(): void
    {
        if ($_GET["url_pattern"] == '/profile/{username}/edit'){
            if (!is_current_user_profile()){
                require_once 'utils/router.php';
                render_403();
                exit();
            }
        }
    }
}