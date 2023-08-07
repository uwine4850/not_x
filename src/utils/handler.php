<?php
require_once "vendor/autoload.php";
require_once "load_env.php";

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

abstract class BaseHandler{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(getenv("PATH_TO_SRC") . 'templates');
        $this->twig = new Environment($loader, [
            'cache' => false,
        ]);
        $this->twig->addFunction((new TwigFunction("static", [$this, "static"])));
    }

    /**
     * Returns the path to the static file if found.ы
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
 * Retrieving child classes of the parent's class.
 * @param $parentClassName string The name of the parent's class.
 * @param $filePath string Path to the file where the classes will be searched.
 * @return array An array of child class names.
 */
function getChildClasses(string $parentClassName, string $filePath): array {
    if (!file_exists($filePath)) {
        throw new Exception("File not found: $filePath");
    }

    // Include the PHP file to make its classes available for reflection
    require $filePath;

    $allClasses = get_declared_classes();

    // Filter out only the child classes of the parent class
    return array_filter($allClasses, function ($className) use ($parentClassName) {
        try {
            $reflectionClass = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw $e;
        }
        return $reflectionClass->isSubclassOf($parentClassName);
    });
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
        $handler_instance = new $handler_name;
        $handler_instance->handle();
    } catch (Exception $e) {
        throw $e;
    }
}
