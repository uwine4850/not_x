<?php
require_once 'utils/handler.php';

class PostEditHandler extends BaseHandler{

    public function handle(): void
    {
        $this->render('post_edit.html', array());
    }
}
