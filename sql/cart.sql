CREATE TABLE cart (
    cart_id INT NOT NULL AUTO_INCREMENT,      -- 장바구니 항목 고유 ID
    member_id INT NOT NULL,                   -- 사용자 (회원) ID
    product_id INT NOT NULL,                  -- 상품 ID
    quantity INT NOT NULL DEFAULT 1,          -- 상품 수량
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP, -- 장바구니에 담은 날짜
    PRIMARY KEY (cart_id),
    FOREIGN KEY (member_id) REFERENCES members(num), -- members 테이블 참조
    FOREIGN KEY (product_id) REFERENCES products(product_id) -- products 테이블 참조
);
