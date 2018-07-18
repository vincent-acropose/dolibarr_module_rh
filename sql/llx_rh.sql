CREATE TABLE llx_rh
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    salary VARCHAR(100),
    address VARCHAR(200),
    zip VARCHAR(20),
    city VARCHAR(200),
    contact VARCHAR(300),
    telContact1 VARCHAR(50),
    telContact2 VARCHAR(50),
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)