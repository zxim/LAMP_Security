CREATE TABLE products (
    product_id INT NOT NULL AUTO_INCREMENT,
    name CHAR(50) NOT NULL,
    price INT NOT NULL,  
    category VARCHAR(50) NOT NULL,
    PRIMARY KEY (product_id)
);
