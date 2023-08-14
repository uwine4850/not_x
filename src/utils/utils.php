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

class FormFieldNotExist extends Exception{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Checks the form for the required fields.
 * @param array $form_fields Fields that must be in the $_POST array.
 * @return array An array of found fields. If everything is valid.
 * @throws FormFieldNotExist Error if the field is not found.
 */
function validate_post_data(array $form_fields): array{
    $post_data = array();
    $err = '';
    foreach (array_keys($_POST) as $field_name){
        if ($err != ''){
            throw new FormFieldNotExist($err);
        }
        foreach ($form_fields as $ff){
            if (array_key_exists($ff, $_POST)){
                $post_data[$field_name] = htmlspecialchars($_POST[$field_name]);
            } else{
                $err = "Key $ff not exist.";
            }
        }
    }
    if ($err != ''){
        throw new FormFieldNotExist($err);
    }
    return $post_data;
}

class ArrayValueIsEmpty extends Exception{
}

/**
 * Returns the nag value of the array if found. If the value is not found it causes an error.
 * @param array $arr Associative Data Array.
 * @param string $key
 * @return mixed The value of the array key.
 * @throws ArrayValueIsEmpty Error if the key has no value.
 */
function get_not_empty_value(array $arr, string $key): mixed{
    if (empty($arr[$key])){
        throw new ArrayValueIsEmpty("Value $key is empty.");
    }
    return $arr[$key];
}

/**
 * Converts form data array values into an associative array with fields processed and renamed as table fields.
 *
 * Instances of the FormDbField class must be used as array data.
 * @param array $arr An associative array of form data.
 * @param array $form_fields An array of instances of the FormDbField class.
 * @return array A specially formatted associative array.
 * @throws ArrayValueIsEmpty Error if the key has no value.
 */
function array_to_db_assoc_array(array $arr, array $form_fields): array{
    $new_arr = array();
    foreach (array_keys($arr) as $key){
        foreach ($form_fields as $form_field){
            if ($form_field instanceof FormDbField && $form_field->get_form_field_name() == $key){
                try {
                    $val = $form_field->parse_value($arr, $key);
                    if ($val != ""){
                        $new_arr[$form_field->get_db_name()] = $val;
                    } else{
                        continue;
                    }
                } catch (ArrayValueIsEmpty $e) {
                    throw $e;
                }
            }
        }
    }
    return $new_arr;
}

trait FormDbFieldOperations{
    /**
     * Hashing a value as a password.
     * @param string $value
     * @return string
     */
    public function pass_hash(string $value): string{
        return password_hash($value, PASSWORD_DEFAULT);
    }
}


/**
 * The class represents a field from a form to convert to fields for the database.
 */
class FormDbField{
    use FormDbFieldOperations;
    private string $form_field_name;
    private string $db_name;

    public bool $_empty = false;
    public bool $_password_hash = false;

    /**
     * @param string $form_field_name The name of the form field.
     * @param string $db_name The name of the field in the database.
     */
    public function __construct(string $form_field_name, string $db_name)
    {
        $this->form_field_name = $form_field_name;
        $this->db_name = $db_name;
        return $this;
    }

    /**
     * Creates a new instance of the FormDbField class.
     * @param string $form_field_name The name of the form field.
     * @param string $db_name The name of the field in the database.
     * @return FormDbField An instance of the FormDbField class.
     */
    public static function make(string $form_field_name, string $db_name): FormDbField{
        return new self($form_field_name, $db_name);
    }

    /**
     * Processing the value of a form field.
     * @param array $arr An array of form fields.
     * @param string $key Key to the meaning.
     * @return string Processed value of a form field.
     * @throws ArrayValueIsEmpty Error if the key has no value.
     */
    public function parse_value(array $arr, string $key): string{
        $value = "";
        if ($this->_empty){
            if (empty($arr[$key])){
                return $value;
            } else{
                $value = $arr[$key];
            }
        } else{
            $value = get_not_empty_value($arr, $key);
        }
        if ($this->_password_hash){
            $value = $this->pass_hash($value);
        }
        return $value;
    }

    /**
     * Allow the field to have no value.
     * @return $this
     */
    public function is_empty(): static{
        $this->_empty = true;
        return $this;
    }

    /**
     * Create a password hash for this form field.
     * @return $this
     */
    public function is_password_hash(): static{
        $this->_password_hash = true;
        return $this;
    }

    public function get_form_field_name(): string{
        return $this->form_field_name;
    }

    public function get_db_name(): string{
        return $this->db_name;
    }

}

interface SaveFile{
    /**
     * @param array $file_data Data about a particular file from the _FILES array.
     * @param string $path_to_dir Path to the file saving directory.
     */
    public function __construct(array $file_data, string $path_to_dir);

    /**
     * The main function that saves the file and runs all the necessary checks.
     * @return void
     */
    public function save():void;

    /**
     * Enables the file name hashing option.
     * @return $this
     */
    public function hash_name():static;

    /**
     * Returns the path to the saved file.
     * @return string
     */
    public function get_save_path():string;
}

class SaveImage implements SaveFile {
    private array $file_data;
    private string $path_to_dir;
    private const IMAGE_TYPES = array('jpeg', 'jpg', 'png');
    private bool $is_hash_name = false;
    private const FILE_MAX_SIZE = 20_971_520; // 20 MB
    private string $save_path = '';

    public function __construct(array $file_data, string $path_to_dir)
    {
        $this->file_data = $file_data;
        $this->path_to_dir = $path_to_dir;
    }

    public function save(): void
    {
        if (!$this->validate_image_size()){
            throw new ExceedMaximumFileSize("Exceed the maximum file size. Maximum size 20 mb.");
        }
        $filetype = $this->get_image_type();
        if (!$this->validate_image_types($filetype)){
            throw new FileTypeError("The .$filetype file type is not supported.");
        }
        $filename = $this->get_filename();
        $full_path_to_file = implode(DIRECTORY_SEPARATOR, [$this->path_to_dir, $filename]);
        if (!move_uploaded_file($this->file_data['tmp_name'], $full_path_to_file)){
            $f = $this->file_data['name'];
            throw new ErrorUploadingFile("Error while uploading the $f file.");
        }
        $this->save_path = $full_path_to_file;
    }

    /**
     * Returns the processed filename of the file.
     * @return string
     * @throws Exception
     */
    private function get_filename(): string{
        $salt = bin2hex(random_bytes(4));
        $filename = pathinfo($this->file_data['name'], PATHINFO_FILENAME) . $salt;
        if ($this->is_hash_name){
            $filename = substr(md5($this->file_data['name'] . $salt), 0, 8);
        }
        $filename = $filename . '.' . $this->get_image_type();
        if (file_exists($this->path_to_dir . $filename)){
            $filename = $this->get_filename();
        }
        return $filename;
    }

    /**
     * Image Type Validation.
     * @param string $type The type of image to be uploaded.
     * @return bool
     */
    private function validate_image_types(string $type): bool{
        if (in_array($type, self::IMAGE_TYPES)){
            return true;
        }
        return false;
    }

    /**
     * Finds and returns the type of image to be loaded.
     * @return string
     */
    private function get_image_type(): string{
        return pathinfo($this->file_data['name'], PATHINFO_EXTENSION);
    }

    /**
     * Image Size Validation.
     * @return bool
     */
    private function validate_image_size(): bool{
        if ($this->file_data['size'] > self::FILE_MAX_SIZE){
            return false;
        }
        return true;
    }

    public function hash_name(): static
    {
        $this->is_hash_name = true;
        return $this;
    }

    public function get_save_path(): string
    {
        return $this->save_path;
    }
}

class FileTypeError extends Exception{}

class ExceedMaximumFileSize extends Exception{}

class ErrorUploadingFile extends Exception{}