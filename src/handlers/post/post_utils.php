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
