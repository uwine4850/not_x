<?php
require_once "load_env.php";

abstract class Middleware{
    abstract public function run(): void;
}

function run_middlewares(): void{
    $files = array_diff(scandir("/var/www/html/middlewares/"), array(".", ".."));
    foreach ($files as $f){
        $path = $_ENV["PATH_TO_MIDDLEWARES"] . $f;
        try {
            $mddls = getChildClasses("Middleware", $path);
            foreach ($mddls as $m){
                $mddl = new $m;
                $mddl->run();
            }
        } catch (Exception $e){
            throw $e;
        }
    }
}
