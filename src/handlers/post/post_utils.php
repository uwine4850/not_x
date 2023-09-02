<?php

/**
 * Checks if a user has liked a post.
 * @param int $user_id
 * @param int $post_id
 * @param Database $db_post_like_instance An instance of the post_like table connection database.
 * @return bool
 */
function is_liked(int $user_id, int $post_id, Database $db_post_like_instance): bool{
    if (empty($db_post_like_instance->all_where("user_id=$user_id AND post_id=$post_id"))){
        return false;
    } else{
        return true;
    }
}

/**
 * Returns the number of likes of the post by id.
 * @param int $post_id
 * @param Database $db_post_like_instance An instance of the post_like table connection database.
 * @return int
 */
function post_like_count(int $post_id, Database $db_post_like_instance): int{
    return $db_post_like_instance->count("post_id=$post_id")[0];
}

/**
 * Returns an array with the post's image data.
 * @param int $post_id
 * @param Database $post_image_instance An instance of the post_image table connection database.
 * @return array
 */
function get_post_image(int $post_id, Database $post_image_instance): array{
//    $post_images_db = new Database('post_image');
    $post_images_db = $post_image_instance;
    return $post_images_db->all_where("parent_post=$post_id");
}

/**
 * Returns answers to the selected comment.
 * @param int $comment_id
 * @param Database $db_comments_instance An instance of the comments table connection database.
 * @return array
 */
function get_answer_comments(int $comment_id, Database $db_comments_instance): array{
    $res = $db_comments_instance->all_fk('comments_answer', 'answer_for_comment_id', where: "comments.id=$comment_id");
    if (empty($res)){
        return array();
    }
    return $res;
}

/**
 * Outputs the number of comments on the post. Answers to comments are not counted.
 * @param int $post_id
 * @param Database $db_comments_instance An instance of the comments table connection database.
 * @return int
 */
function get_count_of_comment_by_post_id(int $post_id, Database $db_comments_instance): int{
    $res = $db_comments_instance->query("SELECT COUNT(*) AS total_comments_and_answers
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
 * @param Database $db_posts_instance An instance of the posts table connection database.
 * @return array
 */
function load_user_posts(int $uid, int $start_post_id, int $count, Database $db_posts_instance): array{
    return $db_posts_instance->all_where("id < $start_post_id AND user=$uid ORDER BY posts.id DESC", $count);
}

function delete_post_by_id (int $post_id): void{
    $db = new Database('posts');
    $db->delete($post_id);
}
