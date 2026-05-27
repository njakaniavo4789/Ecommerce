<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

include "navbar.php";
require_once "connectionBD.php";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$message = "";
$debug = "";

$contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
$postMaxSize = ini_get('post_max_size');
$postMaxBytes = parse_size($postMaxSize);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $contentLength > $postMaxBytes) {
    $message = "Données trop volumineuses";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($message)) {
    if (!empty($_POST['nom']) && !empty($_POST['description']) && !empty($_POST['prix']) && isset($_FILES['image'])) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tmpName = $_FILES['image']['tmp_name'];
            $mime = mime_content_type($tmpName);
            if (!in_array($mime, $allowedTypes)) {
                $message = "Format image non autorisé";
            } else {
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $message = "Image trop lourde (max 5MB)";
                } else {
                    $uploadDir = __DIR__ . "/upload/";
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $imageName = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
                    $fileFullPath = $uploadDir . $imageName;
                    $path = "upload/" . $imageName;
                    if (move_uploaded_file($tmpName, $fileFullPath)) {
                        $sql = "INSERT INTO produit (nom, description, prix, stock, image) VALUES (:nom, :description, :prix, :stock, :image)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':nom' => htmlspecialchars($_POST['nom']),
                            ':description' => htmlspecialchars($_POST['description']),
                            ':prix' => floatval($_POST['prix']),
                            ':stock' => intval($_POST['stock']),
                            ':image' => $path
                        ]);
                        header("Location: produit.php?success=1"); exit;
                    } else {
                        $message = "Impossible de déplacer l'image";
                    }
                }
            }
        } else {
            $message = "Erreur upload image";
        }
    } else {
        $message = "Tous les champs sont obligatoires";
    }
}

if (isset($_GET['success'])) $message = "Produit ajouté avec succès";

$produits = $pdo->query("SELECT * FROM produit ORDER BY id DESC")->fetchAll();
$uploadMaxSize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion Produits</title>
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
.main{position:relative;z-index:1;max-width:1500px;margin:auto;padding:30px 24px;}
.page-header{text-align:center;margin-bottom:35px;padding:20px 0;}
.page-header h1{font-family:'Orbitron',monospace;font-size:clamp(1.8rem,4vw,2.8rem);background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 0 40px rgba(0,255,249,0.15);}
.page-header p{color:var(--muted);font-size:.95rem;letter-spacing:2px;text-transform:uppercase;}
.glow-line{height:4px;background:linear-gradient(90deg,var(--neon-cyan),var(--neon-pink),var(--neon-purple));border-radius:2px;margin-bottom:25px;animation:glow 2.5s linear infinite;background-size:200%;box-shadow:0 0 25px rgba(0,255,249,0.25);}
@keyframes glow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.sub-nav{display:flex;justify-content:center;gap:8px;margin-bottom:30px;background:rgba(13,0,26,0.6);backdrop-filter:blur(12px);padding:6px;border-radius:14px;width:fit-content;margin-left:auto;margin-right:auto;border:1px solid var(--border);animation:fadeUp .5s ease both;}
.sub-nav button{padding:12px 28px;border-radius:10px;border:none;background:transparent;color:var(--muted);cursor:pointer;font-weight:600;font-size:.88rem;transition:all .3s;display:flex;align-items:center;gap:8px;}
.sub-nav button:hover{color:var(--text);background:rgba(0,255,249,0.06);}
.sub-nav button.active{background:linear-gradient(135deg,rgba(0,255,249,0.15),rgba(200,0,255,0.1));color:var(--neon-cyan);box-shadow:0 4px 20px rgba(0,255,249,0.1);border:1px solid rgba(0,255,249,0.15);}
form{max-width:580px;margin:0 auto;background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));backdrop-filter:blur(12px);padding:35px;border-radius:20px;border:1px solid var(--border);animation:fadeUp .6s ease both;}
form h2{text-align:center;font-family:'Orbitron',monospace;font-size:1.3rem;margin-bottom:6px;color:var(--neon-cyan);}
form .form-subtitle{text-align:center;color:var(--muted);margin-bottom:25px;font-size:.9rem;}
.info-box{background:rgba(0,255,249,0.05);border:1px solid rgba(0,255,249,0.1);padding:14px 18px;border-radius:12px;margin-bottom:22px;font-size:.82rem;color:var(--muted);line-height:1.6;}
.form-group{margin-bottom:18px;}
.form-group label{display:block;font-weight:500;margin-bottom:6px;color:#cbd5e1;font-size:.85rem;}
input,textarea,select{width:100%;padding:13px 16px;border-radius:12px;border:1px solid var(--border);background:rgba(15,23,42,0.5);color:white;font-size:.9rem;transition:all .3s;}
input:focus,textarea:focus{outline:none;border-color:var(--neon-cyan);box-shadow:0 0 20px rgba(0,255,249,0.1);}
input::placeholder,textarea::placeholder{color:var(--muted)}
textarea{min-height:100px;resize:vertical}
.file-input-wrapper{position:relative;border:2px dashed rgba(0,255,249,0.2);border-radius:12px;padding:25px;text-align:center;transition:all .3s;cursor:pointer;background:rgba(15,23,42,0.4);}
.file-input-wrapper:hover{border-color:var(--neon-cyan);background:rgba(0,255,249,0.04);}
.file-input-wrapper input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;}
.file-input-wrapper .upload-icon{font-size:2.5rem;margin-bottom:10px;color:var(--muted);}
.file-input-wrapper p{color:var(--muted);font-size:.85rem;}
.file-input-wrapper span{color:var(--neon-cyan);font-weight:600;}
button.submit{width:100%;padding:15px;background:linear-gradient(135deg,rgba(0,255,249,0.2),rgba(200,0,255,0.1));border:1px solid rgba(0,255,249,0.2);border-radius:12px;font-weight:700;font-size:.95rem;color:var(--neon-cyan);cursor:pointer;transition:all .3s;margin-top:10px;}
button.submit:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,255,249,0.12);}
.products{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;margin-top:20px;}
.card{background:linear-gradient(145deg,var(--card),rgba(20,0,40,0.65));border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:all .4s;animation:fadeUp .6s ease both;}
.card:hover{transform:translateY(-6px);border-color:rgba(0,255,249,0.25);box-shadow:0 15px 40px rgba(0,255,249,0.06);}
.card .image-container{position:relative;overflow:hidden;height:200px;}
.card .image-container img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
.card:hover .image-container img{transform:scale(1.06);}
.card .image-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(3,7,18,0.85) 0%,transparent 60%);}
.card-content{padding:20px}
.card h3{font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:8px;}
.card .description{color:var(--muted);font-size:.82rem;line-height:1.5;margin-bottom:12px;}
.price-section{display:flex;align-items:center;gap:12px;margin-bottom:12px;flex-wrap:wrap;padding:14px;background:rgba(15,23,42,0.5);border-radius:12px;border:1px solid var(--border);}
.price-section .regular-price{font-size:1.3rem;font-weight:700;color:var(--neon-cyan);}
.price-section .currency{font-size:.75rem;color:var(--muted);margin-left:3px;}
.price-compare,.price-new{display:flex;flex-direction:column;gap:2px;}
.price-label{font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:var(--muted);font-weight:600;}
.old-price{text-decoration:line-through;color:var(--muted);font-size:.9rem;}
.new-price{font-size:1.4rem;font-weight:800;color:var(--success);}
.savings-tag{background:linear-gradient(135deg,rgba(255,0,110,0.2),rgba(200,0,255,0.1));color:var(--neon-pink);padding:5px 10px;border-radius:8px;font-size:.75rem;font-weight:700;border:1px solid rgba(255,0,110,0.2);}
.stock-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font-size:.8rem;font-weight:600;margin-bottom:15px;}
.stock-badge.in-stock{background:rgba(0,255,157,0.1);color:var(--success);}
.stock-badge.low-stock{background:rgba(255,107,0,0.1);color:var(--neon-orange);}
.stock-badge.out-of-stock{background:rgba(255,0,110,0.1);color:var(--neon-pink);}
.actions{display:flex;gap:10px;}
.actions a{flex:1;text-align:center;padding:10px 14px;border-radius:10px;font-weight:600;font-size:.82rem;text-decoration:none;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:6px;}
.actions .edit{background:rgba(0,255,249,0.1);color:var(--neon-cyan);border:1px solid rgba(0,255,249,0.2);}
.actions .edit:hover{background:rgba(0,255,249,0.2);}
.actions .delete{background:rgba(255,0,110,0.1);color:var(--neon-pink);border:1px solid rgba(255,0,110,0.2);}
.actions .delete:hover{background:rgba(255,0,110,0.2);}
.message{text-align:center;margin-bottom:20px;padding:14px 20px;border-radius:12px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:10px;max-width:600px;margin-left:auto;margin-right:auto;animation:fadeUp .4s ease;}
.message.success{background:rgba(0,255,157,0.1);border:1px solid rgba(0,255,157,0.15);color:var(--success);}
.message.error{background:rgba(255,0,110,0.1);border:1px solid rgba(255,0,110,0.15);color:var(--neon-pink);}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted);grid-column:1/-1;}
.empty-state .ico{font-size:3rem;margin-bottom:15px;opacity:.5;}
.empty-state h3{font-size:1.3rem;color:var(--muted);margin-bottom:8px;}
.promo-ribbon{position:absolute;top:16px;right:-30px;background:linear-gradient(135deg,var(--neon-pink),var(--neon-purple));color:#fff;padding:6px 35px;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;transform:rotate(45deg);box-shadow:0 4px 15px rgba(255,0,110,0.3);z-index:5;}
@media(max-width:768px){.products{grid-template-columns:1fr;}}
</style>
</head>
<body>
<canvas id="particles"></canvas>
<div class="main">
<div class="glow-line"></div>
<div class="page-header">
  <h1>GESTION DES PRODUITS</h1>
  <p>Gérez votre catalogue</p>
</div>

<?php if($message): ?>
<div class="message <?= preg_match('/succès|succes/',$message)?'success':'error' ?>">
  <i class="fas fa-<?= preg_match('/succès|succes/',$message)?'check-circle':'exclamation-circle' ?>"></i>
  <?= $message ?>
</div>
<?php endif; ?>

<div class="sub-nav">
  <button class="active" onclick="showForm()"><i class="fas fa-plus"></i> Ajouter</button>
  <button onclick="showList()"><i class="fas fa-th"></i> Catalogue (<?= count($produits) ?>)</button>
</div>

<form id="form" method="post" enctype="multipart/form-data">
  <h2>Nouveau Produit</h2>
  <p class="form-subtitle">Ajoutez un nouveau produit à votre catalogue</p>
  <div class="info-box"><strong>Configuration:</strong> Upload max: <?=$uploadMaxSize?> | Formats: JPG, PNG, GIF, WEBP</div>
  <div class="form-group"><label>Nom du produit</label><input type="text" name="nom" placeholder="Ex: iPhone 15 Pro Max" required></div>
  <div class="form-group"><label>Description</label><textarea name="description" placeholder="Décrivez votre produit..." required></textarea></div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div class="form-group"><label>Prix (Ar)</label><input type="number" name="prix" step="0.01" min="0" placeholder="0.00" required></div>
    <div class="form-group"><label>Stock</label><input type="number" name="stock" min="0" placeholder="0" required></div>
  </div>
  <div class="form-group">
    <label>Image du produit</label>
    <div class="file-input-wrapper">
      <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
      <p><span>Cliquez</span> ou glissez-déposez</p>
      <p style="font-size:.75rem;margin-top:6px;">PNG, JPG, GIF, WEBP (max 5MB)</p>
      <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
    </div>
  </div>
  <button class="submit" type="submit"><i class="fas fa-plus"></i> Ajouter le produit</button>
</form>

<div class="products" id="list" style="display:none">
<?php if(empty($produits)): ?>
  <div class="empty-state">
    <div class="ico">📦</div>
    <h3>Aucun produit</h3>
    <p>Commencez par ajouter votre premier produit</p>
  </div>
<?php else: foreach($produits as $p):
  $stockStatus = $p['stock'] > 10 ? 'in-stock' : ($p['stock'] > 0 ? 'low-stock' : 'out-of-stock');
  $stockLabel = $p['stock'] > 10 ? 'En stock' : ($p['stock'] > 0 ? 'Stock faible' : 'Rupture');
?>
  <div class="card">
    <div class="image-container">
      <img src="<?=htmlspecialchars($p['image'])?>" alt="<?=htmlspecialchars($p['nom'])?>" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%233b4251%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2220%22 fill=%22%2364748b%22 text-anchor=%22middle%22 dy=%22.3em%22%3EPas d%27image%3C/text%3E%3C/svg%3E'">
      <div class="image-overlay"></div>
    </div>
    <div class="card-content">
      <h3><?=htmlspecialchars($p['nom'])?></h3>
      <p class="description"><?=htmlspecialchars(substr($p['description'],0,100))?>...</p>
      <div class="price-section">
        <span class="regular-price"><?=number_format($p['prix'],0,',',' ')?><span class="currency">Ar</span></span>
      </div>
      <div class="stock-badge <?=$stockStatus?>"><i class="fas fa-box"></i> <?=$stockLabel?> (<?=$p['stock']?>)</div>
      <div class="actions">
        <a class="edit" href="modifier_produit.php?id=<?=$p['id']?>"><i class="fas fa-edit"></i> Modifier</a>
        <a class="delete" href="supprimer_produit.php?id=<?=$p['id']?>" onclick="return confirm('Supprimer ce produit ?')"><i class="fas fa-trash"></i> Supprimer</a>
      </div>
    </div>
  </div>
<?php endforeach; endif; ?>
</div>
</div>

<script>
const form=document.getElementById('form'),list=document.getElementById('list'),btns=document.querySelectorAll('.sub-nav button');
function showForm(){form.style.display='block';list.style.display='none';btns[0].classList.add('active');btns[1].classList.remove('active');}
function showList(){form.style.display='none';list.style.display='grid';btns[1].classList.add('active');btns[0].classList.remove('active');}
const fw=document.querySelector('.file-input-wrapper');
if(fw){['dragenter','dragover'].forEach(e=>fw.addEventListener(e,()=>fw.style.borderColor='#00fff9'));['dragleave','drop'].forEach(e=>fw.addEventListener(e,()=>fw.style.borderColor='rgba(0,255,249,0.2)'));}
</script>
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