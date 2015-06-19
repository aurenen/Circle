CREATE TABLE control (
    uid INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(256) NOT NULL
);
--
CREATE TABLE users (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(256) NOT NULL,
    UNIQUE (email)
);
--
CREATE TABLE list (
    id INTEGER,
    user INTEGER,
    url VARCHAR(75),
    title VARCHAR(70),
    banner VARCHAR(120),
    description VARCHAR(400),
    approved TINYINT,
    UNIQUE (id)
);