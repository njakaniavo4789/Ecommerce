<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Détails commande</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#030712;--neon-cyan:#00fff9;--neon-pink:#ff006e;--neon-purple:#c800ff;
  --success:#00ff9d;--danger:#ff2b6e;--card:rgba(13,0,26,0.9);--text:#f0f0ff;
  --muted:#9ca3af;--border:rgba(0,255,249,0.15);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);min-height:100vh;padding-top:90px;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,rgba(0,255,249,0.03)1px,transparent 1px),linear-gradient(rgba(0,255,249,0.03)1px,transparent 1px);background-size:50px 50px;pointer-events:none;z-index:0;}
.container{position:relative;z-index:1;max-width:900px;margin:auto;padding:30px;}
.page-title{font-family:'Orbitron',monospace;font-size:clamp(1.5rem,4vw,2.2rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink));-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:30px;}
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));border:2px solid var(--border);border-radius:20px;padding:30px;margin-bottom:25px;}
.card h2{font-family:'Orbitron',monospace;font-size:1.1rem;margin-bottom:20px;color:var(--neon-cyan);}
.grp{display:grid;grid-template-columns:1fr 1fr;gap:15px;}
.grp .lb{color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:1px;}
.grp .vl{font-weight:600;}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:12px 15px;text-align:left;border-bottom:1px solid var(--border);}
th{color:var(--muted);font-size:.75rem;text-transform:uppercase;letter-spacing:2px;}
.badge{display:inline-block;padding:4px 12px;border-radius:30px;font-size:.7rem;font-weight:700;text-transform:uppercase;}
.badge.en_attente{background:rgba(0,255,249,0.15);color:var(--neon-cyan);}
.badge.en_cours{background:rgba(255,107,0,0.15);color:#ff6b00;}
.badge.expedie{background:rgba(200,0,255,0.15);color:var(--neon-purple);}
.badge.livre{background:rgba(0,255,157,0.15);color:var(--success);}
.badge.annule{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.back{display:inline-block;margin-bottom:20px;color:var(--neon-cyan);text-decoration:none;font-weight:600;}
.back:hover{text-shadow:0 0 12px var(--neon-cyan);}
.total-row{font-size:1.1rem;font-weight:700;color:var(--neon-pink);text-align:right;padding-top:15px;}
.thu{width:60px;height:60px;object-fit:cover;border-radius:10px;border:1px solid var(--border);}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<?php require 'connectionBD.php';

$id = (int)($_GET['id'] ?? 0);
$order = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$order->execute([$id]);
$o = $order->fetch(PDO::FETCH_ASSOC);
if (!$o) { echo '<div class="container"><p>Commande introuvable.</p></div>'; exit(); }

$details = $pdo->prepare("SELECT d.*, p.nom as produit_nom, p.image as produit_image FROM details_commande d LEFT JOIN produit p ON d.id_produit = p.id WHERE d.id_commande = ?");
$details->execute([$id]);
$items = $details->fetchAll();

$labels = ['en_attente'=>'En attente','en_cours'=>'En cours','expedie'=>'Expédié','livre'=>'Livré','annule'=>'Annulé'];
$badge_class = ['en_attente'=>'en_attente','en_cours'=>'en_cours','expedie'=>'expedie','livre'=>'livre','annule'=>'annule'];
?>
<div class="container">
<a href="commande.php" class="back"><i class="fas fa-arrow-left"></i> Retour aux commandes</a>
<h1 class="page-title"><i class="fas fa-receipt"></i> Commande #CMD-<?= $o['id'] ?></h1>

<div class="card">
  <h2><i class="fas fa-info-circle"></i> Informations</h2>
  <div class="grp">
    <div><div class="lb">Client</div><div class="vl"><?= htmlspecialchars(($o['prenom_client']??'') . ' ' . ($o['nom_client']??'') ?: '-') ?></div></div>
    <div><div class="lb">Email</div><div class="vl"><?= htmlspecialchars($o['email_client'] ?? '-') ?></div></div>
    <div><div class="lb">Date</div><div class="vl"><?= date('d/m/Y H:i', strtotime($o['date_commande'])) ?></div></div>
    <div><div class="lb">Statut</div><div class="vl"><span class="badge <?= $badge_class[$o['statut']] ?? 'en_attente' ?>"><?= $labels[$o['statut']] ?? $o['statut'] ?></span></div></div>
    <div style="grid-column:1/-1;"><div class="lb">Adresse livraison</div><div class="vl"><?= htmlspecialchars($o['adresse_livraison'] ?? '-') ?></div></div>
    <div style="grid-column:1/-1;"><div class="lb">Mode de paiement</div><div class="vl"><?= htmlspecialchars($o['mode_paiement'] ?? '-') ?></div></div>
  </div>
</div>

<div class="card">
  <h2><i class="fas fa-box"></i> Articles</h2>
  <table>
    <tr><th>Produit</th><th>Prix u.</th><th>Qté</th><th>Total</th></tr>
    <?php $sub=0; foreach($items as $it):
      $line = $it['prix_unitaire'] * $it['quantite'];
      $sub += $line; ?>
    <tr>
      <td style="display:flex;align-items:center;gap:12px;">
        <img src="<?= htmlspecialchars($it['produit_image']??'images/placeholder.jpg') ?>" class="thu" onerror="this.src='images/placeholder.jpg'">
        <?= htmlspecialchars($it['produit_nom']??'Produit #'.$it['id_produit']) ?>
      </td>
      <td><?= number_format($it['prix_unitaire'],0,',',' ') ?> Ar</td>
      <td><?= $it['quantite'] ?></td>
      <td style="font-weight:600;"><?= number_format($line,0,',',' ') ?> Ar</td>
    </tr>
    <?php endforeach; ?>
    <tr><td colspan="3" class="total-row">Total</td><td class="total-row"><?= number_format($o['total'],0,',',' ') ?> Ar</td></tr>
  </table>
</div>
</div>
</body>
</html>