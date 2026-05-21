<?php
require "connectionBD.php";
$id=isset($_GET['id'])?$_GET['id']:NULL;
if ($id) {
    $sql="DELETE FROM produit WHERE id=:id";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([
        ':id'=>$id
    ]);
}
header("Location:produit.php");
exit;
?>