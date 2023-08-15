<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'profile_utils.php';

class ProfileEditHandler extends BaseHandler{
    use HandlerUtils;
    private string $form_error = "";
    private const form_fields = array('profile-name', 'description');
    private Database $db;
    private array $user;
    private const PATH_TO_MEDIA_USERS = '/var/www/html/media/users/';

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database('users');
        $this->user = get_user_data();
    }

    private function post(): void{
        $post_data = array();
        try {
            $post_data = validate_post_data(self::form_fields);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // Obtaining data for updating
        $insert_values = array();
        try {
            if ($post_data){
                $insert_values = array_to_db_assoc_array($post_data, array(
                    FormDbField::make('profile-name', 'name'),
                    FormDbField::make('description', 'description')->is_empty(),
                ));
            }
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // If the form is submitted
        if ($post_data){
            if (isset($_POST['profile-image-del'])){
                // Deleting an image file and clearing a column in the database
                $insert_values['path_to_user_image'] = '';
                $this->delete_image($this->user['path_to_user_image']);
            } else{
                // Delete old image
                if (!empty($_FILES['profile-image']['name'])){
                    $this->delete_image($this->user['path_to_user_image']);
                }
                // Save new image
                $save_path = $this->save_file();
                if ($save_path == '' && !empty($_FILES['profile_image']['name'])){
                    return;
                }
                if ($save_path){
                    $insert_values['path_to_user_image'] = $save_path;
                }
            }

            // Updates user data in the database.
            $this->db->update($this->user['id'], $insert_values);
            $username = $this->user['username'];
            header("Location: /profile/$username");
        }
    }

    /**
     * Removes the image from the foil system.
     * @param string $path Path to image.
     * @return void
     */
    private function delete_image(string $path): void{
        if (file_exists($path)){
            unlink($path);
        }
    }

    /**
     * Saving the image. Some checks are performed during this process.
     *
     * Creating a directory to save images if it does not exist.
     * @return string
     */
    private function save_file(): string{
        $save_path = '';
        // check exist user dir
        $username = $this->user['username'];
        $user_dir = self::PATH_TO_MEDIA_USERS . $username;
        if (!is_dir($user_dir)){
            $this->form_error = "User dir not exist.";
            return $save_path;
        }

        // create image dir
        $image_dir = implode(DIRECTORY_SEPARATOR, [$user_dir, 'profile_image']);
        if (!is_dir($image_dir)){
            mkdir($image_dir);
        }

        // save image
        try {
            $save_path = save_image($_FILES['profile-image'], $image_dir);
        } catch (FileTypeError|ExceedMaximumFileSize|ErrorUploadingFile $e) {
            $this->form_error = $e->getMessage();
            return $save_path;
        }
        return $save_path;
    }

    public function handle(): void
    {
        $this->post();
        $this->twig->addFunction((new \Twig\TwigFunction("media_img", [$this, "get_path_to_media_image"])));
        $this->render('profile_edit.html', array('error' => $this->form_error, 'user' => $this->user));
    }
}
