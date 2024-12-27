CREATE TABLE comments (
    comment_id INT NOT NULL AUTO_INCREMENT,  -- 댓글 고유 ID
    board_num INT NOT NULL,                  -- 게시글 ID (memberboard 테이블 참조)
    member_id INT NOT NULL,                  -- 작성자 ID (members 테이블 참조)
    content VARCHAR(255) NOT NULL,           -- 댓글 내용 (최대 255자)
    regist_day DATETIME DEFAULT CURRENT_TIMESTAMP, -- 댓글 작성 시간
    PRIMARY KEY (comment_id),
    FOREIGN KEY (board_num) REFERENCES memberboard(num) ON DELETE CASCADE, -- 게시글 삭제 시 댓글 삭제
    FOREIGN KEY (member_id) REFERENCES members(num) ON DELETE CASCADE,    -- 작성자 삭제 시 댓글 삭제
    KEY idx_board_num (board_num)            -- 게시글 번호 인덱스
);
