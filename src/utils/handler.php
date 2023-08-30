<?php
require_once "vendor/autoload.php";
require_once "load_env.php";
require_once "middleware.php";

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

abstract class BaseHandler{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(getenv("PATH_TO_SRC") . 'templates');
        $this->twig = new Environment($loader, [
            'cache' => false,
        ]);
        $this->twig->addFunction((new TwigFunction("static", [$this, "static"])));
        if (!empty($_GET['user_g'])){
            $this->twig->addGlobal('user_g', $_GET['user_g']);
        }
        if (!empty($_GET['msgn'])){
            $this->twig->addGlobal('msgn', $_GET['msgn']);
        }
    }

    protected function is_ajax(): bool{
        if (isset($_POST['is_ajax'])){
            return true;
        }
        return false;
    }

    protected function ajax_response(array $resp): string{
        return json_encode($resp);
    }

    /**
     * Returns the path to the static file if found.
     * @param string $filename The name of the static file.
     * @return string File Path.
     */
    public function static(string $filename): string{
        $filepath = "/" . $_ENV["PATH_TO_STATIC"] . "/" . $filename;
        if (file_exists($_ENV["PATH_TO_SRC"] . $filepath)){
            return $filepath;
        } else{
            throw new Exception("File: $filepath not exist.");
        }
    }

    /**
     * Starts rendering a file using the templating engine.
     * @param string $template_name Template Name. Must be located in the appropriate directory.
     * @param array $args Template Variable.
     * @return void
     */
    protected function render(string $template_name, array $args): void{
        try {
            echo $this->twig->render($template_name, $args);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw $e;
        }
    }
    abstract public function handle(): void;
}

/**
 * Runs the handle method of the handler.
 *
 * <strong>The file with the handler class must be attached in a previously favorite way.</strong>
 * @param string $handler_name The name of the handler class.
 * @return void
 */
function exec_handler(string $handler_name): void{
    try {
        run_middlewares();
        $handler_instance = new $handler_name;
        $handler_instance->handle();
    } catch (Exception $e) {
        throw $e;
    }
}

trait HandlerUtils{
    /**
     * Starts a session if it has not been started before.
     * @return void
     */
    private function start_session():void {
        if (session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    /**
     * Sets some url data in the session.
     * @return void
     */
    public function set_current_url_pattern():void {
       $this->start_session();
       $_SESSION['curr_url_pattern'] = $_GET["url_pattern"];
       $_SESSION['current_url'] = $_SERVER['REQUEST_URI'];
   }

    /**
     * Sets the data that should go into the js.
     * @param array $data
     * @return void
     */
    public function set_custom_js_data(array $data):void {
       $this->start_session();
       $_SESSION = array_merge($_SESSION, $data);
   }
}
