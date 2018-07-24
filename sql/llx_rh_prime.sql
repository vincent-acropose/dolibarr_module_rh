CREATE TABLE llx_rh_prime
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fk_user INT NOT NULL,
    date_prime DATE,
    montant TEXT,
    FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)