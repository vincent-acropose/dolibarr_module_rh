CREATE TABLE llx_rh
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    salary VARCHAR(100),
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)