<?php

require_once 'utils/handler.php';
require_once 'utils/database.php';

class HomeHandler extends BaseHandler
{
    public function handle(): void
    {
        $this->render('home.html', array());
    }
}
