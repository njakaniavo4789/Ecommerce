<?php
  require_once "connectionBD.php";
  $sql="SELECT * FROM reclamation";
  $query=$pdo->prepare($sql);
  $query->execute();
  $reclamations=$query->fetchAll();
  $stat=[
    "total"=>count($reclamations),
    "defecteuses"=>0,
    "Livraison_retard"=>0,
    "manquant"=>0,
    "Mauvaise_taille"=>0,
    "autre"=>0
  ];
  foreach ($reclamations as $reclam) {

    switch($reclam['typeReclamation']){

        case 'Produit défectueux':
            $stat['defecteuses']++;
            break;

        case 'Retard de livraison':
            $stat['Livraison_retard']++;
            break;

        case 'Produit manquant':
            $stat['manquant']++;
            break;

        case 'Mauvaise taille / couleur':
            $stat['Mauvaise_taille']++;
            break;

        case 'Autre':
            $stat['autre']++;
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réclamations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f8f7f4;
  --surface:#ffffff;
  --surface-2:#f1efe8;
  --border:#e0ded5;
  --border-hover:#c8c5bb;
  --text:#1a1a18;
  --muted:#6b6b66;
  --hint:#9b9b95;
  --accent-blue:#185fa5;
  --accent-blue-bg:#e6f1fb;
  --accent-red:#a32d2d;
  --accent-red-bg:#fcebeb;
  --accent-amber:#854f0b;
  --accent-amber-bg:#faeeda;
  --accent-green:#3b6d11;
  --accent-green-bg:#eaf3de;
  --accent-info-border:#b5d4f4;
  --radius-md:8px;
  --radius-lg:12px;
}
@media(prefers-color-scheme:dark){
  :root{
    --bg:#1a1a18;
    --surface:#242422;
    --surface-2:#2c2c2a;
    --border:#3a3a37;
    --border-hover:#555550;
    --text:#f0f0e8;
    --muted:#9b9b95;
    --hint:#6b6b66;
    --accent-blue:#85b7eb;
    --accent-blue-bg:#0c447c;
    --accent-red:#f09595;
    --accent-red-bg:#791f1f;
    --accent-amber:#fac775;
    --accent-amber-bg:#633806;
    --accent-green:#97c459;
    --accent-green-bg:#27500a;
    --accent-info-border:#185fa5;
  }
}
*{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
body{background:var(--bg);color:var(--text);min-height:100vh;padding-top:90px;}
.container{max-width:1100px;margin:auto;padding:2rem 1.5rem;}

/* Top bar */
.top-bar{height:3px;background:linear-gradient(90deg,#185fa5,#D4537E,#534AB7);border-radius:2px;margin-bottom:2.5rem;}

/* Header */
.page-header{margin-bottom:2rem;}
.page-title{font-size:1.6rem;font-weight:500;color:var(--text);display:flex;align-items:center;gap:10px;}
.page-title i{font-size:1.3rem;color:var(--muted);}
.page-subtitle{font-size:0.75rem;color:var(--hint);text-transform:uppercase;letter-spacing:2.5px;margin-top:6px;}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:2.5rem;}
.stat-card{background:var(--surface-2);border-radius:var(--radius-lg);padding:1.25rem 1rem;border:0.5px solid var(--border);transition:border-color 0.2s,transform 0.2s;cursor:default;}
.stat-card:hover{border-color:var(--accent-info-border);transform:translateY(-2px);}
.stat-icon{font-size:1.1rem;color:var(--muted);margin-bottom:8px;}
.stat-value{font-size:1.9rem;font-weight:500;color:var(--text);line-height:1;}
.stat-label{font-size:0.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-top:5px;}

/* Section label */
.section-label{font-size:0.72rem;font-weight:500;text-transform:uppercase;letter-spacing:2px;color:var(--hint);margin-bottom:1rem;padding-bottom:8px;border-bottom:0.5px solid var(--border);}

/* Rec list */
.rec-list{display:flex;flex-direction:column;gap:10px;}
.rec-card{background:var(--surface);border:0.5px solid var(--border);border-radius:var(--radius-lg);padding:1rem 1.25rem;transition:border-color 0.2s,transform 0.15s;}
.rec-card:hover{border-color:var(--border-hover);transform:translateX(3px);}
.rec-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;}
.rec-meta{display:flex;flex-direction:column;gap:2px;}
.rec-id{font-size:0.72rem;font-weight:500;color:var(--accent-blue);font-family:monospace;}
.rec-email{font-size:0.7rem;color:var(--muted);}

/* Badges */
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.68rem;font-weight:500;}
.badge.defectueux{background:var(--accent-red-bg);color:var(--accent-red);}
.badge.retard{background:var(--accent-amber-bg);color:var(--accent-amber);}
.badge.manquant{background:var(--accent-blue-bg);color:var(--accent-blue);}
.badge.taille{background:var(--accent-green-bg);color:var(--accent-green);}
.badge.autre{background:var(--surface-2);color:var(--muted);}

.rec-name{font-size:0.95rem;font-weight:500;color:var(--text);}
.rec-order{font-size:0.75rem;color:var(--muted);margin-top:2px;display:flex;align-items:center;gap:4px;}
.rec-desc{margin-top:10px;padding:10px 12px;background:var(--surface-2);border-radius:var(--radius-md);border-left:2px solid var(--accent-info-border);font-size:0.82rem;color:var(--muted);line-height:1.65;}
.rec-photo{margin-top:10px;}
.rec-photo img{width:100%;max-height:180px;object-fit:cover;border-radius:var(--radius-md);border:0.5px solid var(--border);}
.rec-footer{display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:10px;border-top:0.5px solid var(--border);}
.rec-date{font-size:0.7rem;color:var(--hint);display:flex;align-items:center;gap:5px;}

@media(max-width:600px){
  .stats-grid{grid-template-columns:repeat(2,1fr);}
  .container{padding:1.25rem;}
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>



<div class="container">
  <div class="top-bar"></div>

  <div class="page-header">
    <h1 class="page-title">
      <i class="fas fa-exclamation-triangle"></i>
      Réclamations
    </h1>
    <p class="page-subtitle">Suivi des réclamations clients</p>
  </div>

  <!-- ===== STATISTIQUES ===== -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
      <div class="stat-value" id="statTotal"><?= $stat['total']  ?></div>
      <div class="stat-label">Total</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-tools"></i></div>
      <div class="stat-value" id="statDefectueux"><?=  $stat['defecteuses'] ?></div>
      <div class="stat-label">Défectueux</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-value" id="statRetard"><?= $stat['Livraison_retard'] ?></div>
      <div class="stat-label">Retards</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-box-open"></i></div>
      <div class="stat-value" id="statManquant"><?= $stat['manquant'] ?></div>
      <div class="stat-label">Manquants</div>
    </div>
  </div>

  <p class="section-label">Liste des réclamations</p>

  <!-- ===== LISTE DES RÉCLAMATIONS ===== -->
  <div class="rec-list" id="reclamationsGrid">
    <?php
      $badge_map = [
        'Produit défectueux' => 'defectueux',
        'Retard de livraison' => 'retard',
        'Produit manquant' => 'manquant',
        'Mauvaise taille / couleur' => 'taille',
        'Autre' => 'autre'
      ];
      $i = 1;
    ?>
    <?php foreach ($reclamations as $r):
      $badge_cls = $badge_map[$r['typeReclamation']] ?? 'autre';
    ?>
    <div class="rec-card">
      <div class="rec-top">
        <div class="rec-meta">
          <span class="rec-id">#<?= $i++ ?></span>
          <span class="rec-email"><?= htmlspecialchars($r['email']) ?></span>
        </div>
        <span class="badge <?= $badge_cls ?>">
          <?= htmlspecialchars($r['typeReclamation']) ?>
        </span>
      </div>
      <div class="rec-name"><?= htmlspecialchars($r['nom']) ?></div>
      <div class="rec-order">
        <i class="fas fa-hashtag" style="font-size:11px"></i>
        Commande #<?= htmlspecialchars($r['Numero_commande']) ?>
      </div>
      <div class="rec-desc">
        <?= htmlspecialchars($r['description']) ?>
      </div>
      <?php if (!empty($r['photo'])): ?>
      <div class="rec-photo">
        <img src="<?= htmlspecialchars($r['photo']) ?>" alt="Photo réclamation">
      </div>
      <?php endif; ?>
      <div class="rec-footer"></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>