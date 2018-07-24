CREATE TABLE llx_rh_historique
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    fk_element VARCHAR(255),
    value VARCHAR(255),
    date_change DATE,
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)