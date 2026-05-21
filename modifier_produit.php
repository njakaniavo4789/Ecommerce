<?php
require "connectionBD.php";

$message = "";
$produit = null;

// 1. Récupérer le produit à modifier (quand on arrive via le lien "Modifier")
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        $message = "Produit introuvable.";
    }
}

// 2. Traitement du formulaire quand on clique sur "Enregistrer"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id <= 0) {
        $message = "ID du produit invalide.";
    } else {
        // Récupérer l'ancienne image
        $stmt = $pdo->prepare("SELECT image FROM produit WHERE id = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagePath = $old ? $old['image'] : '';

        // Nouvelle image uploadée ?
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
            // Supprimer l'ancienne image si elle existe
            if ($imagePath && file_exists($imagePath)) {
                @unlink($imagePath);
            }

            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $imagePath = "upload/" . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $message = "Échec lors de l'upload de la nouvelle image.";
            }
        }

        // Mise à jour si pas d'erreur d'upload
        if (empty($message)) {
            $sql = "UPDATE produit SET
                    nom         = :nom,
                    description = :description,
                    prix        = :prix,
                    stock       = :stock,
                    image       = :image
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':id'          => $id,
                ':nom'         => isset($_POST['nom']) ? trim($_POST['nom']) : '',
                ':description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                ':prix'        => isset($_POST['prix']) ? (float)$_POST['prix'] : 0,
                ':stock'       => isset($_POST['stock']) ? (int)$_POST['stock'] : 0,
                ':image'       => $imagePath
            ));

            header("Location: produit.php?success=modifie");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit</title>
    <style>
        body {
            background: #0f172a;
            color: #e5e7eb;
            font-family: 'Segoe UI', sans-serif;
            padding: 30px 20px;
            margin: 0;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background: #020617;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h1 {
            color: #38bdf8;
            text-align: center;
            margin-bottom: 25px;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
            background: #1e293b;
            color: white;
            font-size: 16px;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 14px;
            background: #38bdf8;
            border: none;
            border-radius: 8px;
            color: #020617;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background: #0ea5e9;
        }
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            color: white;
        }
        .error   { background: #dc2626; }
        img.preview {
            max-width: 180px;
            border-radius: 8px;
            margin: 10px auto;
            display: block;
        }
        .center {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: #94a3b8;
        }
        a:hover { color: #38bdf8; }
    </style>
</head>
<body>

<div class="container">

    <h1>Modifier le produit</h1>

    <?php if (!empty($message)): ?>
        <div class="message error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!$produit): ?>
        <p style="text-align:center">Produit non trouvé ou ID invalide.</p>
        <p class="center"><a href="produit.php">← Retour à la liste</a></p>
    <?php else: ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">

            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" placeholder="Nom du produit" required>
            
            <textarea name="description" placeholder="Description" required><?= htmlspecialchars($produit['description']) ?></textarea>
            
            <input type="number" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" step="0.01" placeholder="Prix" required>
            
            <input type="number" name="stock" value="<?= htmlspecialchars($produit['stock']) ?>" placeholder="Stock" required>

            <p>Image actuelle :</p>
            <img src="<?= htmlspecialchars($produit['image']) ?>" alt="Image actuelle" class="preview">

            <input type="file" name="image" accept="image/*">
            <p style="font-size:0.9em; color:#94a3b8; margin-top:5px;">
                (laisser vide pour conserver l'image actuelle)
            </p>

            <button type="submit">Enregistrer les modifications</button>
        </form>

        <p class="center">
            <a href="produit.php">Annuler et retourner à la liste</a>
        </p>

    <?php endif; ?>

</div>

</body>
</html>