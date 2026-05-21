<?php
require "connectionBD.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        if ($_POST['action'] === 'ajouter') {
            $sql = "INSERT INTO users (username, mot_de_passe, nom, email, role) 
                    VALUES (:username, :motdepasse, :nom, :email, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $_POST['username'],
                ':motdepasse' => password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                ':nom' => $_POST['nom'],
                ':email' => $_POST['email'],
                ':role' => $_POST['role']
            ]);
            $message = "Utilisateur ajouté avec succès";
        }
        
        elseif ($_POST['action'] === 'supprimer') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = "Utilisateur supprimé";
        }
        
        elseif ($_POST['action'] === 'modifier') {
            if (!empty($_POST['mot_de_passe'])) {
                $sql = "UPDATE users SET username=?, nom=?, email=?, role=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['username'], $_POST['nom'], $_POST['email'], $_POST['role'], $_POST['id']]);
            } else {
                $sql = "UPDATE users SET username=?, nom=?, email=?, role=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['username'], $_POST['nom'], $_POST['email'], $_POST['role'], $_POST['id']]);
            }
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
.form input,.form select{width:100%;padding:14px 18px;border-radius:14px;border:2px solid var(--border);background:rgba(15,23,42,0.6);color:white;font-size:1rem;transition:all 0.3s ease;}
.form input:focus,.form select:focus{outline:none;border-color:var(--neon-cyan);box-shadow:0 0 20px rgba(0,255,249,0.3);}
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
.badge.admin{background:rgba(255,0,110,0.15);color:var(--neon-pink);}
.badge.manager{background:rgba(234,179,8,.15);color:#eab308;}
.badge.commercial{background:rgba(0,255,157,0.15);color:var(--success);}
.msg{padding:15px;border-radius:14px;margin-bottom:20px;font-weight:600;background:rgba(0,255,157,0.15);color:var(--success);}
@media (max-width:768px){.form{grid-template-columns:1fr}}
</style>
</head>
<body>
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
<div class="card">
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
    <div class="form-actions">
      <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> Ajouter</button>
    </div>
  </div>
</div>
</form>

<div class="card">
  <h2><i class="fas fa-users"></i> Liste des utilisateurs</h2>
  <table>
    <tr>
      <th>Username</th>
      <th>Nom</th>
      <th>Email</th>
      <th>Rôle</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
      <td><?= htmlspecialchars($u['nom']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><span class="badge <?= $u['role'] ?>"><?= htmlspecialchars($u['role']) ?></span></td>
      <td><?= htmlspecialchars($u['statut']) ?></td>
      <td>
        <?php if ($u['username'] != 'admin'): ?>
        <form method="post" style="display:inline;">
          <input type="hidden" name="action" value="supprimer">
          <input type="hidden" name="id" value="<?= $u['id'] ?>">
          <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer?')" style="padding:8px 15px;font-size:0.85rem;"><i class="fas fa-trash"></i></button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

</div>
</body>
</html>