<?php
session_start();
require "connectionBD.php";

$Error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mot_de_passe'])) {

            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: index.php");
            exit;

        } else {
            $Error = "Nom d'utilisateur ou mot de passe incorrect";
        }

    } else {
        $Error = "Veuillez remplir tous les champs";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI,sans-serif;}
body{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:linear-gradient(270deg,#020617,#0f172a,#020617);
  background-size:600% 600%;
  animation:bg 12s ease infinite;
  color:#e5e7eb;
}
@keyframes bg{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.login-box{
  width:360px;
  padding:40px;
  background:rgba(15,23,42,.95);
  backdrop-filter:blur(15px);
  border-radius:20px;
  box-shadow:0 30px 70px rgba(0,0,0,.6);
}
h1{text-align:center;margin-bottom:30px;}
.input-group{position:relative;margin-bottom:25px;}
.input-group input{
  width:100%;
  padding:12px;
  background:transparent;
  border:1px solid #334155;
  border-radius:10px;
  color:white;
  outline:none;
}
.input-group label{
  position:absolute;
  top:50%;
  left:12px;
  transform:translateY(-50%);
  color:#94a3b8;
  pointer-events:none;
  transition:.3s;
}
.input-group input:focus + label,
.input-group input:valid + label{
  top:-8px;
  font-size:12px;
  background:#0f172a;
  padding:0 5px;
  color:#38bdf8;
}
button{
  width:100%;
  padding:12px;
  border:none;
  border-radius:12px;
  background:linear-gradient(135deg,#38bdf8,#2563eb);
  color:white;
  font-weight:bold;
  cursor:pointer;
}
.error{
  background:#7f1d1d;
  color:#fecaca;
  padding:10px;
  border-radius:8px;
  margin-bottom:15px;
  text-align:center;
}
</style>
</head>

<body>

<div class="login-box">
<h1>Connexion</h1>

<?php if($Error): ?>
<div class="error"><?= $Error ?></div>
<?php endif; ?>

<form method="POST" autocomplete="off">

  <div class="input-group">
    <input type="text" name="username" required autocomplete="off">
    <label>Nom d'utilisateur</label>
  </div>

  <div class="input-group">
    <input type="password" name="password" required autocomplete="new-password">
    <label>Mot de passe</label>
  </div>

  <button type="submit">Se connecter</button>

</form>
</div>

</body>
</html>
