<?php
require_once 'utils/handler.php';

class CreatePostHandler extends BaseHandler{
    public function handle(): void
    {
        $this->render('create_post.html', array());
    }
}
