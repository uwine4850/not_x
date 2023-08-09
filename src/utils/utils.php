<?php

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