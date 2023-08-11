<?php
require_once 'utils/handler.php';

class LoginHandler extends BaseHandler{

    public function handle(): void
    {
        $this->render('login.html', array());
    }
}
