<?php
require_once 'utils/handler.php';
require_once 'utils/database.php';

trait LazyLoadPost{
    use ConnectToAllTables;

    protected Database $db;
    private array $posts;
    private array $user;

    public function lazy_load_post_construct(): void{
        $this->db = new Database();
        $this->connect_to_all_tables($this->db);
    }

    public function __destruct()
    {
        $this->db->close();
    }

    public function close_db(): void{
        $this->db->close();
    }
}

/**
 * Outputs some number of posts from subscriptions in descending order of when they were created.
 * @param int $uid ID of the logged-in user.
 * @param int $post_id Post Identifier.
 * @param int $count Number of posts displayed.
 * @return array Array of posts.
 */
function get_subscriptions_posts(int $uid, int $post_id, int $count, Database $db_posts_instance): array{
    if (!$post_id){
        $post_id = '<= (SELECT MAX(id) FROM posts)';
    } else{
        $post_id = "< $post_id";
    }
    $res = $db_posts_instance->query("SELECT p.* FROM posts p
                                        INNER JOIN subscriptions s ON p.user = s.profile_id
                                        WHERE s.subscriber_id = $uid AND p.id $post_id
                                        ORDER BY p.id DESC LIMIT $count");
    return $res->fetch_all(MYSQLI_ASSOC);
}