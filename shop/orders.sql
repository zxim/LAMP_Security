CREATE TABLE orders (
    order_id INT NOT NULL AUTO_INCREMENT,      -- 주문 고유 ID
    member_id INT NOT NULL,                    -- 구매자 (회원) ID
    product_id INT NOT NULL,                   -- 상품 ID
    quantity INT NOT NULL DEFAULT 1,           -- 구매 수량
    total_price INT NOT NULL,                  -- 총 결제 금액 (포인트)
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP, -- 주문 날짜
    PRIMARY KEY (order_id),
    FOREIGN KEY (member_id) REFERENCES members(num), -- members 테이블 참조
    FOREIGN KEY (product_id) REFERENCES products(product_id) -- products 테이블 참조
);
