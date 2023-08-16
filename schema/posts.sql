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
