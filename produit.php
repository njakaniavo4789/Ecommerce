<?php
/* ===============================
   DEBUG PHP (ANTI PAGE BLANCHE)
================================ */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ===============================
   FONCTIONS UTILITAIRES
================================ */
function parse_size($size) {
    $unit = strtoupper(substr($size, -1));
    $value = (int)$size;
    switch ($unit) {
        case 'G': return $value * 1024 * 1024 * 1024;
        case 'M': return $value * 1024 * 1024;
        case 'K': return $value * 1024;
        default: return $value;
    }
}

/* ===============================
   INCLUDES
================================ */
include "navbar.php";
require_once "connectionBD.php";

/* ===============================
   CONFIG PDO
================================ */
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* ===============================
   VARIABLES
================================ */
$message = "";
$debug = "";

/* ===============================
   LIMITE POST
================================ */
$contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
$postMaxSize = ini_get('post_max_size');
$postMaxBytes = parse_size($postMaxSize);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $contentLength > $postMaxBytes) {
    $message = "❌ Données trop volumineuses";
}

/* ===============================
   AJOUT PRODUIT
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($message)) {

    if (
        !empty($_POST['nom']) &&
        !empty($_POST['description']) &&
        !empty($_POST['prix']) &&
        isset($_FILES['image'])
    ) {

        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tmpName = $_FILES['image']['tmp_name'];
            $mime = mime_content_type($tmpName);

            if (!in_array($mime, $allowedTypes)) {
                $message = "❌ Format image non autorisé";
            } else {

                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $message = "❌ Image trop lourde (max 5MB)";
                } else {

                    $uploadDir = __DIR__ . "/upload/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $imageName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
                    $fileFullPath = $uploadDir . $imageName;
                    $path = "upload/" . $imageName;

                    if (move_uploaded_file($tmpName, $fileFullPath)) {

                        $sql = "INSERT INTO produit (nom, description, prix, stock, image)
                                VALUES (:nom, :description, :prix, :stock, :image)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':nom' => htmlspecialchars($_POST['nom']),
                            ':description' => htmlspecialchars($_POST['description']),
                            ':prix' => floatval($_POST['prix']),
                            ':stock' => intval($_POST['stock']),
                            ':image' => $path
                        ]);

                        header("Location: produit.php?success=1");
                        exit;

                    } else {
                        $message = "❌ Impossible de déplacer l’image";
                    }
                }
            }
        } else {
            $message = "❌ Erreur upload image";
        }
    } else {
        $message = "❌ Tous les champs sont obligatoires";
    }
}

/* ===============================
   MESSAGE SUCCÈS
================================ */
if (isset($_GET['success'])) {
    $message = "✅ Produit ajouté avec succès";
}

/* ===============================
   LISTE PRODUITS
================================ */
$produits = $pdo->query("SELECT * FROM produit ORDER BY id DESC")->fetchAll();

/* ===============================
   CONFIG PHP
================================ */
$uploadMaxSize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion Produits</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

*{margin:0;padding:0;box-sizing:border-box}
body{
  font-family:'Inter',sans-serif;
  background:#030712;
  color:#f1f5f9;
  min-height:100vh;
  overflow-x:hidden;
}

/* Animated Background */
body::before{
  content:'';
  position:fixed;
  top:0;left:0;right:0;bottom:0;
  background:
    radial-gradient(ellipse 80% 50% at 50% -20%, rgba(120,119,198,0.15), transparent),
    radial-gradient(ellipse 60% 40% at 100% 100%, rgba(56,189,248,0.1), transparent);
  pointer-events:none;
  z-index:-1;
}

.main{max-width:1500px;margin:auto;padding:40px 24px}

/* Header Section */
.page-header{
  text-align:center;
  margin-bottom:50px;
}
.page-header h1{
  font-size:2.5rem;
  font-weight:800;
  background:linear-gradient(135deg,#38bdf8,#818cf8,#c084fc);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  margin-bottom:12px;
}
.page-header p{
  color:#64748b;
  font-size:1.1rem;
}

/* Navigation Tabs */
.sub-nav{
  display:flex;
  justify-content:center;
  gap:8px;
  margin-bottom:40px;
  background:rgba(15,23,42,0.6);
  backdrop-filter:blur(20px);
  padding:8px;
  border-radius:16px;
  width:fit-content;
  margin-left:auto;
  margin-right:auto;
  border:1px solid rgba(148,163,184,0.1);
}
.sub-nav button{
  padding:14px 32px;
  border-radius:12px;
  border:none;
  background:transparent;
  color:#94a3b8;
  cursor:pointer;
  font-weight:600;
  font-size:0.95rem;
  transition:all 0.3s ease;
  display:flex;
  align-items:center;
  gap:8px;
}
.sub-nav button:hover{
  color:#f1f5f9;
  background:rgba(56,189,248,0.1);
}
.sub-nav button.active{
  background:linear-gradient(135deg,#38bdf8,#818cf8);
  color:#020617;
  box-shadow:0 8px 32px rgba(56,189,248,0.3);
}
.sub-nav button svg{width:20px;height:20px;}

/* Form Styles */
form{
  max-width:580px;
  margin:0 auto;
  background:linear-gradient(145deg,rgba(15,23,42,0.8),rgba(30,41,59,0.4));
  backdrop-filter:blur(20px);
  padding:40px;
  border-radius:24px;
  border:1px solid rgba(148,163,184,0.1);
  box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);
}
form h2{
  text-align:center;
  font-size:1.75rem;
  font-weight:700;
  margin-bottom:8px;
  color:#f1f5f9;
}
form .form-subtitle{
  text-align:center;
  color:#64748b;
  margin-bottom:30px;
  font-size:0.95rem;
}
.info-box{
  background:rgba(56,189,248,0.08);
  border:1px solid rgba(56,189,248,0.2);
  padding:16px;
  border-radius:14px;
  margin-bottom:24px;
  font-size:0.85rem;
  color:#94a3b8;
  line-height:1.6;
}
.form-group{
  margin-bottom:20px;
}
.form-group label{
  display:block;
  font-weight:500;
  margin-bottom:8px;
  color:#cbd5e1;
  font-size:0.9rem;
}
input,textarea,select{
  width:100%;
  padding:14px 18px;
  border-radius:14px;
  border:2px solid rgba(148,163,184,0.15);
  background:rgba(15,23,42,0.6);
  color:white;
  font-size:1rem;
  transition:all 0.3s ease;
}
input:focus,textarea:focus{
  outline:none;
  border-color:#38bdf8;
  box-shadow:0 0 0 4px rgba(56,189,248,0.15);
}
input::placeholder,textarea::placeholder{color:#475569}
textarea{min-height:120px;resize:vertical}

/* File Input */
.file-input-wrapper{
  position:relative;
  border:2px dashed rgba(148,163,184,0.3);
  border-radius:14px;
  padding:30px;
  text-align:center;
  transition:all 0.3s ease;
  cursor:pointer;
  background:rgba(15,23,42,0.4);
}
.file-input-wrapper:hover{
  border-color:#38bdf8;
  background:rgba(56,189,248,0.05);
}
.file-input-wrapper input[type="file"]{
  position:absolute;
  inset:0;
  opacity:0;
  cursor:pointer;
}
.file-input-wrapper .upload-icon{
  width:48px;height:48px;
  margin:0 auto 12px;
  color:#64748b;
}
.file-input-wrapper p{color:#64748b;font-size:0.9rem;}
.file-input-wrapper span{color:#38bdf8;font-weight:600;}

button.submit{
  width:100%;
  padding:16px;
  background:linear-gradient(135deg,#38bdf8,#818cf8);
  border:none;
  border-radius:14px;
  font-weight:700;
  font-size:1rem;
  color:#020617;
  cursor:pointer;
  transition:all 0.3s ease;
  margin-top:10px;
}
button.submit:hover{
  transform:translateY(-2px);
  box-shadow:0 12px 40px rgba(56,189,248,0.4);
}

/* Products Grid */
.products{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
  gap:28px;
  margin-top:20px;
}

/* Product Card */
.card{
  background:linear-gradient(165deg,rgba(15,23,42,0.9),rgba(30,41,59,0.5));
  backdrop-filter:blur(10px);
  border-radius:24px;
  overflow:hidden;
  position:relative;
  border:1px solid rgba(148,163,184,0.08);
  transition:all 0.4s cubic-bezier(0.4,0,0.2,1);
}
.card:hover{
  transform:translateY(-8px);
  box-shadow:0 30px 60px -15px rgba(0,0,0,0.5);
  border-color:rgba(56,189,248,0.2);
}

/* Image Container */
.card .image-container{
  position:relative;
  overflow:hidden;
  height:220px;
}
.card .image-container img{
  width:100%;
  height:100%;
  object-fit:cover;
  transition:transform 0.5s ease;
}
.card:hover .image-container img{
  transform:scale(1.08);
}
.card .image-overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(to top,rgba(3,7,18,0.9) 0%,transparent 60%);
}

/* Promotion Badge - Modern & Eye-catching */
.promo-badge{
  position:absolute;
  top:16px;
  left:16px;
  background:linear-gradient(135deg,#ef4444 0%,#f97316 50%,#fbbf24 100%);
  background-size:200% 200%;
  animation:gradientShift 3s ease infinite, float 2s ease-in-out infinite;
  color:white;
  padding:12px 20px;
  border-radius:14px;
  font-size:1rem;
  font-weight:800;
  letter-spacing:0.5px;
  box-shadow:0 10px 30px rgba(239,68,68,0.5), 0 0 20px rgba(249,115,22,0.3);
  z-index:10;
  display:flex;
  align-items:center;
  gap:8px;
  text-transform:uppercase;
}
.promo-badge::before{
  content:'';
  width:10px;height:10px;
  background:white;
  border-radius:50%;
  animation:pulse-dot 1.5s infinite;
  box-shadow:0 0 10px rgba(255,255,255,0.8);
}
.promo-badge::after{
  content:'SOLDE';
  font-size:0.65rem;
  background:rgba(0,0,0,0.3);
  padding:2px 6px;
  border-radius:4px;
  margin-left:4px;
}
@keyframes gradientShift{
  0%,100%{background-position:0% 50%}
  50%{background-position:100% 50%}
}
@keyframes float{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-3px)}
}
@keyframes pulse-dot{
  0%,100%{opacity:1;transform:scale(1)}
  50%{opacity:0.5;transform:scale(1.3)}
}

/* Card with Promo - Special Effects */
.card.has-promo{
  border:2px solid transparent;
  background-image:linear-gradient(165deg,rgba(15,23,42,0.95),rgba(30,41,59,0.6)),
                   linear-gradient(135deg,#ef4444,#f97316,#fbbf24);
  background-origin:border-box;
  background-clip:padding-box, border-box;
}
.card.has-promo::before{
  content:'';
  position:absolute;
  inset:-2px;
  background:linear-gradient(135deg,#ef4444,#f97316,#fbbf24);
  border-radius:26px;
  z-index:-1;
  opacity:0.5;
  filter:blur(15px);
  animation:glow 3s ease-in-out infinite;
}
@keyframes glow{
  0%,100%{opacity:0.3}
  50%{opacity:0.6}
}

/* Card Content */
.card-content{
  padding:24px;
  position:relative;
}
.card h3{
  font-size:1.25rem;
  font-weight:700;
  color:#f1f5f9;
  margin-bottom:10px;
  line-height:1.4;
}
.card .description{
  color:#64748b;
  font-size:0.9rem;
  line-height:1.6;
  margin-bottom:16px;
}

/* Price Section */
.price-section{
  display:flex;
  align-items:center;
  gap:14px;
  margin-bottom:14px;
  flex-wrap:wrap;
  padding:16px;
  background:rgba(15,23,42,0.6);
  border-radius:14px;
  border:1px solid rgba(148,163,184,0.1);
}
.price-section.has-discount{
  background:linear-gradient(135deg,rgba(34,197,94,0.08),rgba(16,185,129,0.05));
  border-color:rgba(34,197,94,0.2);
}
.old-price{
  text-decoration:line-through;
  color:#64748b;
  font-size:1rem;
  position:relative;
}
.old-price::after{
  content:'';
  position:absolute;
  left:0;right:0;top:50%;
  height:2px;
  background:#ef4444;
  transform:rotate(-5deg);
}
.new-price{
  font-size:1.75rem;
  font-weight:800;
  background:linear-gradient(135deg,#22c55e,#10b981,#059669);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
  animation:priceGlow 2s ease-in-out infinite;
}
@keyframes priceGlow{
  0%,100%{filter:drop-shadow(0 0 0 transparent)}
  50%{filter:drop-shadow(0 0 8px rgba(34,197,94,0.5))}
}
.regular-price{
  font-size:1.35rem;
  font-weight:700;
  color:#38bdf8;
}
.currency{
  font-size:0.85rem;
  font-weight:600;
  color:#94a3b8;
  margin-left:4px;
}

/* Savings Tag */
.savings-tag{
  background:linear-gradient(135deg,#f97316,#ef4444);
  color:white;
  padding:6px 12px;
  border-radius:8px;
  font-size:0.8rem;
  font-weight:700;
  animation:bounce 2s infinite;
  box-shadow:0 4px 15px rgba(249,115,22,0.3);
}
@keyframes bounce{
  0%,100%{transform:scale(1)}
  50%{transform:scale(1.05)}
}

/* Promo Tag */
.promo-tag{
  display:inline-flex;
  align-items:center;
  gap:8px;
  background:linear-gradient(135deg,rgba(34,197,94,0.2),rgba(16,185,129,0.1));
  border:1px solid rgba(34,197,94,0.4);
  color:#22c55e;
  padding:10px 16px;
  border-radius:12px;
  font-size:0.85rem;
  font-weight:700;
  margin-bottom:16px;
  position:relative;
  overflow:hidden;
}
.promo-tag::before{
  content:'';
  position:absolute;
  top:0;left:-100%;
  width:100%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,0.2),transparent);
  animation:shimmer 2s infinite;
}
@keyframes shimmer{
  0%{left:-100%}
  100%{left:100%}
}
.promo-tag svg{width:16px;height:16px;}

/* Stock Badge */
.stock-badge{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:8px 14px;
  border-radius:10px;
  font-size:0.85rem;
  font-weight:600;
  margin-bottom:20px;
}
.stock-badge.in-stock{
  background:rgba(34,197,94,0.1);
  color:#22c55e;
}
.stock-badge.low-stock{
  background:rgba(249,115,22,0.1);
  color:#f97316;
}
.stock-badge.out-of-stock{
  background:rgba(239,68,68,0.1);
  color:#ef4444;
}

/* Actions */
.actions{
  display:flex;
  gap:12px;
}
.actions a{
  flex:1;
  text-align:center;
  padding:12px 16px;
  border-radius:12px;
  font-weight:600;
  font-size:0.9rem;
  text-decoration:none;
  transition:all 0.3s ease;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:6px;
}
.actions a svg{width:18px;height:18px;}
.edit{
  background:rgba(56,189,248,0.15);
  color:#38bdf8;
  border:1px solid rgba(56,189,248,0.3);
}
.edit:hover{
  background:#38bdf8;
  color:#020617;
}
.delete{
  background:rgba(239,68,68,0.1);
  color:#ef4444;
  border:1px solid rgba(239,68,68,0.2);
}
.delete:hover{
  background:#ef4444;
  color:white;
}

/* Messages */
.message{
  text-align:center;
  margin-bottom:24px;
  padding:16px 24px;
  border-radius:14px;
  font-weight:600;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  max-width:600px;
  margin-left:auto;
  margin-right:auto;
}
.success{
  background:rgba(34,197,94,0.15);
  border:1px solid rgba(34,197,94,0.3);
  color:#22c55e;
}
.error{
  background:rgba(239,68,68,0.15);
  border:1px solid rgba(239,68,68,0.3);
  color:#ef4444;
}

/* Debug Box */
.debug{
  background:rgba(30,41,59,0.8);
  border:1px solid rgba(148,163,184,0.2);
  padding:20px;
  border-radius:14px;
  margin-bottom:24px;
  font-size:0.85rem;
  line-height:1.8;
  max-width:800px;
  margin-left:auto;
  margin-right:auto;
}

/* Empty State */
.empty-state{
  text-align:center;
  padding:80px 40px;
  color:#64748b;
}
.empty-state svg{
  width:80px;height:80px;
  margin-bottom:20px;
  opacity:0.5;
}
.empty-state h3{
  font-size:1.5rem;
  color:#94a3b8;
  margin-bottom:10px;
}

/* Promo Ribbon */
.promo-ribbon{
  position:absolute;
  top:20px;
  right:-35px;
  background:linear-gradient(135deg,#ef4444,#dc2626);
  color:white;
  padding:8px 40px;
  font-size:0.7rem;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:1px;
  transform:rotate(45deg);
  box-shadow:0 4px 15px rgba(239,68,68,0.4);
  z-index:15;
}

/* Price Compare Layout */
.price-compare,.price-new{
  display:flex;
  flex-direction:column;
  gap:4px;
}
.price-label{
  font-size:0.7rem;
  text-transform:uppercase;
  letter-spacing:1px;
  color:#64748b;
  font-weight:600;
}
.price-arrow{
  display:flex;
  align-items:center;
  animation:arrowPulse 1.5s infinite;
}
@keyframes arrowPulse{
  0%,100%{transform:translateX(0)}
  50%{transform:translateX(5px)}
}

/* Promo Stats Banner */
.promo-stats{
  display:flex;
  gap:16px;
  padding:12px 16px;
  background:linear-gradient(135deg,rgba(239,68,68,0.1),rgba(249,115,22,0.05));
  border-radius:12px;
  margin-bottom:16px;
  border:1px solid rgba(239,68,68,0.2);
}
.promo-stat{
  display:flex;
  flex-direction:column;
  gap:2px;
}
.promo-stat-value{
  font-size:1.1rem;
  font-weight:800;
  color:#ef4444;
}
.promo-stat-label{
  font-size:0.7rem;
  color:#94a3b8;
  text-transform:uppercase;
}
</style>
</head>

<body>
<div class="main">

<!-- Page Header -->
<div class="page-header">
  <h1>Gestion des Produits</h1>
  <p>Gerez votre catalogue et vos promotions en toute simplicite</p>
</div>

<?php if($message): ?>
<div class="message <?php echo strpos($message,'ok')!==false || strpos($message,'succes')!==false || strpos($message,'OK')!==false ? 'success' : 'error'; ?>">
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <?php if(strpos($message,'ok')!==false || strpos($message,'succes')!==false || strpos($message,'OK')!==false): ?>
    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    <?php else: ?>
    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
    <?php endif; ?>
  </svg>
  <?= $message ?>
</div>
<?php endif; ?>

<?php if($debug): ?>
<div class="debug">
<strong>Informations de diagnostic:</strong><br>
<?= $debug ?>
</div>
<?php endif; ?>

<!-- Navigation Tabs -->
<div class="sub-nav">
  <button class="active" onclick="showForm()">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    Ajouter
  </button>
  <button onclick="showList()">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
      <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
    </svg>
    Catalogue (<?= count($produits) ?>)
  </button>
</div>

<!-- Add Product Form -->
<form id="form" method="post" enctype="multipart/form-data">
  <h2>Nouveau Produit</h2>
  <p class="form-subtitle">Ajoutez un nouveau produit a votre catalogue</p>
  
  <div class="info-box">
    <strong>Configuration serveur:</strong><br>
    Upload max: <strong><?= $uploadMaxSize ?></strong> | POST max: <strong><?= $postMaxSize ?></strong><br>
    Formats acceptes: JPG, PNG, GIF, WEBP
  </div>

  <div class="form-group">
    <label>Nom du produit</label>
    <input type="text" name="nom" placeholder="Ex: iPhone 15 Pro Max" required>
  </div>
  
  <div class="form-group">
    <label>Description</label>
    <textarea name="description" placeholder="Decrivez votre produit en detail..." required></textarea>
  </div>
  
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="form-group">
      <label>Prix (Ar)</label>
      <input type="number" name="prix" step="0.01" min="0" placeholder="0.00" required>
    </div>
    <div class="form-group">
      <label>Stock disponible</label>
      <input type="number" name="stock" min="0" placeholder="0" required>
    </div>
  </div>
  
  <div class="form-group">
    <label>Image du produit</label>
    <div class="file-input-wrapper">
      <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
      </svg>
      <p><span>Cliquez pour telecharger</span> ou glissez-deposez</p>
      <p style="font-size:0.8rem;margin-top:8px;">PNG, JPG, GIF, WEBP (max 5MB)</p>
      <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
    </div>
  </div>
  
  <button class="submit" type="submit">
    Ajouter le produit
  </button>
</form>

<!-- Products Grid -->
<div class="products" id="list" style="display:none">
<?php if(empty($produits)): ?>
  <div class="empty-state" style="grid-column:1/-1;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
      <polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>
    </svg>
    <h3>Aucun produit</h3>
    <p>Commencez par ajouter votre premier produit</p>
  </div>
<?php else: ?>
  <?php foreach($produits as $p): 
    $hasPromo = !empty($p['promo_reduction']);
    $prixOriginal = $p['prix'];
    $prixReduit = $hasPromo ? $prixOriginal * (1 - $p['promo_reduction'] / 100) : $prixOriginal;
    $economie = $hasPromo ? $prixOriginal - $prixReduit : 0;
    $stockStatus = $p['stock'] > 10 ? 'in-stock' : ($p['stock'] > 0 ? 'low-stock' : 'out-of-stock');
    $stockLabel = $p['stock'] > 10 ? 'En stock' : ($p['stock'] > 0 ? 'Stock faible' : 'Rupture');
  ?>
  <div class="card <?= $hasPromo ? 'has-promo' : '' ?>">
    <!-- Image Container -->
    <div class="image-container">
      <?php if ($hasPromo): ?>
        <span class="promo-badge">
          -<?= $p['promo_reduction'] ?>%
        </span>
        <!-- Promo Ribbon -->
        <div class="promo-ribbon">OFFRE SPECIALE</div>
      <?php endif; ?>
      <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%234b5563%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2224%22 fill=%22%2394a3b8%22 text-anchor=%22middle%22 dy=%22.3em%22%3EImage non disponible%3C/text%3E%3C/svg%3E'">
      <div class="image-overlay"></div>
    </div>
    
    <!-- Card Content -->
    <div class="card-content">
      <h3><?= htmlspecialchars($p['nom']) ?></h3>
      <p class="description"><?= htmlspecialchars(substr($p['description'],0,100)) ?>...</p>
      
      <!-- Price Section -->
      <div class="price-section <?= $hasPromo ? 'has-discount' : '' ?>">
        <?php if ($hasPromo): ?>
          <div class="price-compare">
            <span class="price-label">Avant</span>
            <span class="old-price"><?= number_format($prixOriginal,0,',',' ') ?> Ar</span>
          </div>
          <div class="price-arrow">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
              <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
          </div>
          <div class="price-new">
            <span class="price-label">Maintenant</span>
            <span class="new-price"><?= number_format($prixReduit,0,',',' ') ?><span class="currency">Ar</span></span>
          </div>
          <span class="savings-tag">Economie: <?= number_format($economie,0,',',' ') ?> Ar</span>
        <?php else: ?>
          <span class="regular-price"><?= number_format($p['prix'],0,',',' ') ?><span class="currency">Ar</span></span>
        <?php endif; ?>
      </div>
      
      <?php if ($hasPromo): ?>
      <div class="promo-tag">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/>
          <line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
          <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
        </svg>
        <?= htmlspecialchars($p['promo_nom']) ?>
      </div>
      <?php endif; ?>
      
      <!-- Stock Badge -->
      <div class="stock-badge <?= $stockStatus ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
        </svg>
        <?= $stockLabel ?> (<?= $p['stock'] ?>)
      </div>
      
      <!-- Actions -->
      <div class="actions">
        <a class="edit" href="modifier_produit.php?id=<?= $p['id'] ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Modifier
        </a>
        <a class="delete" href="supprimer_produit.php?id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
          </svg>
          Supprimer
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>

</div>

<script>
const form = document.getElementById('form');
const list = document.getElementById('list');
const buttons = document.querySelectorAll('.sub-nav button');

function showForm(){
  form.style.display='block';
  list.style.display='none';
  buttons[0].classList.add('active');
  buttons[1].classList.remove('active');
}

function showList(){
  form.style.display='none';
  list.style.display='grid';
  buttons[1].classList.add('active');
  buttons[0].classList.remove('active');
}

// Highlight file input on drag
const fileWrapper = document.querySelector('.file-input-wrapper');
if(fileWrapper){
  ['dragenter', 'dragover'].forEach(e => {
    fileWrapper.addEventListener(e, () => fileWrapper.style.borderColor = '#38bdf8');
  });
  ['dragleave', 'drop'].forEach(e => {
    fileWrapper.addEventListener(e, () => fileWrapper.style.borderColor = 'rgba(148,163,184,0.3)');
  });
}
</script>
</body>
</html>
