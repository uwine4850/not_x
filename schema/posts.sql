use not_x;

CREATE TABLE IF NOT EXISTS posts(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    text TEXT NOT NULL,
    user INT NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (user) REFERENCES not_x.users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS post_image(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    parent_post INT NOT NULL,
    image TEXT NOT NULL,
    FOREIGN KEY (parent_post) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS post_like(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    UNIQUE KEY plike (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES not_x.users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    text TEXT NOT NULL,
    parent_post_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (parent_post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES not_x.users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments_answer(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    text TEXT NOT NULL,
    user_id INT NOT NULL,
    answer_for_comment_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES not_x.users(id) ON DELETE CASCADE,
    FOREIGN KEY (answer_for_comment_id) REFERENCES comments(id) ON DELETE CASCADE
);

