CREATE TABLE llx_rh
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    present VARCHAR(100),
    salary VARCHAR(100),
    salary_brut VARCHAR(100),
    address1 VARCHAR(200),
    address2 VARCHAR(200),
    zip VARCHAR(20),
    city VARCHAR(200),
    telFixe VARCHAR(50),
    telPortable VARCHAR(50),
    contact VARCHAR(300),
    telContact1 VARCHAR(50),
    telContact2 VARCHAR(50),
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)