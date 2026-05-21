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
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));border:2px solid var(--border);border-radius:20px;padding:30px;margin-bottom:25px;}
.card h2{font-family:'Orbitron',monospace;font-size:1.3rem;margin-bottom:20px;color:var(--neon-cyan);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:15px;border-bottom:1px solid var(--border);}
th{color:var(--muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:2px;font-weight:600;}
.badge{display:inline-block;padding:6px 14px;border-radius:30px;font-size:0.75rem;font-weight:700;text-transform:uppercase;}
.badge.pending{background:rgba(0,255,249,0.15);color:var(--neon-cyan);}
.badge.delivered{background:rgba(0,255,157,0.15);color:var(--success);}
.badge.cancelled{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.badge.processing{background:rgba(255,107,0,0.15);color:var(--neon-orange);}
.btn{padding:10px 18px;border-radius:12px;border:none;cursor:pointer;font-weight:600;transition:all 0.3s ease;}
.btn-danger{background:linear-gradient(135deg,var(--danger),#b91c1c);color:#fff;}
.btn-danger:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(239,68,68,0.4);}
.order-id{font-family:'Orbitron',monospace;color:var(--neon-cyan);font-weight:700;}
.order-amount{font-weight:700;color:var(--neon-pink);}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
<div class="glow-line"></div>

<div class="page-header">
  <h1 class="page-title">🛒 GESTION DES COMMANDES</h1>
</div>

<div class="card">
  <h2><i class="fas fa-list"></i> Liste des commandes</h2>

  <table>
    <tr>
      <th>Commande</th>
      <th>Client</th>
      <th>Date</th>
      <th>Montant</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
    <tr>
      <td><span class="order-id">#CMD-1023</span></td>
      <td>Jean Rakoto</td>
      <td>23/04/2026</td>
      <td><span class="order-amount">120 000 Ar</span></td>
      <td><span class="badge delivered"><i class="fas fa-check"></i> Livrée</span></td>
      <td><button class="btn btn-primary">Modifier</button></td>
    </tr>
    <tr>
      <td><span class="order-id">#CMD-1024</span></td>
      <td>Marie Rabe</td>
      <td>23/04/2026</td>
      <td><span class="order-amount">85 000 Ar</span></td>
      <td><span class="badge processing"><i class="fas fa-spinner"></i> En cours</span></td>
      <td><button class="btn btn-primary">Modifier</button></td>
    </tr>
    <tr>
      <td><span class="order-id">#CMD-1025</span></td>
      <td>Lina Andry</td>
      <td>22/04/2026</td>
      <td><span class="order-amount">60 000 Ar</span></td>
      <td><span class="badge cancelled"><i class="fas fa-times"></i> Annulée</span></td>
      <td><button class="btn btn-primary">Modifier</button></td>
    </tr>
    <tr>
      <td><span class="order-id">#CMD-1026</span></td>
      <td>Paul Randria</td>
      <td>21/04/2026</td>
      <td><span class="order-amount">45 000 Ar</span></td>
      <td><span class="badge pending"><i class="fas fa-clock"></i> En attente</span></td>
      <td><button class="btn btn-primary">Modifier</button></td>
    </tr>
  </table>
</div>

</div>
</body>
</html>
