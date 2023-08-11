<?php
require_once 'utils/handler.php';

class ProfileEditHandler extends BaseHandler{

    public function handle(): void
    {
        $this->render('profile_edit.html', array());
    }
}
