<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

class CreatePostHandler extends BaseHandler{
    private Database $posts_db;
    private Database $post_image_db;
    private Database $users_db;
    private string $form_error = '';
    private const PATH_TO_MEDIA_USERS = '/var/www/html/media/users/';

    public function __construct()
    {
        parent::__construct();
        $this->posts_db = new Database('posts');
        $this->post_image_db = new Database('post_image');
        $this->users_db = new Database('users');
    }

    private function post(): void{
        // Check if the required fields exist.
        $post_data = array();
        try {
            $post_data = validate_post_data(['crt-p-text']);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }
        if (!$post_data){
            return;
        }

        // Formatting fields for insertion into the database.
        $date = new DateTime();
        $post_data['user_g_id'] = $_GET['user_g']['id'];
        $post_data['date'] = $date->format('Y-m-d');
        $insert_data_post = array();
        try {
            $insert_data_post = array_to_db_assoc_array($post_data, array(
                FormDbField::make('user_g_id', 'user'),
                FormDbField::make('crt-p-text', 'text'),
                FormDbField::make('date', 'date'),
            ));
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        // Create a post in the table and return its ID.
        $new_post_id = $this->posts_db->insert($insert_data_post);

        // Saving images if it is sent by form.
        $save_images_path = $this->save_images();
        if ($save_images_path){
            for ($i = 0; $i < count($save_images_path); $i++) {
                $this->post_image_db->insert(array('parent_post' => $new_post_id, 'image' => $save_images_path[$i]));
            }
        }
        if (!$this->form_error){
            header("Location: /");
        }
    }

    private function save_images(): array{
        $username = $_GET['user_g']['username'];
        $user_dir = self::PATH_TO_MEDIA_USERS . $username;
        if (!is_dir($user_dir)){
            $this->form_error = "User dir not exist.";
            return array();
        }

        // Create/get images path.
        $post_img_dir = implode(DIRECTORY_SEPARATOR, [$user_dir, 'post_images']);
        if (!is_dir($post_img_dir)){
            mkdir($post_img_dir);
        }

        // Get and validate images count.
        $file_count = 0;
        if ($_FILES['crt-p-images']['error'][0] != UPLOAD_ERR_NO_FILE){
            $names = $_FILES['crt-p-images']['name'];
            if (count($names) > 2){
                $this->form_error = 'More than 2 files have been uploaded.';
                return array();
            }
            $file_count = count($names);
        }

        // Save images.
        $images_save_path = array();
        try {
            $images_save_path = save_multiple_images($_FILES['crt-p-images'], $post_img_dir, $file_count);
        } catch (ErrorUploadingFile|ExceedMaximumFileSize|FileTypeError $e) {
            $this->form_error = $e->getMessage();
            return array();
        }
        return $images_save_path;
    }

    public function handle(): void
    {
        $this->post();
        $this->render('create_post.html', array('error' => $this->form_error));
    }
}
