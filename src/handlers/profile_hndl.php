<?php
require_once "utils/handler.php";

class HomeHnd extends BaseHandler {
    public function handle(): void
    {
        $this->render("profile.html", array());
    }
}