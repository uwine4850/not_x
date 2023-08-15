use not_x;

CREATE TABLE IF NOT EXISTS users(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    username varchar(100) NOT NULL,
    password varchar(100) NOT NULL,
    description text NULL,
    path_to_user_image text NULL
);

CREATE TABLE IF NOT EXISTS subscriptions(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    subscriber_id INT NOT NULL,
    profile_id INT NOT NULL,
    UNIQUE KEY subscription (subscriber_id, profile_id),
    FOREIGN KEY (subscriber_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profile_id) REFERENCES users(id) ON DELETE CASCADE
);
