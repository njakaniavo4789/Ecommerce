<?php
$pdo = new PDO("mysql:host=localhost;dbname=ecommerce;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    role ENUM('admin', 'manager', 'commercial') DEFAULT 'admin',
    avatar VARCHAR(255),
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME
)";
$pdo->exec($sql);
echo "Table users créée<br>";

$hash = password_hash("admin123", PASSWORD_DEFAULT);
$sql2 = "INSERT IGNORE INTO users (username, mot_de_passe, nom, email, role) 
         VALUES ('admin', :motdepasse, 'Administrateur', 'admin@ecommerce.com', 'admin')";
$stmt = $pdo->prepare($sql2);
$stmt->execute([':motdepasse' => $hash]);
echo "Admin créé (username: admin, password: admin123)";
?>