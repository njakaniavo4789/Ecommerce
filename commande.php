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
  --card:rgba(13,0,26,0.7);
  --card-hover:rgba(20,0,40,0.85);
  --text:#f0f0ff;
  --muted:#9ca3af;
  --border:rgba(0,255,249,0.12);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif;margin:0;padding:0;}
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-track{background:var(--bg);}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--neon-cyan),var(--neon-purple));border-radius:3px;}
body{background:var(--bg);color:var(--text);min-height:100vh;position:relative;padding-top:90px;overflow-x:hidden;}
#particles{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,rgba(0,255,249,0.03) 1px,transparent 1px),linear-gradient(rgba(0,255,249,0.03) 1px,transparent 1px);background-size:50px 50px;pointer-events:none;z-index:0;}
.container{position:relative;z-index:1;max-width:1300px;margin:auto;padding:30px 25px;}
.page-header{text-align:center;margin-bottom:45px;padding:30px 0;}
.page-title{font-family:'Orbitron',monospace;font-size:clamp(1.8rem,4vw,2.8rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 0 40px rgba(0,255,249,0.15);}
.glow-line{height:4px;background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));border-radius:2px;margin-bottom:30px;animation:glow 2.5s linear infinite;background-size:200%;box-shadow:0 0 25px rgba(0,255,249,0.25);}
@keyframes glow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
@keyframes fadeUp{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.5}}
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:20px;padding:28px;margin-bottom:25px;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all 0.4s ease;animation:fadeUp 0.6s ease both;}
.card:hover{border-color:rgba(0,255,249,0.25);box-shadow:0 8px 40px rgba(0,255,249,0.05);}
.card h2{font-family:'Orbitron',monospace;font-size:1.15rem;margin-bottom:22px;color:var(--neon-cyan);display:flex;align-items:center;gap:10px;}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:30px;animation:fadeUp 0.5s ease both;}
.stat-card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:18px;padding:24px;text-align:center;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all 0.35s ease;position:relative;overflow:hidden;}
.stat-card:hover{transform:translateY(-4px);border-color:rgba(0,255,249,0.3);box-shadow:0 12px 40px rgba(0,255,249,0.08);}
.stat-card .sicon{font-size:2rem;margin-bottom:8px;opacity:0.7;}
.stat-card .sval{font-family:'Orbitron',monospace;font-size:2rem;font-weight:700;margin-bottom:4px;}
.stat-card .slbl{color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:2px;}
.stats-cyan .sval{color:var(--neon-cyan);}
.stats-pink .sval{color:var(--neon-pink);}
.stats-purple .sval{color:var(--neon-purple);}
.stats-green .sval{color:var(--success);}
.stats-cyan .sicon{color:var(--neon-cyan);}
.stats-pink .sicon{color:var(--neon-pink);}
.stats-purple .sicon{color:var(--neon-purple);}
.stats-green .sicon{color:var(--success);}
.table-wrap{overflow-x:auto;border-radius:16px;}
table{width:100%;border-collapse:separate;border-spacing:0;margin-top:5px;min-width:800px;}
th{padding:16px 18px;text-align:left;color:var(--muted);font-size:.75rem;text-transform:uppercase;letter-spacing:2px;font-weight:600;border-bottom:1px solid var(--border);background:rgba(0,255,249,0.03);}
td{padding:14px 18px;border-bottom:1px solid rgba(255,255,255,0.03);transition:background 0.25s ease;}
tr:hover td{background:rgba(0,255,249,0.04);}
tr:last-child td{border-bottom:none;}
.badge{display:inline-block;padding:5px 14px;border-radius:30px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.badge.en_attente{background:rgba(0,255,249,0.12);color:var(--neon-cyan);box-shadow:0 0 12px rgba(0,255,249,0.08);}
.badge.en_cours{background:rgba(255,107,0,0.12);color:var(--neon-orange);box-shadow:0 0 12px rgba(255,107,0,0.08);}
.badge.expedie{background:rgba(200,0,255,0.12);color:var(--neon-purple);box-shadow:0 0 12px rgba(200,0,255,0.08);}
.badge.livre{background:rgba(0,255,157,0.12);color:var(--success);box-shadow:0 0 12px rgba(0,255,157,0.08);}
.badge.annule{background:rgba(255,0,110,0.12);color:var(--neon-pink);box-shadow:0 0 12px rgba(255,0,110,0.08);}
.btn-s{padding:8px 16px;border-radius:12px;border:none;cursor:pointer;font-weight:600;font-size:.78rem;transition:all .3s;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
.btn-cyan{background:linear-gradient(135deg,rgba(0,255,249,0.15),rgba(0,153,153,0.1));color:var(--neon-cyan);border:1px solid rgba(0,255,249,0.2);margin-right:5px;}
.btn-cyan:hover{background:linear-gradient(135deg,rgba(0,255,249,0.25),rgba(0,153,153,0.15));transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,255,249,0.15);}
.btn-pink{background:linear-gradient(135deg,rgba(255,0,110,0.15),rgba(153,0,102,0.1));color:var(--neon-pink);border:1px solid rgba(255,0,110,0.2);}
.btn-pink:hover{background:linear-gradient(135deg,rgba(255,0,110,0.25),rgba(153,0,102,0.15));transform:translateY(-2px);box-shadow:0 6px 20px rgba(255,0,110,0.15);}
.order-id{font-family:'Orbitron',monospace;color:var(--neon-cyan);font-weight:700;font-size:.82rem;}
.order-amount{font-weight:700;color:var(--neon-pink);}
.status-form{display:inline-block;}
.status-form select{padding:7px 12px;border-radius:10px;background:rgba(255,255,255,.04);color:var(--text);border:1px solid var(--border);font-size:.78rem;cursor:pointer;transition:all .3s;}
.status-form select:hover{border-color:rgba(0,255,249,0.3);}
.status-form select:focus{outline:none;border-color:var(--neon-cyan);box-shadow:0 0 15px rgba(0,255,249,0.12);}
.empty-state{text-align:center;padding:60px 0;color:var(--muted);}
.empty-state .ico{font-size:3.5rem;margin-bottom:15px;opacity:0.5;}
@media(max-width:768px){.stat-grid{grid-template-columns:1fr 1fr;}.container{padding:20px 15px;}}
</style>
</head>
<body>
<canvas id="particles"></canvas>
<?php include 'navbar.php'; ?>
<?php require 'connectionBD.php'; ?>

<div class="container">
<div class="glow-line"></div>

<div class="page-header">
  <h1 class="page-title"><i class="fas fa-truck"></i> GESTION DES COMMANDES</h1>
</div>

<?php
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM commandes WHERE id = ?")->execute([$id]);
    echo '<script>window.location="commande.php";</script>';
    exit();
}
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

<div class="stat-grid">
  <div class="stat-card stats-cyan" style="animation-delay:.05s">
    <div class="sicon"><i class="fas fa-shopping-cart"></i></div>
    <div class="sval"><?= $total_cmd ?></div>
    <div class="slbl">Total Commandes</div>
  </div>
  <div class="stat-card stats-pink" style="animation-delay:.1s">
    <div class="sicon"><i class="fas fa-coins"></i></div>
    <div class="sval"><?= number_format($total_rev, 0, ',', ' ') ?> Ar</div>
    <div class="slbl">Revenu Total</div>
  </div>
  <div class="stat-card stats-purple" style="animation-delay:.15s">
    <div class="sicon"><i class="fas fa-clock"></i></div>
    <div class="sval"><?= $stats['en_attente'] ?? 0 ?></div>
    <div class="slbl">En attente</div>
  </div>
  <div class="stat-card stats-green" style="animation-delay:.2s">
    <div class="sicon"><i class="fas fa-check-circle"></i></div>
    <div class="sval"><?= $stats['livre'] ?? 0 ?></div>
    <div class="slbl">Livrées</div>
  </div>
</div>

<div class="card" style="animation-delay:.15s">
  <h2><i class="fas fa-list"></i> Liste des commandes</h2>
  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <div class="ico">📦</div>
      <p>Aucune commande pour le moment.</p>
    </div>
  <?php else: ?>
  <div class="table-wrap">
  <table>
    <tr><th>N°</th><th>Client</th><th>Email</th><th>Date</th><th>Montant</th><th>Paiement</th><th>Statut</th><th>Actions</th></tr>
    <?php foreach ($orders as $o): ?>
    <tr>
      <td><span class="order-id">#CMD-<?= $o['id'] ?></span></td>
      <td><?= htmlspecialchars(($o['prenom_client'] ?? '') . ' ' . ($o['nom_client'] ?? 'Client')) ?></td>
      <td style="font-size:.82rem;color:var(--muted);"><?= htmlspecialchars($o['email_client'] ?? '-') ?></td>
      <td style="font-size:.82rem;color:var(--muted);"><?= date('d/m/Y H:i', strtotime($o['date_commande'])) ?></td>
      <td><span class="order-amount"><?= number_format($o['total'], 0, ',', ' ') ?> Ar</span></td>
      <td style="font-size:.82rem;"><?= htmlspecialchars($o['mode_paiement'] ?? '-') ?></td>
      <td>
        <form method="GET" class="status-form">
          <input type="hidden" name="update" value="<?= $o['id'] ?>">
          <select name="statut" onchange="this.form.submit()">
            <?php $labels = ['en_attente'=>'En attente','en_cours'=>'En cours','expedie'=>'Expédié','livre'=>'Livré','annule'=>'Annulé']; ?>
            <?php foreach (['en_attente','en_cours','expedie','livre','annule'] as $s): ?>
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
  </div>
  <?php endif; ?>
</div>
</div>

<script>
const c=document.getElementById('particles'),x=c.getContext('2d');
let W,H,P;
const rsz=()=>{W=c.width=innerWidth;H=c.height=innerHeight;};
const ini=()=>{P=Array.from({length:35},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.2+.3,vx:(Math.random()-.5)*.15,vy:(Math.random()-.5)*.15,a:Math.random()*.5+.1}));};
const drw=()=>{
  x.clearRect(0,0,W,H);
  P.forEach(p=>{p.x+=p.vx;p.y+=p.vy;if(p.x<0)p.x=W;if(p.x>W)p.x=0;if(p.y<0)p.y=H;if(p.y>H)p.y=0;
    x.beginPath();x.arc(p.x,p.y,p.r,0,Math.PI*2);x.fillStyle=`rgba(0,255,249,${p.a*.25})`;x.fill();});
  for(let i=0;i<P.length;i++)for(let j=i+1;j<P.length;j++){
    const dx=P[i].x-P[j].x,dy=P[i].y-P[j].y,d=Math.sqrt(dx*dx+dy*dy);
    if(d<100){x.beginPath();x.moveTo(P[i].x,P[i].y);x.lineTo(P[j].x,P[j].y);x.strokeStyle=`rgba(0,255,249,${(1-d/100)*.03})`;x.lineWidth=.5;x.stroke();}
  }
  requestAnimationFrame(drw);
};
addEventListener('resize',()=>{rsz();ini();});
rsz();ini();drw();
</script>
</body>
</html>