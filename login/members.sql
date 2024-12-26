CREATE TABLE members (
    num INT NOT NULL AUTO_INCREMENT,
    id CHAR(20) NOT NULL,
    pass CHAR(255) NOT NULL,
    name CHAR(20) NOT NULL,
    email CHAR(80),
    regist_day CHAR(20),
    admin TINYINT(1) DEFAULT 0, 
    points INT DEFAULT 0,
    PRIMARY KEY (num)
);
