<?php
session_start();
require 'connectionBD.php';

// Supprimer un article
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
    header("Location: boutique.php#cart");
    exit();
}

// Vider le panier
if (isset($_GET['clear'])) {
    $_SESSION['panier'] = [];
    header("Location: boutique.php");
    exit();
}

// Ajouter un produit
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sql = "SELECT * FROM produit WHERE id = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$id]);
    $produit = $query->fetch(PDO::FETCH_ASSOC);

    if ($produit) {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        if (isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]['quantite']++;
        } else {
            $_SESSION['panier'][$id] = [
                "id" => $produit['id'],
                "nom" => $produit['nom'],
                "prix" => $produit['prix'],
                "image" => $produit['image'],
                "quantite" => 1
            ];
        }
    }
    header("Location: boutique.php#cart");
    exit();
}

header("Location: boutique.php");
exit();
?>