CREATE TABLE llx_rh_hab
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    numero VARCHAR(200),
    date_hab DATE,
    date_fin DATE,
    label VARCHAR(200),
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)