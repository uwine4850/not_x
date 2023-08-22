<?php
require_once 'utils/handler.php';

class E404Handler extends BaseHandler{
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->render('errors/404.html', array());
    }
}