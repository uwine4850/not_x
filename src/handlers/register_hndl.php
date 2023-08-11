<?php
require_once 'utils/handler.php';

class RegisterHandler extends BaseHandler{
    public function handle(): void
    {
        $this->render('register.html', array());
    }
}
