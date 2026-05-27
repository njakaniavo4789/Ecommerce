<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin – Statistiques & Rapports</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root{
  --bg:#030712;--neon-cyan:#00fff9;--neon-pink:#ff006e;--neon-purple:#c800ff;
  --neon-orange:#ff6b00;--success:#00ff9d;--danger:#ff2b6e;--card:rgba(13,0,26,0.7);
  --text:#f0f0ff;--muted:#9ca3af;--border:rgba(0,255,249,0.12);
}
*{box-sizing:border-box;font-family:'Inter',sans-serif;margin:0;padding:0;}
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-track{background:var(--bg);}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--neon-cyan),var(--neon-purple));border-radius:3px;}
body{background:var(--bg);color:var(--text);min-height:100vh;padding-top:90px;overflow-x:hidden;}
#particles{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;}
body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,rgba(0,255,249,0.03)1px,transparent 1px),linear-gradient(rgba(0,255,249,0.03)1px,transparent 1px);background-size:50px 50px;pointer-events:none;z-index:0;}
.container{position:relative;z-index:1;max-width:1400px;margin:auto;padding:30px;}
.page-header{text-align:center;margin-bottom:40px;padding:30px 0;}
.page-title{font-family:'Orbitron',monospace;font-size:clamp(1.8rem,4vw,3rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 0 40px rgba(0,255,249,0.15);}
.page-subtitle{color:var(--muted);font-size:.95rem;letter-spacing:3px;text-transform:uppercase;margin-top:8px;}
.glow-line{height:4px;background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));border-radius:2px;margin-bottom:30px;animation:glow 2.5s linear infinite;background-size:200%;box-shadow:0 0 25px rgba(0,255,249,0.25);}
@keyframes glow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
@keyframes fadeUp{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:40px;}
.stat-card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:20px;padding:25px;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all .35s ease;position:relative;overflow:hidden;animation:fadeUp .5s ease both;}
.stat-card:hover{transform:translateY(-5px);border-color:rgba(0,255,249,0.3);box-shadow:0 12px 40px rgba(0,255,249,0.08);}
.stat-icon{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:12px;}
.stat-icon.cyan{background:rgba(0,255,249,0.12);color:var(--neon-cyan);}
.stat-icon.pink{background:rgba(255,0,110,0.12);color:var(--neon-pink);}
.stat-icon.purple{background:rgba(200,0,255,0.12);color:var(--neon-purple);}
.stat-icon.green{background:rgba(0,255,157,0.12);color:var(--success);}
.stat-icon.orange{background:rgba(255,107,0,0.12);color:var(--neon-orange);}
.stat-value{font-family:'Orbitron',monospace;font-size:1.8rem;font-weight:900;margin-bottom:4px;background:linear-gradient(90deg,#fff,var(--neon-cyan));-webkit-background-clip:text;background-clip:text;color:transparent;}
.stat-label{color:var(--muted);font-size:.82rem;text-transform:uppercase;letter-spacing:2px;}
.stat-sub{font-size:.72rem;color:var(--muted);margin-top:6px;}
.charts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(450px,1fr));gap:25px;margin-bottom:40px;}
.chart-card,.table-card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:20px;padding:28px;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all .4s ease;animation:fadeUp .6s ease both;}
.chart-card:hover,.table-card:hover{border-color:rgba(0,255,249,0.25);box-shadow:0 8px 40px rgba(0,255,249,0.05);}
.chart-card h3,.table-card h3{font-family:'Orbitron',monospace;font-size:1.1rem;margin-bottom:18px;color:var(--neon-cyan);display:flex;align-items:center;gap:10px;}
.chart-card canvas{width:100% !important;height:300px !important;}
.chart-card .chart-rows{display:flex;flex-wrap:wrap;gap:12px;margin-top:15px;}
.chart-card .chart-rows .cr{display:flex;align-items:center;gap:8px;font-size:.78rem;color:var(--muted);}
.chart-card .chart-rows .cr .dot{width:10px;height:10px;border-radius:50%;}
.table-wrap{overflow-x:auto;border-radius:14px;}
table{width:100%;border-collapse:separate;border-spacing:0;margin-top:5px;min-width:600px;}
th{padding:13px 15px;text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:2px;font-weight:600;border-bottom:1px solid var(--border);background:rgba(0,255,249,0.03);}
td{padding:12px 15px;border-bottom:1px solid rgba(255,255,255,0.03);transition:background .25s;}
tr:hover td{background:rgba(0,255,249,0.04);}
tr:last-child td{border-bottom:none;}
.badge{display:inline-block;padding:4px 12px;border-radius:30px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.badge.livre{background:rgba(0,255,157,0.12);color:var(--success);box-shadow:0 0 12px rgba(0,255,157,0.08);}
.badge.en_attente{background:rgba(0,255,249,0.12);color:var(--neon-cyan);box-shadow:0 0 12px rgba(0,255,249,0.08);}
.badge.en_cours{background:rgba(255,107,0,0.12);color:var(--neon-orange);box-shadow:0 0 12px rgba(255,107,0,0.08);}
.badge.annule{background:rgba(255,0,110,0.12);color:var(--neon-pink);box-shadow:0 0 12px rgba(255,0,110,0.08);}
.badge.expedie{background:rgba(200,0,255,0.12);color:var(--neon-purple);box-shadow:0 0 12px rgba(200,0,255,0.08);}
.order-id{font-family:'Orbitron',monospace;color:var(--neon-cyan);font-weight:700;font-size:.8rem;}
.order-amount{font-weight:700;color:var(--neon-pink);}
@media(max-width:768px){.charts-grid{grid-template-columns:1fr;}.stat-value{font-size:1.4rem;}.container{padding:20px;}}
</style>
</head>
<body>
<canvas id="particles"></canvas>
<?php include 'navbar.php'; ?>
<?php require 'connectionBD.php';

// --- Aggréger les stats depuis les commandes ---
$total_cmd = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$total_rev = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes")->fetchColumn();
$total_prod = $pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();
$total_promo = $pdo->query("SELECT COUNT(*) FROM marketing")->fetchColumn();
$mois_rev = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE MONTH(date_commande)=MONTH(CURDATE()) AND YEAR(date_commande)=YEAR(CURDATE())")->fetchColumn();
$mois_cmd = $pdo->query("SELECT COUNT(*) FROM commandes WHERE MONTH(date_commande)=MONTH(CURDATE()) AND YEAR(date_commande)=YEAR(CURDATE())")->fetchColumn();
$mois_prec_rev = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE MONTH(date_commande)=MONTH(CURDATE())-1 AND YEAR(date_commande)=YEAR(CURDATE())")->fetchColumn();
$evol_pct = $mois_prec_rev > 0 ? round(($mois_rev - $mois_prec_rev) / $mois_prec_rev * 100) : 100;

$stats = $pdo->query("SELECT statut, COUNT(*) as cnt FROM commandes GROUP BY statut")->fetchAll(PDO::FETCH_KEY_PAIR);
$en_attente = $stats['en_attente'] ?? 0;
$en_cours = $stats['en_cours'] ?? 0;
$expedie = $stats['expedie'] ?? 0;
$livre = $stats['livre'] ?? 0;
$annule = $stats['annule'] ?? 0;

// --- Données des graphiques depuis stats_ventes ---
$ventes_mensuelles = $pdo->query("SELECT DATE_FORMAT(date,'%b') as mois, SUM(total_ventes) as total, SUM(nombre_commandes) as nb FROM stats_ventes GROUP BY YEAR(date), MONTH(date) ORDER BY YEAR(date), MONTH(date) LIMIT 12")->fetchAll();
$mois_labels = []; $mois_data = [];
foreach ($ventes_mensuelles as $v) { $mois_labels[] = $v['mois']; $mois_data[] = (float)$v['total']; }

// Top produits depuis stats_produits
$top_produits = $pdo->query("SELECT produit_nom, SUM(total_vendu) as total FROM stats_produits GROUP BY produit_id ORDER BY total DESC LIMIT 8")->fetchAll();
$prod_labels = []; $prod_data = []; $prod_colors = ['#00fff9','#ff006e','#c800ff','#ff6b00','#00ff9d','#eab308','#6366f1','#f97316'];
foreach ($top_produits as $i=>$p) { $prod_labels[] = $p['produit_nom']; $prod_data[] = (int)$p['total']; }

// Stats paiements
$paiements = $pdo->query("SELECT mode_paiement, SUM(total) as total, SUM(nombre) as nb FROM stats_paiements GROUP BY mode_paiement ORDER BY total DESC")->fetchAll();

// Dernières commandes
$orders = $pdo->query("SELECT * FROM commandes ORDER BY date_commande DESC LIMIT 5")->fetchAll();
$labels = ['en_attente'=>'En attente','en_cours'=>'En cours','expedie'=>'Expédié','livre'=>'Livré','annule'=>'Annulé'];
$badge_classes = ['en_attente'=>'en_attente','en_cours'=>'en_cours','expedie'=>'expedie','livre'=>'livre','annule'=>'annule'];
?>

<div class="container">
<div class="glow-line"></div>
<div class="page-header">
  <h1 class="page-title">📊 TABLEAU DE BORD</h1>
  <p class="page-subtitle">Statistiques en temps réel · Données stockées en base</p>
</div>

<div class="stats-grid">
  <div class="stat-card" style="animation-delay:.03s">
    <div class="stat-icon cyan"><i class="fas fa-shopping-cart"></i></div>
    <div class="stat-value"><?= $total_cmd ?></div>
    <div class="stat-label">Commandes Totales</div>
    <div class="stat-sub"><?= $mois_cmd ?> ce mois</div>
  </div>
  <div class="stat-card" style="animation-delay:.06s">
    <div class="stat-icon pink"><i class="fas fa-money-bill-wave"></i></div>
    <div class="stat-value"><?= number_format($total_rev/1000000,1) ?>M</div>
    <div class="stat-label">Revenus Totals</div>
    <div class="stat-sub"><?= number_format($mois_rev/1000000,1) ?>M ce mois <?= $evol_pct>=0?'(+':'('.$evol_pct.'%)'?></div>
  </div>
  <div class="stat-card" style="animation-delay:.09s">
    <div class="stat-icon purple"><i class="fas fa-boxes"></i></div>
    <div class="stat-value"><?= $total_prod ?></div>
    <div class="stat-label">Produits</div>
  </div>
  <div class="stat-card" style="animation-delay:.12s">
    <div class="stat-icon orange"><i class="fas fa-bullhorn"></i></div>
    <div class="stat-value"><?= $total_promo ?></div>
    <div class="stat-label">Promotions</div>
  </div>
  <div class="stat-card" style="animation-delay:.15s">
    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    <div class="stat-value"><?= $livre ?></div>
    <div class="stat-label">Commandes Livrées</div>
  </div>
  <div class="stat-card" style="animation-delay:.18s">
    <div class="stat-icon cyan"><i class="fas fa-clock"></i></div>
    <div class="stat-value"><?= $en_attente ?></div>
    <div class="stat-label">En attente</div>
  </div>
</div>

<div class="charts-grid">
  <div class="chart-card" style="animation-delay:.1s">
    <h3>📈 Ventes Mensuelles</h3>
    <canvas id="chartVentes"></canvas>
  </div>
  <div class="chart-card" style="animation-delay:.15s">
    <h3>🏆 Top Produits Vendus</h3>
    <canvas id="chartProduits"></canvas>
    <?php if(!empty($prod_labels)): ?>
    <div class="chart-rows">
      <?php foreach($top_produits as $i=>$p): ?>
      <div class="cr"><span class="dot" style="background:<?=$prod_colors[$i]??'#6366f1'?>"></span> <?=htmlspecialchars($p['produit_nom'])?>: <strong><?=$p['total']?></strong></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:25px;margin-bottom:40px;">
  <div class="chart-card" style="animation-delay:.2s">
    <h3>🥧 Statut des Commandes</h3>
    <canvas id="chartStatus"></canvas>
  </div>
  <div class="chart-card" style="animation-delay:.2s">
    <h3>💳 Paiements</h3>
    <canvas id="chartPaiements"></canvas>
    <?php if(!empty($paiements)): ?>
    <div class="chart-rows">
      <?php foreach($paiements as $p): ?>
      <div class="cr"><span class="dot" style="background:<?=['#00fff9','#ff006e','#c800ff','#00ff9d','#ff6b00'][array_search($p['mode_paiement'],array_column($paiements,'mode_paiement'))%5]?>"></span> <?=htmlspecialchars($p['mode_paiement'] ?? 'Inconnu')?>: <strong><?=number_format($p['total'],0,',',' ')?> Ar</strong> (<?=$p['nb']?> cmd)</div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="table-card" style="animation-delay:.25s">
  <h3>📋 Dernières Commandes</h3>
  <div class="table-wrap">
  <table>
    <tr><th>Commande</th><th>Client</th><th>Date</th><th>Montant</th><th>Statut</th></tr>
    <?php if (empty($orders)): ?>
    <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;">Aucune commande</td></tr>
    <?php else: foreach($orders as $o): ?>
    <tr>
      <td><span class="order-id">#CMD-<?=$o['id']?></span></td>
      <td><?=htmlspecialchars(($o['prenom_client']??'').' '.($o['nom_client']??'Client'))?></td>
      <td style="font-size:.82rem;color:var(--muted)"><?=date('d/m/Y',strtotime($o['date_commande']))?></td>
      <td><span class="order-amount"><?=number_format($o['total'],0,',',' ')?> Ar</span></td>
      <td><span class="badge <?=$badge_classes[$o['statut']]??'en_attente'?>"><?=$labels[$o['statut']]??$o['statut']?></span></td>
    </tr>
    <?php endforeach; endif; ?>
  </table>
  </div>
</div>
</div>

<script>
Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = 'rgba(0,255,249,0.15)';

new Chart(document.getElementById('chartVentes'),{
  type:'line',
  data:{
    labels:<?=json_encode($mois_labels)?>,
    datasets:[{
      label:'Ventes (Ar)',
      data:<?=json_encode($mois_data)?>,
      borderColor:'#00fff9',
      backgroundColor:'rgba(0,255,249,0.1)',
      fill:true,tension:0.4,
      pointBackgroundColor:'#00fff9',pointBorderColor:'#0f172a',pointBorderWidth:2,pointRadius:5,pointHoverRadius:9
    }]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{y:{grid:{color:'rgba(0,255,249,0.1)'},ticks:{callback:v=>v>=1000000?(v/1000000)+'M':v>=1000?(v/1000)+'k':v}},x:{grid:{color:'rgba(0,255,249,0.1)'}}}}
});

new Chart(document.getElementById('chartProduits'),{
  type:'bar',
  data:{
    labels:<?=json_encode($prod_labels)?>,
    datasets:[{label:'Vendus',data:<?=json_encode($prod_data)?>,backgroundColor:<?=json_encode(array_slice($prod_colors,0,count($prod_labels)))?>,borderRadius:8,borderSkipped:false}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{y:{grid:{color:'rgba(0,255,249,0.1)'},ticks:{stepSize:1}},x:{grid:{display:false}}}
});

new Chart(document.getElementById('chartStatus'),{
  type:'doughnut',
  data:{
    labels:['Livrées','En cours','En attente','Expédiées','Annulées'],
    datasets:[{data:[<?=$livre?>,<?=$en_cours?>,<?=$en_attente?>,<?=$expedie?>,<?=$annule?>],backgroundColor:['#00ff9d','#ff6b00','#00fff9','#c800ff','#ff006e'],borderWidth:0,hoverOffset:10}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{padding:18,usePointStyle:true,pointStyle:'circle'}}},cutout:'70%'}
});

new Chart(document.getElementById('chartPaiements'),{
  type:'doughnut',
  data:{
    labels:<?=json_encode(array_map(function($p){return $p['mode_paiement']??'Inconnu';},$paiements))?>,
    datasets:[{data:<?=json_encode(array_map(function($p){return (float)$p['total'];},$paiements))?>,backgroundColor:['#00fff9','#ff006e','#c800ff','#00ff9d','#ff6b00'],borderWidth:0,hoverOffset:10}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{padding:18,usePointStyle:true,pointStyle:'circle'}}},cutout:'70%'}
});
</script>

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
    if(d<100){x.beginPath();x.moveTo(P[i].x,P[i].y);x.lineTo(P[j].x,P[j].y);x.strokeStyle=`rgba(0,255,249,${(1-d/100)*.03})`;x.lineWidth=.5;x.stroke();}}
  requestAnimationFrame(drw);
};
addEventListener('resize',()=>{rsz();ini();});
rsz();ini();drw();
</script>
</body>
</html>