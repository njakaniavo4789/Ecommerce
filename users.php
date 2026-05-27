<?php
require "connectionBD.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'ajouter') {
            $sql = "INSERT INTO users (username, mot_de_passe, nom, email, role) VALUES (:username, :motdepasse, :nom, :email, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $_POST['username'],
                ':motdepasse' => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                ':nom' => $_POST['nom'],
                ':email' => $_POST['email'],
                ':role' => $_POST['role']
            ]);
            $message = "Utilisateur ajouté avec succès";
        } elseif ($_POST['action'] === 'supprimer') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = "Utilisateur supprimé";
        } elseif ($_POST['action'] === 'modifier') {
            $sql = "UPDATE users SET username=?, nom=?, email=?, role=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['username'], $_POST['nom'], $_POST['email'], $_POST['role'], $_POST['id']]);
            $message = "Utilisateur modifié";
        }
    }
}
$users = $pdo->query("SELECT * FROM users ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des utilisateurs</title>
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
.container{position:relative;z-index:1;max-width:1200px;margin:auto;padding:30px;}
.page-header{text-align:center;margin-bottom:40px;padding:30px 0;}
.page-title{font-family:'Orbitron',monospace;font-size:clamp(1.8rem,4vw,2.8rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 0 40px rgba(0,255,249,0.15);}
.glow-line{height:4px;background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));border-radius:2px;margin-bottom:30px;animation:glow 2.5s linear infinite;background-size:200%;box-shadow:0 0 25px rgba(0,255,249,0.25);}
@keyframes glow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
@keyframes fadeUp{from{opacity:0;transform:translateY(25px)}to{opacity:1;transform:translateY(0)}}
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:20px;padding:28px;margin-bottom:25px;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);transition:all .4s ease;animation:fadeUp .6s ease both;}
.card:hover{border-color:rgba(0,255,249,0.25);box-shadow:0 8px 40px rgba(0,255,249,0.05);}
.card h2{font-family:'Orbitron',monospace;font-size:1.15rem;margin-bottom:22px;color:var(--neon-cyan);display:flex;align-items:center;gap:10px;}
.form{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form label{display:block;font-weight:500;margin-bottom:6px;color:var(--muted);font-size:.85rem;}
.form input,.form select{width:100%;padding:13px 16px;border-radius:14px;border:1px solid var(--border);background:rgba(15,23,42,0.5);color:white;font-size:.95rem;transition:all .3s;}
.form input:focus,.form select:focus{outline:none;border-color:var(--neon-cyan);box-shadow:0 0 20px rgba(0,255,249,0.12);}
.form-actions{grid-column:1/3;display:flex;gap:15px;margin-top:8px;}
.btn{padding:13px 26px;border-radius:14px;border:none;cursor:pointer;font-weight:700;transition:all .3s;font-size:.9rem;display:inline-flex;align-items:center;gap:8px;}
.btn-primary{background:linear-gradient(135deg,rgba(0,255,249,0.2),rgba(99,102,241,0.15));color:var(--neon-cyan);border:1px solid rgba(0,255,249,0.2);}
.btn-primary:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(0,255,249,0.15);}
.btn-s{padding:9px 16px;border-radius:12px;border:none;cursor:pointer;font-weight:600;font-size:.78rem;transition:all .3s;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
.btn-danger{background:linear-gradient(135deg,rgba(255,0,110,0.15),rgba(153,0,102,0.1));color:var(--neon-pink);border:1px solid rgba(255,0,110,0.2);}
.btn-danger:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(255,0,110,0.15);}
.table-wrap{overflow-x:auto;border-radius:16px;}
table{width:100%;border-collapse:separate;border-spacing:0;margin-top:5px;min-width:650px;}
th{padding:14px 16px;text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:2px;font-weight:600;border-bottom:1px solid var(--border);background:rgba(0,255,249,0.03);}
td{padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.03);transition:background .25s;}
tr:hover td{background:rgba(0,255,249,0.04);}
tr:last-child td{border-bottom:none;}
.badge{display:inline-block;padding:4px 12px;border-radius:30px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.badge.admin{background:rgba(255,0,110,0.12);color:var(--neon-pink);box-shadow:0 0 12px rgba(255,0,110,0.08);}
.badge.manager{background:rgba(234,179,8,0.12);color:#eab308;box-shadow:0 0 12px rgba(234,179,8,0.08);}
.badge.commercial{background:rgba(0,255,157,0.12);color:var(--success);box-shadow:0 0 12px rgba(0,255,157,0.08);}
.msg{padding:14px 20px;border-radius:14px;margin-bottom:20px;font-weight:600;background:rgba(0,255,157,0.1);color:var(--success);border:1px solid rgba(0,255,157,0.15);display:flex;align-items:center;gap:10px;animation:fadeUp .4s ease;}
@media(max-width:768px){.form{grid-template-columns:1fr}.form-actions{grid-column:1}}
</style>
</head>
<body>
<canvas id="particles"></canvas>
<?php include 'navbar.php'; ?>
<div class="container">
<div class="glow-line"></div>
<div class="page-header">
  <h1 class="page-title">👥 GESTION DES UTILISATEURS</h1>
</div>
<?php if ($message): ?>
<div class="msg"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post">
<div class="card" style="animation-delay:.05s">
  <h2><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h2>
  <input type="hidden" name="action" value="ajouter">
  <div class="form">
    <div><label>Username</label><input type="text" name="username" required placeholder="Ex: admin"></div>
    <div><label>Nom complet</label><input type="text" name="nom" required placeholder="Ex: Jean Rakoto"></div>
    <div><label>Email</label><input type="email" name="email" required placeholder="Ex: jean@exemple.mg"></div>
    <div><label>Mot de passe</label><input type="password" name="mot_de_passe" required placeholder="••••••••"></div>
    <div><label>Rôle</label>
    <select name="role">
      <option value="admin">Admin</option>
      <option value="manager">Manager</option>
      <option value="commercial">Commercial</option>
    </select>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> Ajouter</button></div>
  </div>
</div>
</form>

<div class="card" style="animation-delay:.1s">
  <h2><i class="fas fa-users"></i> Liste des utilisateurs</h2>
  <div class="table-wrap">
  <table>
    <tr><th>Username</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
      <td><?= htmlspecialchars($u['nom']) ?></td>
      <td style="font-size:.82rem;color:var(--muted)"><?= htmlspecialchars($u['email']) ?></td>
      <td><span class="badge <?= $u['role'] ?>"><?= htmlspecialchars($u['role']) ?></span></td>
      <td><?= htmlspecialchars($u['statut']) ?></td>
      <td>
        <?php if ($u['username'] != 'admin'): ?>
        <form method="post" style="display:inline;">
          <input type="hidden" name="action" value="supprimer">
          <input type="hidden" name="id" value="<?= $u['id'] ?>">
          <button type="submit" class="btn-s btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>
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