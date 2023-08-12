<?php

require_once 'utils/handler.php';

class HomeHandler extends BaseHandler
{
    private function post(): void{
        if (array_key_exists('log-out', $_POST)){
            setcookie('UID', '', time()-1);
            header("Location: /login");
        }
    }
    public function handle(): void
    {
        $this->post();
        $this->render('home.html', array());
    }
}
