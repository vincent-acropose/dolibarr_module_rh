CREATE TABLE llx_rh_med
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    date_visit DATE,
    commentaire TEXT,
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)