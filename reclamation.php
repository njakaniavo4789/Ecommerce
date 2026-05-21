<?php
require "connectionBD.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $Numero_commande = trim($_POST['CommandeNumero'] ?? '');
    $typeReclamation = trim($_POST['type_reclamation'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $photo = "";

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "upload/";
        $photo = $uploadDir . time() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    if (!empty($nom) && !empty($email) && !empty($Numero_commande) && !empty($typeReclamation) && !empty($description)) {
        $sql = "INSERT INTO reclamation (nom, email, Numero_commande, typeReclamation, description, photo)
                VALUES (:nom, :email, :Numero_commande, :typeReclamation, :description, :photo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':Numero_commande' => $Numero_commande,
            ':typeReclamation' => $typeReclamation,
            ':description' => $description,
            ':photo' => $photo
        ]);
    }
}

header("Location: boutique.php");
exit;
?>