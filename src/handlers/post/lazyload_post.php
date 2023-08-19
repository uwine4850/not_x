<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';
require_once 'handlers/post/post_utils.php';
require_once 'handlers/profile/profile_utils.php';

trait LazyLoadPost{
    private Database $posts_db;
    private array $posts;
    private array $user;
    private Database $post_images_db;

    public function lazy_load_post_construct(): void{
        $this->posts_db = new Database('posts');
        $this->post_images_db = new Database('post_image');
    }

    public function get_post_image(int $post_id): array{
        return $this->post_images_db->all_where("parent_post=$post_id");
    }

    /**
     * Outputs some number of posts from subscriptions in descending order of when they were created.
     * @param int $uid ID of the logged-in user.
     * @param int $post_id Post Identifier.
     * @param int $count Number of posts displayed.
     * @return array Array of posts.
     */
    private function get_subscriptions_posts(int $uid, int $post_id, int $count): array{
        if (!$post_id){
            $post_id = '<= (SELECT MAX(id) FROM posts)';
        } else{
            $post_id = "< $post_id";
        }
        $res = $this->posts_db->query("SELECT p.* FROM posts p
                                        INNER JOIN subscriptions s ON p.user = s.profile_id
                                        WHERE s.subscriber_id = $uid AND p.id $post_id
                                        ORDER BY p.id DESC LIMIT $count");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}