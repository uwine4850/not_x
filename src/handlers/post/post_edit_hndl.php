<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/post_utils.php';
require_once 'handlers/twig_functions.php';
require_once 'config.php';

class PostEditHandler extends BaseHandler{
    use \TwigFunc\GlobalFunc;

    private Database $posts_db;
    private Database $post_images_db;
    private array $post;
    private array $post_images;
    private string $form_error = '';

    public function __construct()
    {
        parent::__construct();
        $this->posts_db = new Database('posts');
        $this->post_images_db = new Database('post_image');
        $this->post = $this->get_post();
        $this->post_images = get_post_image($this->post['id']);
    }

    private function get_post(): array{
        $p_id = $_GET['post_id'];
        return $this->posts_db->all_where("id=$p_id")[0];
    }

    private function post(): void{
        $post_data = array();
        try {
            $post_data = validate_post_data(['del_images', 'post-text-edit']);
        } catch (FormFieldNotExist $e) {
            $this->form_error = $e->getMessage();
            return;
        }
        if (empty($post_data)){
            return;
        }

        $insert_data = array();
        try {
            $insert_data = array_to_db_assoc_array($post_data, array(
                FormDbField::make('post-text-edit', 'text'),
            ));
        } catch (ArrayValueIsEmpty $e) {
            $this->form_error = $e->getMessage();
            return;
        }
        $this->posts_db->update($this->post['id'], $insert_data);

        $delete_id = $this->get_delete_id();
        $upload_img_count = $this->get_upload_files_count($_FILES['post-edit-new-images']);
        $current_img_count = count($this->post_images);
        try {
            $this->validate_update_del_count(count($delete_id), $upload_img_count, $current_img_count);
        } catch (ErrDifferentNumberDelUplImages|ErrNumDelImagesToLarge|ErrNumUplImagesToLarge $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        try {
            $this->delete_images($delete_id);
        } catch (ErrImageNotBelongToPost $e) {
            $this->form_error = $e->getMessage();
            return;
        }

        if ($upload_img_count) {
            try {
                $save_paths = $this->save_new_images($upload_img_count);
                foreach ($save_paths as $save_path){
                    $this->post_images_db->insert(array('parent_post' => $this->post['id'], 'image' => $save_path));
                }
            } catch (ErrorUploadingFile|ExceedMaximumFileSize|FileTypeError $e) {
                $this->form_error = $e->getMessage();
                return;
            }
        }
        $post_id = $this->post['id'];
        header("Location: /post/$post_id");
    }

    /**
     * Checks the validity of the number of images uploaded and deleted.
     * @param int $del_count Number of images to be deleted.
     * @param int $upl_count Number of images to upload.
     * @param int $current_image_count The number of images of the post before the update.
     * @return void
     * @throws ErrDifferentNumberDelUplImages
     * @throws ErrNumDelImagesToLarge
     * @throws ErrNumUplImagesToLarge
     */
    private function validate_update_del_count(int $del_count, int $upl_count, int $current_image_count): void{
        // You cannot delete or add more images than the maximum number of images.
        if (config\MAX_IMAGES < $del_count || config\MAX_IMAGES < $upl_count){
            throw new ErrDifferentNumberDelUplImages();
        }

        // The number of images to be deleted must not exceed the number of images of the current post.
        if ($del_count > $current_image_count){
            throw new ErrNumDelImagesToLarge();
        }

        // First, the number of images that will remain after deletion is calculated. If its number is equal to zero,
        // you can add any number of new images within the norm.
        // Accordingly, if the number of images is not equal to zero, the number of new images is added to this number.
        // Accordingly, this number should not exceed the maximum.
        $curr_images_after_delete = $current_image_count - $del_count;
        if ($curr_images_after_delete != 0 && $curr_images_after_delete + $upl_count > config\MAX_IMAGES){
            throw new ErrNumUplImagesToLarge();
        }
    }

    /**
     * @return array Returns an array of image identifiers to be deleted.
     */
    private function get_delete_id(): array{
        if (empty($_POST['del_images'])){
            return array();
        }
        return array_filter(explode(';', $_POST['del_images']), function ($val){
            return $val != '';
        });
    }

    /**
     * Returns the number of images to be uploaded.
     * @param array $concrete_post_files_input A specific array with file data. For example $_FILES['input']
     * @return int
     */
    private function get_upload_files_count(array $concrete_post_files_input): int{
        $upload_img_count = 0;
        if ($_FILES['post-edit-new-images']['error'][0] != UPLOAD_ERR_NO_FILE){
            $upload_img_count = count($concrete_post_files_input['name']);
        }
        return $upload_img_count;
    }

    /**
     * Saves new images.
     * @param int $upload_img_count Number of images to upload.
     * @return array The path to each saved image.
     * @throws ErrorUploadingFile
     * @throws ExceedMaximumFileSize
     * @throws FileTypeError
     */
    private function save_new_images(int $upload_img_count): array{
        $username = $_GET['user_g']['username'];
        $post_img_dir = implode(DIRECTORY_SEPARATOR, [config\PATH_TO_MEDIA_USERS . $username, 'post_images']);
        try {
            return save_multiple_images($_FILES['post-edit-new-images'], $post_img_dir, $upload_img_count);
        } catch (ErrorUploadingFile|ExceedMaximumFileSize|FileTypeError $e) {
            throw $e;
        }
    }

    /**
     * Completely removes images from the database and file system.
     * @param array $delete_images_id The identifiers of the files to be deleted.
     * @return void
     * @throws ErrImageNotBelongToPost
     */
    private function delete_images(array $delete_images_id): void{
        // Checks if the IDs of the images to be deleted match the IDs of the images in this post.
        $current_images = $this->post_images;
        foreach ($delete_images_id as $del_id){
            $ok = false;
            foreach ($current_images as $curr_image){
                if ($del_id == $curr_image['id']){
                    $ok = true;
                }
            }
            if (!$ok){
                throw new ErrImageNotBelongToPost();
            }
        }

        // Deletion from file system and database.
        foreach ($delete_images_id as $del_img_id){
            unlink($this->post_images_db->all_where("id=$del_img_id")[0]['image']);
            $this->post_images_db->delete($del_img_id);
        }
    }

    public function handle(): void{
        $this->post();
        $this->enable_global_func($this->twig);
        $this->render('post/post_edit.html', array(
            'post' => $this->post,
            'images' => $this->post_images,
            'error' => $this->form_error,
        ));
    }
}


class ErrImageNotBelongToPost extends Exception{
    public function __construct(string $message = "One or more images do not belong in this post.", int $code = 0,
                                ?Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}

class ErrDifferentNumberDelUplImages extends Exception{
    public function __construct(string $message = "Different number of deleted and uploaded images.", int $code = 0,
                                ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class ErrNumDelImagesToLarge extends Exception{
    public function __construct(string $message = "The number of images to be deleted is too large.", int $code = 0,
                                ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class ErrNumUplImagesToLarge extends Exception{
    public function __construct(string $message = "The number of images to be uploaded is too large.", int $code = 0,
                                ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
