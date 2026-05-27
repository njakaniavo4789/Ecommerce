<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin – Commandes</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#030712;
  --neon-cyan:#00fff9;
  --neon-pink:#ff006e;
  --neon-purple:#c800ff;
  --neon-orange:#ff6b00;
  --success:#00ff9d;
  --danger:#ff2b6e;
  --card:rgba(13,0,26,0.9);
  --text:#f0f0ff;
  --muted:#9ca3af;
  --border:rgba(0,255,249,0.15);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif;margin:0;padding:0;}
body{background:var(--bg);color:var(--text);min-height:100vh;position:relative;padding-top:90px;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,rgba(0,255,249,0.03) 1px,transparent 1px),linear-gradient(rgba(0,255,249,0.03) 1px,transparent 1px);background-size:50px 50px;pointer-events:none;z-index:0;}
.container{position:relative;z-index:1;max-width:1200px;margin:auto;padding:30px;}
.page-header{text-align:center;margin-bottom:40px;padding:30px 0;}
.page-title{font-family:'Orbitron',monospace;font-size:clamp(2rem,5vw,3rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));-webkit-background-clip:text;background-clip:text;color:transparent;}
.glow-line{height:4px;background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));border-radius:2px;margin-bottom:30px;animation:glow 2s linear infinite;background-size:200%;}
@keyframes glow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));border:2px solid var(--border);border-radius:20px;padding:30px;margin-bottom:25px;overflow-x:auto;}
.card h2{font-family:'Orbitron',monospace;font-size:1.3rem;margin-bottom:20px;color:var(--neon-cyan);}
table{width:100%;border-collapse:collapse;margin-top:15px;min-width:750px;}
th,td{padding:15px;text-align:left;border-bottom:1px solid var(--border);}
th{color:var(--muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:2px;font-weight:600;}
.badge{display:inline-block;padding:6px 14px;border-radius:30px;font-size:0.75rem;font-weight:700;text-transform:uppercase;}
.badge.en_attente{background:rgba(0,255,249,0.15);color:var(--neon-cyan);}
.badge.en_cours{background:rgba(255,107,0,0.15);color:var(--neon-orange);}
.badge.expedie{background:rgba(200,0,255,0.15);color:var(--neon-purple);}
.badge.livre{background:rgba(0,255,157,0.15);color:var(--success);}
.badge.annule{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.btn-s{padding:8px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:600;font-size:.8rem;transition:all .3s;}
.btn-cyan{background:linear-gradient(135deg,var(--neon-cyan),#009999);color:#000;margin-right:5px;}
.btn-cyan:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,255,249,.3);}
.btn-pink{background:linear-gradient(135deg,var(--neon-pink),#990066);color:#fff;}
.btn-pink:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(255,0,110,.3);}
.order-id{font-family:'Orbitron',monospace;color:var(--neon-cyan);font-weight:700;font-size:.85rem;}
.order-amount{font-weight:700;color:var(--neon-pink);}
.status-form{display:inline-block;}
.status-form select{padding:6px 10px;border-radius:8px;background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--border);font-size:.8rem;}
.empty-state{text-align:center;padding:50px 0;color:var(--muted);}
.empty-state .ico{font-size:3rem;margin-bottom:15px;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<?php require 'connectionBD.php'; ?>

<div class="container">
<div class="glow-line"></div>

<div class="page-header">
  <h1 class="page-title"><i class="fas fa-truck"></i> GESTION DES COMMANDES</h1>
</div>

<?php
// Delete order
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM commandes WHERE id = ?")->execute([$id]);
    echo '<script>window.location="commande.php";</script>';
    exit();
}

// Update status
if (isset($_GET['update']) && isset($_GET['statut'])) {
    $id = (int)$_GET['update'];
    $statut = $_GET['statut'];
    $allowed = ['en_attente','en_cours','expedie','livre','annule'];
    if (in_array($statut, $allowed)) {
        $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?")->execute([$statut, $id]);
        echo '<script>window.location="commande.php";</script>';
        exit();
    }
}

$orders = $pdo->query("SELECT * FROM commandes ORDER BY date_commande DESC")->fetchAll();
$stats = $pdo->query("SELECT statut, COUNT(*) as cnt FROM commandes GROUP BY statut")->fetchAll(PDO::FETCH_KEY_PAIR);
$total_cmd = count($orders);
$total_rev = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes")->fetchColumn();
?>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;">
  <div class="card" style="text-align:center;">
    <div style="font-size:2rem;color:var(--neon-cyan);"><?= $total_cmd ?></div>
    <div style="color:var(--muted);font-size:.85rem;text-transform:uppercase;">Total Commandes</div>
  </div>
  <div class="card" style="text-align:center;">
    <div style="font-size:2rem;color:var(--neon-pink);"><?= number_format($total_rev, 0, ',', ' ') ?> Ar</div>
    <div style="color:var(--muted);font-size:.85rem;text-transform:uppercase;">Revenu Total</div>
  </div>
  <div class="card" style="text-align:center;">
    <div style="font-size:2rem;color:var(--neon-purple);"><?= $stats['en_attente'] ?? 0 ?></div>
    <div style="color:var(--muted);font-size:.85rem;text-transform:uppercase;">En attente</div>
  </div>
  <div class="card" style="text-align:center;">
    <div style="font-size:2rem;color:var(--success);"><?= $stats['livre'] ?? 0 ?></div>
    <div style="color:var(--muted);font-size:.85rem;text-transform:uppercase;">Livrées</div>
  </div>
</div>

<div class="card">
  <h2><i class="fas fa-list"></i> Liste des commandes</h2>

  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <div class="ico">📦</div>
      <p>Aucune commande pour le moment.</p>
    </div>
  <?php else: ?>
  <table>
    <tr>
      <th>N°</th>
      <th>Client</th>
      <th>Email</th>
      <th>Date</th>
      <th>Montant</th>
      <th>Paiement</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($orders as $o): ?>
    <tr>
      <td><span class="order-id">#CMD-<?= $o['id'] ?></span></td>
      <td><?= htmlspecialchars(($o['prenom_client'] ?? '') . ' ' . ($o['nom_client'] ?? 'Client')) ?></td>
      <td style="font-size:.85rem;color:var(--muted);"><?= htmlspecialchars($o['email_client'] ?? '-') ?></td>
      <td style="font-size:.85rem;color:var(--muted);"><?= date('d/m/Y H:i', strtotime($o['date_commande'])) ?></td>
      <td><span class="order-amount"><?= number_format($o['total'], 0, ',', ' ') ?> Ar</span></td>
      <td style="font-size:.85rem;"><?= htmlspecialchars($o['mode_paiement'] ?? '-') ?></td>
      <td>
        <form method="GET" class="status-form">
          <input type="hidden" name="update" value="<?= $o['id'] ?>">
          <select name="statut" onchange="this.form.submit()">
            <?php foreach (['en_attente','en_cours','expedie','livre','annule'] as $s):
              $labels = ['en_attente'=>'En attente','en_cours'=>'En cours','expedie'=>'Expédié','livre'=>'Livré','annule'=>'Annulé']; ?>
              <option value="<?= $s ?>" <?= $o['statut']===$s?'selected':'' ?>><?= $labels[$s] ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td>
        <a href="commande_details.php?id=<?= $o['id'] ?>" class="btn-s btn-cyan"><i class="fas fa-eye"></i> Détails</a>
        <a href="commande.php?delete=<?= $o['id'] ?>" class="btn-s btn-pink" onclick="return confirm('Supprimer cette commande ?')"><i class="fas fa-trash"></i></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>

</div>
</body>
</html>