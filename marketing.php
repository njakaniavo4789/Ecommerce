<?php
require "connectionBD.php";

/* ACTIVER LES ERREURS PDO */
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ===========================
   PRODUITS
=========================== */
$stmt = $pdo->query("SELECT id, nom FROM produit");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===========================
   RÉCUPÉRER LES PROMOTIONS EXISTANTES
=========================== */
$stmtPromo = $pdo->query("
    SELECT m.*, p.nom AS produit_nom 
    FROM marketing m 
    LEFT JOIN produit p ON m.id_produit = p.id 
    ORDER BY m.date_debut DESC
");
$promotions = $stmtPromo->fetchAll(PDO::FETCH_ASSOC);

$message = "";

/* ===========================
   INSERT PROMOTION (avec redirection PRG)
=========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset(
            $_POST['nom_promotion'],
            $_POST['reduction'],
            $_POST['date_debut'],
            $_POST['date_fin'],
            $_POST['description'],
            $_POST['id_produit']
        )
    ) {

        try {
            $sql = "INSERT INTO marketing 
                (nom, date_debut, date_fin, reduction, description, id_produit)
                VALUES 
                (:nom, :date_debut, :date_fin, :reduction, :description, :id_produit)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'         => trim($_POST['nom_promotion']),
                ':date_debut'  => $_POST['date_debut'],
                ':date_fin'    => $_POST['date_fin'],
                ':reduction'   => (int) $_POST['reduction'],
                ':description' => trim($_POST['description']),
                ':id_produit'  => (int) $_POST['id_produit']
            ]);

            // Redirection PRG pour eviter la resoumission
            header("Location: marketing.php?success=1");
            exit;

        } catch (PDOException $e) {
            $message = "Erreur SQL : " . $e->getMessage();
        }

    } else {
        $message = "Tous les champs sont obligatoires";
    }
}

// Gerer le message de succes apres redirection
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Promotion ajoutee avec succes";
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin – Promotion & Marketing</title>
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
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));border:2px solid var(--border);border-radius:20px;padding:30px;margin-bottom:25px;}
.card h2{font-family:'Orbitron',monospace;font-size:1.3rem;margin-bottom:20px;color:var(--neon-cyan);}
.form{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.form label{display:block;font-weight:500;margin-bottom:8px;color:var(--muted);font-size:0.9rem;}
.form input,.form select,.form textarea{width:100%;padding:14px 18px;border-radius:14px;border:2px solid var(--border);background:rgba(15,23,42,0.6);color:white;font-size:1rem;transition:all 0.3s ease;}
.form input:focus,.form select:focus,.form textarea:focus{outline:none;border-color:var(--neon-cyan);box-shadow:0 0 20px rgba(0,255,249,0.3);}
.form textarea{grid-column:1/3;resize:none;min-height:100px;}
.form-actions{grid-column:1/3;display:flex;gap:15px;margin-top:10px;}
.btn{padding:14px 28px;border-radius:14px;border:none;cursor:pointer;font-weight:700;transition:all 0.3s ease;font-size:1rem;}
.btn-primary{background:linear-gradient(135deg,var(--neon-cyan),#6366f1);color:#0f172a;}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(0,255,249,0.4);}
.btn-danger{background:linear-gradient(135deg,var(--danger),#b91c1c);color:#fff;}
.btn-danger:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(239,68,68,0.4);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:15px;border-bottom:1px solid var(--border);}
th{color:var(--muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:2px;font-weight:600;}
.badge{display:inline-block;padding:6px 14px;border-radius:30px;font-size:0.75rem;font-weight:700;text-transform:uppercase;}
.badge.active{background:rgba(0,255,157,0.15);color:var(--success);}
.badge.expired{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.badge.pending{background:rgba(0,255,249,0.15);color:var(--neon-cyan);}
.msg{padding:15px;border-radius:14px;margin-bottom:20px;font-weight:600;}
.msg.success{background:rgba(0,255,157,0.15);color:var(--success);}
.msg.error{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
@media (max-width:768px){.form{grid-template-columns:1fr}.form textarea{grid-column:1}}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
<div class="glow-line"></div>

<div class="page-header">
  <h1 class="page-title">🎯 MARKETING & PROMOTIONS</h1>
</div>

<?php if ($message): ?>
<div class="msg <?= isset($_GET['success']) ? 'success' : 'error' ?>">
  <i class="fas fa-<?= isset($_GET['success']) ? 'check-circle' : 'exclamation-circle' ?>"></i>
  <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<form method="post">
<div class="card">
  <h2><i class="fas fa-plus-circle"></i> Nouvelle Promotion</h2>
  <div class="form">
    <div>
      <label>Nom de la promotion</label>
      <input type="text" name="nom_promotion" required placeholder="Ex: Soldes d'été">
    </div>
    <div>
      <label>Produit</label>
      <select name="id_produit" required>
        <option value="">-- Choisir un produit --</option>
        <?php foreach ($produits as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Réduction (%)</label>
      <input type="number" name="reduction" required min="1" max="100" placeholder="Ex: 20">
    </div>
    <div>
      <label>Date début</label>
      <input type="date" name="date_debut" required>
    </div>
    <div>
      <label>Date fin</label>
      <input type="date" name="date_fin" required>
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" rows="3" placeholder="Description de la promotion..." required></textarea>
    </div>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> Ajouter</button>
    </div>
  </div>
</div>
</form>

<div class="card">
  <h2><i class="fas fa-fire"></i> Promotions Actives</h2>
  <table>
    <tr>
      <th>Nom</th>
      <th>Produit</th>
      <th>Réduction</th>
      <th>Validité</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
    <?php if (empty($promotions)): ?>
    <tr>
      <td colspan="6" style="text-align:center;color:var(--muted);padding:40px;">
        <i class="fas fa-info-circle"></i> Aucune promotion pour le moment
      </td>
    </tr>
    <?php else: ?>
      <?php foreach ($promotions as $promo): 
        $today = date('Y-m-d');
        $isActive = ($promo['date_debut'] <= $today && $promo['date_fin'] >= $today);
        $isExpired = ($promo['date_fin'] < $today);
      ?>
      <tr>
        <td><?= htmlspecialchars($promo['nom']) ?></td>
        <td><?= htmlspecialchars($promo['produit_nom'] ?? 'Produit supprimé') ?></td>
        <td><strong style="color:var(--success)"><?= $promo['reduction'] ?>%</strong></td>
        <td><?= date('d/m/Y', strtotime($promo['date_debut'])) ?> - <?= date('d/m/Y', strtotime($promo['date_fin'])) ?></td>
        <td>
          <?php if ($isActive): ?>
            <span class="badge active"><i class="fas fa-check"></i> Active</span>
          <?php elseif ($isExpired): ?>
            <span class="badge expired"><i class="fas fa-times"></i> Expirée</span>
          <?php else: ?>
            <span class="badge pending"><i class="fas fa-clock"></i> À venir</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="supprimer_promo.php?id=<?= $promo['id'] ?>" 
             onclick="return confirm('Supprimer cette promotion?')" 
             style="color:var(--danger);text-decoration:none;font-weight:600;">
             <i class="fas fa-trash"></i> Supprimer
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>
</div>

</div>
</body>
</html>
