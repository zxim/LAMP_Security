CREATE TABLE login_attempts (
    ip_address VARCHAR(45) NOT NULL,        -- ipv4, ipv6 주소
    attempt_count INT NOT NULL,\            -- 실패 횟수
    blocked_until DATETIME DEFAULT NULL,    -- 차단 해제 시간
    last_attempt DATETIME NOT NULL,         -- 마지막 시도 시간
    PRIMARY KEY (ip_address)                -- IP 주소를 기준으로 고유 식별
);