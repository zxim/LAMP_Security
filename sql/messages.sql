CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT,       -- 메시지 고유 ID
    sender_id CHAR(20) NOT NULL,          -- 보낸 사람의 ID (members.id 참조)
    receiver_id CHAR(20) NOT NULL,        -- 받는 사람의 ID (members.id 참조)
    subject VARCHAR(100) DEFAULT NULL,    -- 쪽지 제목
    content TEXT NOT NULL,                -- 쪽지 내용
    is_read TINYINT(1) DEFAULT 0,         -- 읽음 여부 (0: 안 읽음, 1: 읽음)
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- 보낸 시간
    read_at DATETIME DEFAULT NULL,        -- 읽은 시간
    PRIMARY KEY (id),
    FOREIGN KEY (sender_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES members(id) ON DELETE CASCADE
);
