<?php
  require_once "connectionBD.php";
  $sql="SELECT * FROM reclamation";
  $query=$pdo->prepare($sql);
  $query->execute();
  $reclamations=$query->fetchAll();
  $stat=["total"=>count($reclamations),"defecteuses"=>0,"Livraison_retard"=>0,"manquant"=>0,"Mauvaise_taille"=>0,"autre"=>0];
  foreach ($reclamations as $reclam) {
    switch($reclam['typeReclamation']){
        case 'Produit défectueux': $stat['defecteuses']++; break;
        case 'Retard de livraison': $stat['Livraison_retard']++; break;
        case 'Produit manquant': $stat['manquant']++; break;
        case 'Mauvaise taille / couleur': $stat['Mauvaise_taille']++; break;
        case 'Autre': $stat['autre']++; break;
    }
  }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réclamations</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#030712;--neon-cyan:#00fff9;--neon-pink:#ff006e;--neon-purple:#c800ff;
  --success:#00ff9d;--danger:#ff2b6e;--card:rgba(13,0,26,0.7);--text:#f0f0ff;
  --muted:#9ca3af;--border:rgba(0,255,249,0.12);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif;margin:0;padding:0;}
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-track{background:var(--bg);}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--neon-cyan),var(--neon-purple));border-radius:3px;}
body{background:var(--bg);color:var(--text);min-height:100vh;padding-top:90px;overflow-x:hidden;}
#particles{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,rgba(0,255,249,0.03)1px,transparent 1px),linear-gradient(rgba(0,255,249,0.03)1px,transparent 1px);background-size:50px 50px;pointer-events:none;z-index:0;}
.container{position:relative;z-index:1;max-width:1100px;margin:auto;padding:30px;}
.page-header{text-align:center;margin-bottom:40px;padding:30px 0;}
.page-title{font-family:'Orbitron',monospace;font-size:clamp(1.8rem,4vw,2.8rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 0 40px rgba(0,255,249,0.15);}
.page-subtitle{color:var(--muted);font-size:.85rem;letter-spacing:3px;text-transform:uppercase;margin-top:6px;}
.glow-line{height:4px;background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));border-radius:2px;margin-bottom:30px;animation:glow 2.5s linear infinite;background-size:200%;box-shadow:0 0 25px rgba(0,255,249,0.25);}
@keyframes glow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
@keyframes fadeUp{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:15px;margin-bottom:30px;}
.stat-card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:18px;padding:22px 20px;text-align:center;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all .35s ease;animation:fadeUp .5s ease both;}
.stat-card:hover{transform:translateY(-4px);border-color:rgba(0,255,249,0.3);box-shadow:0 12px 40px rgba(0,255,249,0.08);}
.stat-icon{font-size:1.5rem;margin-bottom:8px;opacity:.7;}
.stat-value{font-family:'Orbitron',monospace;font-size:2rem;font-weight:700;margin-bottom:4px;}
.stat-label{color:var(--muted);font-size:.75rem;text-transform:uppercase;letter-spacing:2px;}
.section-label{font-family:'Orbitron',monospace;font-size:.85rem;color:var(--neon-cyan);margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;}
.rec-list{display:flex;flex-direction:column;gap:12px;}
.rec-card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:16px;padding:18px 22px;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all .3s ease;animation:fadeUp .6s ease both;}
.rec-card:hover{border-color:rgba(0,255,249,0.25);transform:translateX(4px);box-shadow:0 8px 30px rgba(0,255,249,0.04);}
.rec-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;}
.rec-meta{display:flex;flex-direction:column;gap:2px;}
.rec-id{font-size:.72rem;font-weight:500;color:var(--neon-cyan);font-family:'Orbitron',monospace;}
.rec-email{font-size:.72rem;color:var(--muted);}
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:.66rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.badge.defectueux{background:rgba(255,0,110,0.12);color:var(--neon-pink);box-shadow:0 0 12px rgba(255,0,110,0.08);}
.badge.retard{background:rgba(255,107,0,0.12);color:var(--neon-orange);box-shadow:0 0 12px rgba(255,107,0,0.08);}
.badge.manquant{background:rgba(0,255,249,0.12);color:var(--neon-cyan);box-shadow:0 0 12px rgba(0,255,249,0.08);}
.badge.taille{background:rgba(0,255,157,0.12);color:var(--success);box-shadow:0 0 12px rgba(0,255,157,0.08);}
.badge.autre{background:rgba(200,0,255,0.12);color:var(--neon-purple);box-shadow:0 0 12px rgba(200,0,255,0.08);}
.rec-name{font-size:.95rem;font-weight:600;color:var(--text);}
.rec-order{font-size:.78rem;color:var(--muted);margin-top:4px;display:flex;align-items:center;gap:5px;}
.rec-desc{margin-top:10px;padding:12px 14px;background:rgba(0,0,0,0.2);border-radius:12px;border-left:2px solid var(--neon-cyan);font-size:.82rem;color:var(--muted);line-height:1.6;}
.rec-photo{margin-top:10px;}
.rec-photo img{width:100%;max-height:200px;object-fit:cover;border-radius:12px;border:1px solid var(--border);}
@media(max-width:600px){.stats-grid{grid-template-columns:repeat(2,1fr);}.container{padding:20px;}}
</style>
</head>
<body>
<canvas id="particles"></canvas>
<?php include 'navbar.php'; ?>

<div class="container">
<div class="glow-line"></div>
<div class="page-header">
  <h1 class="page-title"><i class="fas fa-exclamation-triangle"></i> RÉCLAMATIONS</h1>
  <p class="page-subtitle">Suivi des réclamations clients</p>
</div>

<div class="stats-grid">
  <div class="stat-card" style="animation-delay:.03s"><div class="stat-icon">📋</div><div class="stat-value" style="color:var(--neon-cyan)"><?=$stat['total']?></div><div class="stat-label">Total</div></div>
  <div class="stat-card" style="animation-delay:.06s"><div class="stat-icon">🔧</div><div class="stat-value" style="color:var(--neon-pink)"><?=$stat['defecteuses']?></div><div class="stat-label">Défectueux</div></div>
  <div class="stat-card" style="animation-delay:.09s"><div class="stat-icon">⏰</div><div class="stat-value" style="color:var(--neon-orange)"><?=$stat['Livraison_retard']?></div><div class="stat-label">Retards</div></div>
  <div class="stat-card" style="animation-delay:.12s"><div class="stat-icon">📦</div><div class="stat-value" style="color:var(--neon-cyan)"><?=$stat['manquant']?></div><div class="stat-label">Manquants</div></div>
  <div class="stat-card" style="animation-delay:.15s"><div class="stat-icon">👕</div><div class="stat-value" style="color:var(--success)"><?=$stat['Mauvaise_taille']?></div><div class="stat-label">Taille/Couleur</div></div>
  <div class="stat-card" style="animation-delay:.18s"><div class="stat-icon">⚙️</div><div class="stat-value" style="color:var(--neon-purple)"><?=$stat['autre']?></div><div class="stat-label">Autre</div></div>
</div>

<div class="section-label"><i class="fas fa-list"></i> Liste des réclamations</div>

<div class="rec-list">
<?php
  $badge_map = ['Produit défectueux'=>'defectueux','Retard de livraison'=>'retard','Produit manquant'=>'manquant','Mauvaise taille / couleur'=>'taille','Autre'=>'autre'];
  $i=1;
  foreach ($reclamations as $r):
    $badge_cls = $badge_map[$r['typeReclamation']] ?? 'autre';
?>
  <div class="rec-card" style="animation-delay:<?=min(.05*$i,.4)?>s">
    <div class="rec-top">
      <div class="rec-meta">
        <span class="rec-id">#<?=$i++?></span>
        <span class="rec-email"><?=htmlspecialchars($r['email'])?></span>
      </div>
      <span class="badge <?=$badge_cls?>"><?=htmlspecialchars($r['typeReclamation'])?></span>
    </div>
    <div class="rec-name"><?=htmlspecialchars($r['nom'])?></div>
    <div class="rec-order"><i class="fas fa-hashtag" style="font-size:10px"></i> Commande #<?=htmlspecialchars($r['Numero_commande'])?></div>
    <div class="rec-desc"><?=htmlspecialchars($r['description'])?></div>
    <?php if(!empty($r['photo'])): ?>
    <div class="rec-photo"><img src="<?=htmlspecialchars($r['photo'])?>" alt="Photo réclamation"></div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
</div>

<script>
const c=document.getElementById('particles'),x=c.getContext('2d');
let W,H,P;
const rsz=()=>{W=c.width=innerWidth;H=c.height=innerHeight;};
const ini=()=>{P=Array.from({length:30},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.2+.3,vx:(Math.random()-.5)*.15,vy:(Math.random()-.5)*.15,a:Math.random()*.5+.1}));};
const drw=()=>{
  x.clearRect(0,0,W,H);
  P.forEach(p=>{p.x+=p.vx;p.y+=p.vy;if(p.x<0)p.x=W;if(p.x>W)p.x=0;if(p.y<0)p.y=H;if(p.y>H)p.y=0;
    x.beginPath();x.arc(p.x,p.y,p.r,0,Math.PI*2);x.fillStyle=`rgba(0,255,249,${p.a*.25})`;x.fill();});
  for(let i=0;i<P.length;i++)for(let j=i+1;j<P.length;j++){
    const dx=P[i].x-P[j].x,dy=P[i].y-P[j].y,d=Math.sqrt(dx*dx+dy*dy);
    if(d<100){x.beginPath();x.moveTo(P[i].x,P[i].y);x.lineTo(P[j].x,P[j].y);x.strokeStyle=`rgba(0,255,249,${(1-d/100)*.03})`;x.lineWidth=.5;x.stroke();}}
  requestAnimationFrame(drw);
};
addEventListener('resize',()=>{rsz();ini();});
rsz();ini();drw();
</script>
</body>
</html>