<?php

/**
 * Checks if a user has liked a post.
 * @param int $user_id
 * @param int $post_id
 * @return bool
 */
function is_liked (int $user_id, int $post_id): bool{
    $post_like_db = new Database('post_like');
    if (empty($post_like_db->all_where("user_id=$user_id AND post_id=$post_id"))){
        return false;
    } else{
        return true;
    }
}

/**
 * Returns the number of likes of the post by id.
 * @param int $post_id
 * @return int
 */
function post_like_count(int $post_id): int{
    $post_like_db = new Database('post_like');
    return $post_like_db->count("post_id=$post_id")[0];
}

/**
 * Returns an array with the post's image data.
 * @param $post_id
 * @return array
 */
function get_post_image($post_id): array{
    $post_images_db = new Database('post_image');
    return $post_images_db->all_where("parent_post=$post_id");
}

/**
 * Returns answers to the selected comment.
 * @param int $comment_id
 * @return array
 */
function get_answer_comments(int $comment_id): array{
    $db = new Database('comments');
    $res = $db->all_fk('comments_answer', 'answer_for_comment_id', where: "comments.id=$comment_id");
    if (empty($res)){
        return array();
    }
    return $res;
}

/**
 * Outputs the number of comments on the post. Answers to comments are not counted.
 * @param int $post_id
 * @return int
 */
function get_count_of_comment_by_post_id(int $post_id): int{
    $db = new Database('comments');
    $res = $db->query("SELECT COUNT(*) AS total_comments_and_answers
                        FROM (
                            SELECT id FROM comments WHERE parent_post_id = $post_id
                            UNION ALL
                            SELECT answer_for_comment_id FROM comments_answer WHERE answer_for_comment_id IN (SELECT id FROM comments WHERE parent_post_id = $post_id)
                        ) AS all_comments;");
    return $res->fetch_all()[0][0];
}

/**
 * Loading posts for a user starting with a specific post ID.
 * @param int $uid User ID.
 * @param int $start_post_id Id of the post from which the output starts.
 * @param int $count Number of posts.
 * @return array
 */
function load_user_posts(int $uid, int $start_post_id, int $count): array{
    $db = new Database('posts');
    return $db->all_where("id < $start_post_id AND user=$uid ORDER BY posts.id DESC", $count);
}

function delete_post_by_id (int $post_id): void{
    $db = new Database('posts');
    $db->delete($post_id);
}
