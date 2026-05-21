<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Navbar</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  min-height:100vh;
  background: linear-gradient(135deg,#030712,#0f172a,#1e1b4b);
  background-size:400% 400%;
  animation:gradientBG 15s ease infinite;
  padding-top:90px;
  padding-bottom:50px;
}

@keyframes gradientBG {
  0%{background-position:0% 50%}
  50%{background-position:100% 50%}
  100%{background-position:0% 50%}
}

nav{
  position:fixed;
  top:15px;
  left:15px;
  right:15px;
  height:70px;
  background:rgba(17,24,39,0.9);
  backdrop-filter:blur(20px);
  -webkit-backdrop-filter:blur(20px);
  border:1px solid rgba(255,255,255,0.1);
  border-radius:20px;
  box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:0 25px;
  z-index:1000;
}

.logo{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:1.4rem;
  font-weight:700;
  color:#22d3ee;
  white-space:nowrap;
}

.logo i{
  font-size:1.5rem;
  color:#22d3ee;
}

.menu{
  display:flex;
  align-items:center;
  gap:5px;
  flex-wrap:wrap;
  justify-content:center;
}

.menu a{
  display:flex;
  align-items:center;
  gap:8px;
  padding:10px 14px;
  border-radius:10px;
  text-decoration:none;
  color:#9ca3af;
  font-size:0.85rem;
  font-weight:500;
  white-space:nowrap;
  transition:all 0.3s ease;
}

.menu a:hover{
  background:rgba(34,211,238,0.15);
  color:#22d3ee;
}

.menu a.active{
  background:linear-gradient(135deg,#22d3ee,#818cf8);
  color:#0f172a;
  font-weight:600;
}

.menu a i{
  font-size:0.9rem;
}

.right-section{
  display:flex;
  align-items:center;
  gap:12px;
  flex-shrink:0;
}

.notification{
  position:relative;
  width:40px;
  height:40px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:rgba(31,41,55,0.6);
  border-radius:10px;
  color:#9ca3af;
  cursor:pointer;
  transition:all 0.3s ease;
}

.notification:hover{
  background:rgba(34,211,238,0.15);
  color:#22d3ee;
}

.notification .dot{
  position:absolute;
  top:8px;
  right:8px;
  width:8px;
  height:8px;
  background:#ef4444;
  border-radius:50%;
  animation:pulse 2s infinite;
}

@keyframes pulse{
  0%,100%{transform:scale(1);}
  50%{transform:scale(1.3);}
}

.user-area{
  display:flex;
  align-items:center;
  gap:10px;
  padding:6px 12px 6px 6px;
  background:rgba(31,41,55,0.6);
  border-radius:12px;
}

.avatar{
  width:32px;
  height:32px;
  background:linear-gradient(135deg,#22d3ee,#a78bfa);
  border-radius:8px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:600;
  color:#0f172a;
  font-size:0.8rem;
}

.user-details{
  display:flex;
  flex-direction:column;
  line-height:1.2;
}

.name{
  font-size:0.8rem;
  font-weight:600;
  color:#fff;
}

.role{
  font-size:0.65rem;
  color:#9ca3af;
}

.btn-logout{
  display:flex;
  align-items:center;
  gap:6px;
  padding:10px 14px;
  background:linear-gradient(135deg,#ef4444,#b91c1c);
  border-radius:10px;
  color:#fff;
  font-weight:600;
  font-size:0.8rem;
  text-decoration:none;
  transition:all 0.3s ease;
}

.btn-logout:hover{
  transform:translateY(-2px);
  box-shadow:0 8px 15px -3px rgba(239,68,68,0.4);
}

.btn-logout i{
  font-size:0.85rem;
}

@media (max-width:900px){
  .menu a span{
    display:none;
  }
  .menu a{
    padding:10px 12px;
  }
  .user-details{
    display:none;
  }
  .btn-logout span{
    display:none;
  }
}

@media (max-width:600px){
  nav{
    flex-wrap:wrap;
    height:auto;
    padding:12px 15px;
    gap:10px;
  }
  .menu{
    order:3;
    width:100%;
    justify-content:center;
    padding-top:10px;
    border-top:1px solid rgba(255,255,255,0.1);
    margin-top:5px;
  }
  .logo{
    flex:1;
  }
  .right-section{
    flex:1;
    justify-content:flex-end;
  }
}
</style>
</head>

<body>

<nav>
  <div class="logo">
    <i class="fas fa-shopping-bag"></i>
    <span>SHOP</span>
  </div>

  <div class="menu">
    <a href="users.php"><i class="fas fa-users-cog"></i><span>Users</span></a>
    <a href="statistique.php"><i class="fas fa-chart-line"></i><span>Stats</span></a>
    <a href="produit.php"><i class="fas fa-box-open"></i><span>Produits</span></a>
    <a href="marketing.php"><i class="fas fa-bullhorn"></i><span>Marketing</span></a>
    <a href="client.php"><i class="fas fa-users"></i><span>Clients</span></a>
    <a href="commande.php"><i class="fas fa-shopping-cart"></i><span>Commandes</span></a>
  </div>

  <div class="right-section">
    <div class="notification">
      <i class="fas fa-bell"></i>
      <div class="dot"></div>
    </div>
    
    <div class="user-area">
      <div class="avatar">A</div>
      <div class="user-details">
        <span class="name">Admin</span>
        <span class="role">Admin</span>
      </div>
    </div>
    
    <a href="logout.php" class="btn-logout">
      <i class="fas fa-sign-out-alt"></i>
      <span>Déconnexion</span>
    </a>
  </div>
</nav>

</body>
</html>