-- Base de données: ecommerce

-- Table: clients
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    ville VARCHAR(100),
    code_postal VARCHAR(20),
    pays VARCHAR(100) DEFAULT 'Maroc',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif'
);

-- Table: categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table: produits
CREATE TABLE produits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    quantite_stock INT DEFAULT 0,
    image VARCHAR(255),
    id_categorie INT,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table: commandes
CREATE TABLE commandes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_client INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'en_cours', 'expedie', 'livre', 'annule') DEFAULT 'en_attente',
    total DECIMAL(10, 2) NOT NULL,
    adresse_livraison TEXT,
    mode_paiement VARCHAR(50),
    FOREIGN KEY (id_client) REFERENCES clients(id) ON DELETE CASCADE
);

-- Table: details_commande
CREATE TABLE details_commande (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_commande) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produit) REFERENCES produits(id) ON DELETE CASCADE
);

-- Table: boutiques
CREATE TABLE boutiques (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(150),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('active', 'inactive') DEFAULT 'active'
);

-- Table: promotions
CREATE TABLE promotions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_produit INT,
    code_promo VARCHAR(50) UNIQUE NOT NULL,
    reduction DECIMAL(5, 2) NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_produit) REFERENCES produits(id) ON DELETE SET NULL
);

-- Table: historique_connexions
CREATE TABLE historique_connexions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_client INT,
    date_connexion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (id_client) REFERENCES clients(id) ON DELETE CASCADE
);