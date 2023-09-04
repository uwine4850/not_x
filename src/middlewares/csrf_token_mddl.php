<?php

require_once "utils/csrf.php";
require_once "utils/middleware.php";

class CsrfTokenMddl extends Middleware{
    public function run(): void
    {
        csrf\csrf_lifetime();
    }
}
