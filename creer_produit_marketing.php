<?php
$pdo = new PDO("mysql:host=localhost;dbname=ecommerce;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql1 = "CREATE TABLE IF NOT EXISTS produit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    quantite_stock INT DEFAULT 0,
    image VARCHAR(255),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($sql1);
echo "Table produit créee<br>";

$sql2 = "CREATE TABLE IF NOT EXISTS marketing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    reduction INT NOT NULL,
    description TEXT,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    id_produit INT,
    FOREIGN KEY (id_produit) REFERENCES produit(id) ON DELETE SET NULL
)";
$pdo->exec($sql2);
echo "Table marketing créee<br>";

echo "Terminé!";
?>