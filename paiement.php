<?php
session_start();
require 'connectionBD.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['panier'])) {
    header("Location: boutique.php");
    exit();
}

$nom       = trim($_POST['nom'] ?? '');
$prenom    = trim($_POST['prenom'] ?? '');
$email     = trim($_POST['email'] ?? '');
$adresse   = trim($_POST['adresse'] ?? '');
$mode_paiement = trim($_POST['mode_paiement'] ?? '');

if (!$nom || !$prenom || !$email || !$adresse || !$mode_paiement) {
    header("Location: boutique.php?error=missing_fields#payment");
    exit();
}

try {
    $pdo->beginTransaction();

    // Total
    $total = 0;
    foreach ($_SESSION['panier'] as $item) {
        $total += $item['prix'] * $item['quantite'];
    }

    // Insérer commande
    $stmt = $pdo->prepare("INSERT INTO commandes (id_client, nom_client, prenom_client, email_client, total, adresse_livraison, mode_paiement) VALUES (NULL, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $total, $adresse, $mode_paiement]);
    $id_commande = $pdo->lastInsertId();

    // Insérer détails
    $stmt = $pdo->prepare("INSERT INTO details_commande (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['panier'] as $item) {
        $stmt->execute([$id_commande, $item['id'], $item['quantite'], $item['prix']]);
    }

    $pdo->commit();

    // Mettre à jour les stats
    $date = date('Y-m-d');
    $pdo->prepare("INSERT INTO stats_ventes (date, total_ventes, nombre_commandes) VALUES (?,?,?) ON DUPLICATE KEY UPDATE total_ventes=total_ventes+?, nombre_commandes=nombre_commandes+1")->execute([$date, $total, 1, $total]);
    foreach ($_SESSION['panier'] as $item) {
        $pdo->prepare("INSERT INTO stats_produits (produit_id, produit_nom, total_vendu, date) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE total_vendu=total_vendu+?")->execute([$item['id'], $item['nom'], $item['quantite'], $date, $item['quantite']]);
    }
    $pdo->prepare("INSERT INTO stats_paiements (mode_paiement, total, nombre, date) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE total=total+?, nombre=nombre+1")->execute([$mode_paiement, $total, 1, $date, $total]);

    $_SESSION['panier'] = [];
    header("Location: boutique.php?success=1#payOk");
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: boutique.php?error=db#payment");
}
exit();
?>