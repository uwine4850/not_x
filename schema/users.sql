use not_x;

CREATE TABLE IF NOT EXISTS users(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    username varchar(100) NOT NULL,
    password varchar(100) NOT NULL,
    description text NULL,
    path_to_user_image text NULL
);