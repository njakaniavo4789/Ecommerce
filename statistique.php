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

body{
  background:var(--bg);
  color:var(--text);
  min-height:100vh;
  overflow-x:hidden;
  position:relative;
  padding-top:90px;
}

body::before{
  content:'';
  position:fixed;
  top:0;left:0;
  width:100%;height:100%;
  background:linear-gradient(90deg,rgba(0,255,249,0.03) 1px,transparent 1px),
              linear-gradient(rgba(0,255,249,0.03) 1px,transparent 1px);
  background-size:50px 50px;
  pointer-events:none;
  z-index:0;
}

.navbar-wrapper{position:relative;z-index:1001;}

.container{
  position:relative;
  z-index:1;
  max-width:1400px;
  margin:0 auto;
  padding:30px;
}

.page-header{
  text-align:center;
  margin-bottom:40px;
  padding:30px 0;
}

.page-title{
  font-family:'Orbitron',monospace;
  font-size: clamp(2rem, 5vw, 3.5rem);
  background: linear-gradient(90deg, var(--neon-cyan), var(--neon-pink), var(--neon-purple));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  text-shadow: 0 0 40px rgba(0,255,249,0.3);
  margin-bottom:10px;
}

.page-subtitle{
  color:var(--muted);
  font-size:1.1rem;
  letter-spacing:3px;
  text-transform:uppercase;
}

.stats-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
  gap:20px;
  margin-bottom:40px;
}

.stat-card{
  background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));
  border:2px solid var(--border);
  border-radius:20px;
  padding:30px;
  position:relative;
  overflow:hidden;
  transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card::before{
  content:'';
  position:absolute;
  top:0;left:0;width:100%;height:100%;
  background:linear-gradient(135deg,rgba(0,255,249,0.1),rgba(255,0,110,0.1));
  opacity:0;
  transition:opacity 0.4s;
}

.stat-card:hover{
  transform:translateY(-8px);
  border-color:var(--neon-cyan);
  box-shadow:0 20px 50px rgba(0,255,249,0.2);
}

.stat-card:hover::before{opacity:1;}

.stat-icon{
  width:60px;
  height:60px;
  border-radius:15px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1.5rem;
  margin-bottom:15px;
}

.stat-icon.cyan{background:rgba(0,255,249,0.15);color:var(--neon-cyan);}
.stat-icon.pink{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.stat-icon.purple{background:rgba(200,0,255,0.15);color:var(--neon-purple);}
.stat-icon.green{background:rgba(0,255,157,0.15);color:var(--success);}
.stat-icon.orange{background:rgba(255,107,0,0.15);color:var(--neon-orange);}

.stat-value{
  font-family:'Orbitron',monospace;
  font-size:2.2rem;
  font-weight:900;
  margin-bottom:5px;
  background: linear-gradient(90deg, #fff, var(--neon-cyan));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.stat-label{
  color:var(--muted);
  font-size:0.95rem;
  text-transform:uppercase;
  letter-spacing:2px;
}

.stat-change{
  display:inline-flex;
  align-items:center;
  gap:5px;
  padding:5px 10px;
  border-radius:20px;
  font-size:0.8rem;
  font-weight:600;
  margin-top:10px;
}

.stat-change.up{background:rgba(0,255,157,0.15);color:var(--success);}
.stat-change.down{background:rgba(255,0,110,0.15);color:var(--neon-pink);}

.charts-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(500px,1fr));
  gap:25px;
  margin-bottom:40px;
}

.chart-card{
  background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));
  border:2px solid var(--border);
  border-radius:20px;
  padding:30px;
  position:relative;
}

.chart-card h3{
  font-family:'Orbitron',monospace;
  font-size:1.3rem;
  margin-bottom:20px;
  color:var(--neon-cyan);
}

.chart-card canvas{
  width:100% !important;
  height:300px !important;
}

.table-card{
  background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.8));
  border:2px solid var(--border);
  border-radius:20px;
  padding:30px;
  position:relative;
}

.table-card h3{
  font-family:'Orbitron',monospace;
  font-size:1.3rem;
  margin-bottom:20px;
  color:var(--neon-cyan);
}

table{width:100%;border-collapse:collapse;}

th,td{padding:15px;text-align:left;border-bottom:1px solid var(--border);}

th{
  color:var(--muted);
  font-size:0.8rem;
  text-transform:uppercase;
  letter-spacing:2px;
  font-weight:600;
}

td{font-weight:500;}

.badge{
  display:inline-block;
  padding:6px 14px;
  border-radius:30px;
  font-size:0.75rem;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:1px;
}

.badge.delivered{background:rgba(0,255,157,0.15);color:var(--success);}
.badge.pending{background:rgba(0,255,249,0.15);color:var(--neon-cyan);}
.badge.cancelled{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.badge.processing{background:rgba(255,107,0,0.15);color:var(--neon-orange);}

.order-id{
  font-family:'Orbitron',monospace;
  color:var(--neon-cyan);
  font-weight:700;
}

.order-amount{
  font-weight:700;
  color:var(--neon-pink);
}

.glow-line{
  height:4px;
  background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));
  border-radius:2px;
  margin-bottom:30px;
  animation:glow 2s linear infinite;
  background-size:200%;
}

@keyframes glow{
  0%{background-position:0% 50%}
  100%{background-position:200% 50%}
}

@media (max-width:768px){
  .charts-grid{grid-template-columns:1fr;}
  .stat-value{font-size:1.8rem;}
  .container{padding:20px;}
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">

<div class="glow-line"></div>

<div class="page-header">
  <h1 class="page-title">📊 TABLEAU DE BORD</h1>
  <p class="page-subtitle">Statistiques et rapports en temps réel</p>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon cyan"><i class="fas fa-shopping-cart"></i></div>
    <div class="stat-value">247</div>
    <div class="stat-label">Commandes Totales</div>
    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +18% ce mois</div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon pink"><i class="fas fa-money-bill-wave"></i></div>
    <div class="stat-value">85M Ar</div>
    <div class="stat-label">Revenus Totals</div>
    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +24% ce mois</div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon purple"><i class="fas fa-users"></i></div>
    <div class="stat-value">156</div>
    <div class="stat-label">Clients Actifs</div>
    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +12% ce mois</div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon green"><i class="fas fa-boxes"></i></div>
    <div class="stat-value">48</div>
    <div class="stat-label">Produits</div>
    <div class="stat-change down"><i class="fas fa-arrow-down"></i> -5% ce mois</div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon orange"><i class="fas fa-bullhorn"></i></div>
    <div class="stat-value">8</div>
    <div class="stat-label">Promos Actives</div>
    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +3 ce mois</div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon cyan"><i class="fas fa-star"></i></div>
    <div class="stat-value">4.8</div>
    <div class="stat-label">Note Moyenne</div>
    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +0.2 ce mois</div>
  </div>
</div>

<div class="charts-grid">
  <div class="chart-card">
    <h3>📈 Ventes par Mois</h3>
    <canvas id="chartVentes"></canvas>
  </div>
  
  <div class="chart-card">
    <h3>🏆 Top Produits Vendus</h3>
    <canvas id="chartProduits"></canvas>
  </div>
</div>

<div class="chart-card" style="margin-bottom:40px;">
  <h3>🥧 Répartition des Commandes par Statut</h3>
  <canvas id="chartStatus"></canvas>
</div>

<div class="table-card">
  <h3>📋 Dernières Commandes</h3>
  <table>
    <thead>
      <tr>
        <th>Commande</th>
        <th>Client</th>
        <th>Date</th>
        <th>Montant</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><span class="order-id">#CMD-1023</span></td>
        <td>Jean Rakoto</td>
        <td>23/04/2026</td>
        <td><span class="order-amount">150 000 Ar</span></td>
        <td><span class="badge delivered">Livrée</span></td>
      </tr>
      <tr>
        <td><span class="order-id">#CMD-1024</span></td>
        <td>Marie Rabe</td>
        <td>23/04/2026</td>
        <td><span class="order-amount">85 000 Ar</span></td>
        <td><span class="badge processing">En cours</span></td>
      </tr>
      <tr>
        <td><span class="order-id">#CMD-1025</span></td>
        <td>Paul Randria</td>
        <td>22/04/2026</td>
        <td><span class="order-amount">220 000 Ar</span></td>
        <td><span class="badge pending">En attente</span></td>
      </tr>
      <tr>
        <td><span class="order-id">#CMD-1026</span></td>
        <td>Sara Ratsama</td>
        <td>22/04/2026</td>
        <td><span class="order-amount">45 000 Ar</span></td>
        <td><span class="badge cancelled">Annulée</span></td>
      </tr>
      <tr>
        <td><span class="order-id">#CMD-1027</span></td>
        <td>Michel Raja</td>
        <td>21/04/2026</td>
        <td><span class="order-amount">310 000 Ar</span></td>
        <td><span class="badge delivered">Livrée</span></td>
      </tr>
    </tbody>
  </table>
</div>

</div>

<script>
Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = 'rgba(0,255,249,0.15)';

const ctxVentes = document.getElementById('chartVentes').getContext('2d');
new Chart(ctxVentes, {
  type:'line',
  data:{
    labels:['Jan','Fév','Mar','Avr','Mai','Juin'],
    datasets:[{
      label:'Ventes (Ar)',
      data:[12000000,15000000,18500000,14000000,21000000,25000000],
      borderColor:'#00fff9',
      backgroundColor:'rgba(0,255,249,0.1)',
      fill:true,
      tension:0.4,
      pointBackgroundColor:'#00fff9',
      pointBorderColor:'#0f172a',
      pointBorderWidth:2,
      pointRadius:6,
      pointHoverRadius:10
    }]
  },
  options:{
    responsive:true,
    maintainAspectRatio:false,
    plugins:{legend:{display:false}},
    scales:{
      y:{grid:{color:'rgba(0,255,249,0.1)'},ticks:{callback:value=>value/1000000+'M'}},
      x:{grid:{color:'rgba(0,255,249,0.1)'}}
    }
  }
});

const ctxProduits = document.getElementById('chartProduits').getContext('2d');
new Chart(ctxProduits, {
  type:'bar',
  data:{
    labels:['iPhone 15','MacBook','AirPods','Samsung S24'],
    datasets:[{
      label:'Ventes',
      data:[85,62,45,38],
      backgroundColor:['#00fff9','#ff006e','#c800ff','#ff6b00'],
      borderRadius:10,
      borderSkipped:false
    }]
  },
  options:{
    responsive:true,
    maintainAspectRatio:false,
    plugins:{legend:{display:false}},
    scales:{
      y:{grid:{color:'rgba(0,255,249,0.1)'}},
      x:{grid:{display:false}}
    }
  }
});

const ctxStatus = document.getElementById('chartStatus').getContext('2d');
new Chart(ctxStatus, {
  type:'doughnut',
  data:{
    labels:['Livrées','En cours','En attente','Annulées'],
    datasets:[{
      data:[145,35,52,15],
      backgroundColor:['#00ff9d','#ff6b00','#00fff9','#ff006e'],
      borderWidth:0,
      hoverOffset:10
    }]
  },
  options:{
    responsive:true,
    maintainAspectRatio:false,
    plugins:{legend:{position:'right',labels:{padding:20,usePointStyle:true,pointStyle:'circle'}}},
    cutout:'70%'
  }
});
</script>

</body>
</html>