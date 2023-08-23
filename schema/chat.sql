use not_x;

CREATE TABLE IF NOT EXISTS chat_rooms(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user1 INT NOT NULL,
    user2 INT NOT NULL,
    FOREIGN KEY (user1) REFERENCES not_x.users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2) REFERENCES not_x.users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS chat_messages(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    parent_chat INT NOT NULL,
    time DATETIME NOT NULL,
    user INT NOT NULL,
    text TEXT NOT NULL,
    FOREIGN KEY (parent_chat) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user) REFERENCES not_x.users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS chat_messages_notification(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user INT NOT NULL,
    from_user INT NOT NULL,
    room_id INT NOT NULL,
    count INT NULL,
    FOREIGN KEY (user) REFERENCES not_x.users(id) ON DELETE CASCADE,
    FOREIGN KEY (from_user) REFERENCES not_x.users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES chat_rooms(id) ON DELETE CASCADE
);
