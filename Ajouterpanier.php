<?php
session_start();
require 'connectionBD.php';

header('Content-Type: application/json');

// AJAX: Supprimer un article
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
    $ajax = isset($_GET['ajax']);
    if ($ajax) {
        echo json_encode(['ok'=>true, 'action'=>'removed', 'id'=>$id, 'count'=>count($_SESSION['panier'])]);
        exit();
    }
    header("Location: boutique.php#cart");
    exit();
}

// AJAX: Vider le panier
if (isset($_GET['clear'])) {
    $_SESSION['panier'] = [];
    $ajax = isset($_GET['ajax']);
    if ($ajax) {
        echo json_encode(['ok'=>true, 'action'=>'cleared']);
        exit();
    }
    header("Location: boutique.php");
    exit();
}

// AJAX: Ajouter un produit
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sql = "SELECT * FROM produit WHERE id = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$id]);
    $produit = $query->fetch(PDO::FETCH_ASSOC);

    $ajax = isset($_GET['ajax']);

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
        if ($ajax) {
            $item = $_SESSION['panier'][$id];
            echo json_encode(['ok'=>true, 'action'=>'added', 'item'=>['id'=>(int)$item['id'], 'nom'=>$item['nom'], 'prix'=>(float)$item['prix'], 'image'=>$item['image'], 'qty'=>(int)$item['quantite'], 'count'=>count($_SESSION['panier']), 'total'=>array_sum(array_map(function($it){return $it['prix']*$it['quantite'];},$_SESSION['panier']))]]);
            exit();
        }
    }
    if ($ajax) {
        echo json_encode(['ok'=>false, 'error'=>'Produit introuvable']);
        exit();
    }
    header("Location: boutique.php#cart");
    exit();
}

if (isset($_GET['count'])) {
    echo json_encode(['count'=>count($_SESSION['panier']??[])]);
    exit();
}
?>