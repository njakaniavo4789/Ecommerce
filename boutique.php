<?php 
    session_start();
    require 'connectionBD.php';
    $sql="SELECT * FROM produit";
    $stmt=$pdo->prepare($sql);
    $stmt->execute();
    $produits=$stmt->fetchAll();
    $sql="SELECT * FROM marketing";
    $stmt=$pdo->prepare($sql);
    $stmt->execute();
    $promotions=$stmt->fetchAll();
    $cartData = $_SESSION['panier'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NJAKA — DROP 09</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --ink:       #03010a;
      --surface:   #0a0616;
      --glass:     rgba(255,255,255,0.035);
      --glass-b:   rgba(255,255,255,0.07);
      --glass-h:   rgba(255,255,255,0.07);
      --acid:      #c8ff00;
      --acid-glow: rgba(200,255,0,0.35);
      --acid-dim:  rgba(200,255,0,0.12);
      --magenta:   #ff2d78;
      --mag-glow:  rgba(255,45,120,0.4);
      --cyan:      #00e5ff;
      --violet:    #7c3aed;
      --text:      #f0ecff;
      --muted:     rgba(240,236,255,0.42);
      --faint:     rgba(240,236,255,0.16);
      --success:   #39ff8a;
      --danger:    #ff3d3d;
      --rs: 10px; --rm: 18px; --rl: 26px; --rxl: 40px;
    }
    *{margin:0;padding:0;box-sizing:border-box;}
    html{scroll-behavior:smooth;}
    body{background:var(--ink);color:var(--text);font-family:'Space Grotesk',sans-serif;min-height:100vh;overflow-x:hidden;}

    /* CANVAS */
    #bgc{position:fixed;inset:0;z-index:0;pointer-events:none;}

    /* ORBS */
    .orb{position:fixed;border-radius:50%;filter:blur(130px);pointer-events:none;z-index:0;}
    .o1{width:800px;height:800px;background:rgba(200,255,0,0.05);top:-250px;left:-250px;}
    .o2{width:650px;height:650px;background:rgba(255,45,120,0.055);bottom:-150px;right:-150px;}
    .o3{width:450px;height:450px;background:rgba(0,229,255,0.038);top:45%;left:50%;transform:translate(-50%,-50%);}

    /* GRAIN */
    body::after{content:'';position:fixed;inset:0;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='g'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23g)' opacity='0.035'/%3E%3C/svg%3E");pointer-events:none;z-index:1;opacity:.65;}

    /* ════════════════ HEADER ════════════════ */
    header{
      position:fixed;top:14px;left:50%;transform:translateX(-50%);z-index:200;
      width:calc(100% - 40px);max-width:1380px;height:58px;
      display:flex;align-items:center;justify-content:space-between;
      background:rgba(3,1,10,0.6);
      backdrop-filter:blur(30px) saturate(200%);-webkit-backdrop-filter:blur(30px) saturate(200%);
      border:1px solid var(--glass-b);border-radius:var(--rxl);
      padding:0 1.1rem 0 1.5rem;
      box-shadow:0 8px 40px rgba(0,0,0,0.45),inset 0 1px 0 rgba(255,255,255,0.055);
    }
    .hl{display:flex;align-items:center;gap:1.1rem;}
    .hr{display:flex;align-items:center;gap:.55rem;}

    .logo{
      font-family:'Syne',sans-serif;font-size:1.55rem;font-weight:800;letter-spacing:-.5px;
      cursor:pointer;user-select:none;
      background:linear-gradient(130deg,#fff 30%,var(--acid));
      -webkit-background-clip:text;background-clip:text;color:transparent;
    }
    .drop-badge{
      font-family:'JetBrains Mono',monospace;font-size:.6rem;font-weight:700;
      letter-spacing:2px;text-transform:uppercase;
      background:var(--acid-dim);border:1px solid rgba(200,255,0,0.22);
      color:var(--acid);padding:.22rem .65rem;border-radius:50px;
    }
    .ntabs{display:flex;gap:2px;background:rgba(255,255,255,0.04);border:1px solid var(--glass-b);border-radius:50px;padding:3px;}
    .ntabs button{background:none;border:none;color:var(--muted);font-family:'Space Grotesk',sans-serif;font-size:.78rem;font-weight:500;padding:.36rem .95rem;border-radius:50px;cursor:pointer;transition:all .22s;white-space:nowrap;}
    .ntabs button:hover{color:var(--text);background:rgba(255,255,255,0.07);}
    .ntabs button.act{color:var(--text);background:rgba(255,255,255,0.1);}

    .hbtn{background:var(--glass);border:1px solid var(--glass-b);color:var(--muted);font-family:'Space Grotesk',sans-serif;font-size:.78rem;font-weight:600;padding:.38rem .95rem;border-radius:50px;cursor:pointer;transition:all .22s;}
    .hbtn:hover{color:var(--text);background:var(--glass-h);border-color:rgba(255,255,255,0.13);}

    .cartbtn{background:var(--acid);border:none;color:#000;font-family:'Space Grotesk',sans-serif;font-size:.8rem;font-weight:700;padding:.4rem 1rem .4rem .85rem;border-radius:50px;cursor:pointer;transition:all .28s;display:flex;align-items:center;gap:.45rem;box-shadow:0 0 16px var(--acid-glow);}
    .cartbtn:hover{transform:translateY(-2px);box-shadow:0 4px 28px var(--acid-glow);}
    .cn{background:#000;color:var(--acid);font-family:'JetBrains Mono',monospace;font-size:.68rem;font-weight:700;min-width:19px;height:19px;border-radius:50%;display:flex;align-items:center;justify-content:center;}

    /* ════════════════ MAIN ════════════════ */
    main{padding-top:88px;position:relative;z-index:2;}
    .page{display:none;}
    #shop{display:block;}

    /* ════════════════ HERO ════════════════ */
    .hero{min-height:94vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:5rem 6% 4rem;position:relative;}

    .eyebrow{font-family:'JetBrains Mono',monospace;font-size:.68rem;letter-spacing:6px;text-transform:uppercase;color:var(--acid);margin-bottom:2rem;opacity:0;animation:up .7s .15s forwards;display:flex;align-items:center;gap:.8rem;}
    .eyebrow::before,.eyebrow::after{content:'';flex:1;max-width:55px;height:1px;}
    .eyebrow::before{background:linear-gradient(90deg,transparent,var(--acid));}
    .eyebrow::after{background:linear-gradient(270deg,transparent,var(--acid));}

    .htitle{font-family:'Syne',sans-serif;font-size:clamp(5.5rem,20vw,17rem);font-weight:800;line-height:.86;letter-spacing:-8px;opacity:0;animation:up .85s .3s forwards;}
    .htitle .l1{color:var(--text);}
    .htitle .l2{display:block;background:linear-gradient(135deg,var(--acid) 0%,var(--cyan) 55%,var(--acid) 100%);background-size:200%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:shimmer 4s 1.2s linear infinite;}
    @keyframes shimmer{0%{background-position:0%}100%{background-position:200%}}

    .hsub{font-size:1rem;color:var(--muted);margin-top:2rem;font-weight:400;line-height:1.65;max-width:460px;opacity:0;animation:up .75s .5s forwards;}

    .hcta{display:flex;gap:.75rem;margin-top:2.5rem;flex-wrap:wrap;justify-content:center;opacity:0;animation:up .75s .65s forwards;}
    .btn-acid{background:var(--acid);border:none;color:#000;font-family:'Space Grotesk',sans-serif;font-size:.92rem;font-weight:700;padding:.88rem 2.1rem;border-radius:50px;cursor:pointer;transition:all .3s;box-shadow:0 0 26px var(--acid-glow);}
    .btn-acid:hover{transform:translateY(-3px) scale(1.03);box-shadow:0 8px 38px var(--acid-glow);}
    .btn-wire{background:transparent;border:1px solid var(--glass-b);color:var(--text);font-family:'Space Grotesk',sans-serif;font-size:.92rem;font-weight:500;padding:.88rem 2.1rem;border-radius:50px;cursor:pointer;transition:all .3s;}
    .btn-wire:hover{background:var(--glass);border-color:rgba(255,255,255,.17);transform:translateY(-3px);}

    /* LIVE META */
    .hmeta{display:flex;align-items:center;gap:2rem;margin-top:3.5rem;opacity:0;animation:up .75s .8s forwards;flex-wrap:wrap;justify-content:center;}
    .ticker{display:flex;align-items:center;gap:.75rem;background:rgba(255,255,255,0.03);border:1px solid var(--glass-b);border-radius:var(--rxl);padding:.65rem 1.3rem;}
    .tdot{width:7px;height:7px;border-radius:50%;background:var(--magenta);box-shadow:0 0 8px var(--magenta);animation:pd 1.3s infinite;}
    @keyframes pd{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.45;transform:scale(1.6)}}
    .tlabel{font-family:'JetBrains Mono',monospace;font-size:.63rem;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
    .tval{font-family:'Syne',sans-serif;font-size:1.55rem;font-weight:800;color:var(--magenta);letter-spacing:-1px;min-width:68px;}
    .smeter{width:210px;}
    .smtop{display:flex;justify-content:space-between;font-family:'JetBrains Mono',monospace;font-size:.62rem;color:var(--muted);margin-bottom:.45rem;}
    .smtrack{height:3px;background:rgba(255,255,255,0.06);border-radius:99px;overflow:hidden;}
    .smfill{height:100%;width:68%;background:linear-gradient(90deg,var(--magenta),var(--acid));border-radius:99px;animation:barin 1.8s 1.4s both cubic-bezier(.4,0,.2,1);}
    @keyframes barin{from{width:0}to{width:68%}}

    /* ════════════════ SECTION HEADERS ════════════════ */
    .sh{display:flex;align-items:baseline;justify-content:space-between;padding:0 5%;margin:5.5rem 0 1.8rem;}
    .stitle{font-family:'Syne',sans-serif;font-size:2.1rem;font-weight:800;letter-spacing:-1.2px;}
    .stag{font-family:'JetBrains Mono',monospace;font-size:.62rem;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}

    /* ════════════════ GAME ZONE ════════════════ */
    .gz{display:grid;grid-template-columns:2fr 1.2fr 1fr;gap:1.1rem;padding:0 5%;margin-bottom:4.5rem;max-width:1380px;margin-left:auto;margin-right:auto;}
    @media(max-width:900px){.gz{grid-template-columns:1fr 1fr;}}
    @media(max-width:600px){.gz{grid-template-columns:1fr;}}

    .gcard{background:var(--glass);border:1px solid var(--glass-b);border-radius:var(--rl);padding:1.7rem;position:relative;overflow:hidden;transition:transform .3s,border-color .3s;}
    .gcard::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.11),transparent);}
    .gcard:hover{transform:translateY(-4px);border-color:rgba(255,255,255,0.11);}

    /* Flash */
    .fc{grid-column:1;background:linear-gradient(135deg,rgba(255,45,120,.08),rgba(124,58,237,.08));border-color:rgba(255,45,120,.18);}
    .fctop{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.1rem;}
    .fcbadge{background:rgba(255,45,120,.14);border:1px solid rgba(255,45,120,.28);color:var(--magenta);font-family:'JetBrains Mono',monospace;font-size:.6rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:.22rem .7rem;border-radius:50px;white-space:nowrap;}
    .fctimer{font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;letter-spacing:-1px;color:#fff;}
    .fctitle{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:var(--magenta);margin-bottom:.3rem;}
    .fcdesc{font-size:.82rem;color:var(--muted);line-height:1.5;}
    .fcpill{display:inline-flex;align-items:center;gap:.38rem;background:rgba(255,45,120,.1);border:1px solid rgba(255,45,120,.18);border-radius:50px;padding:.3rem .85rem;margin-top:.9rem;font-size:.75rem;font-weight:600;color:var(--magenta);}
    .livedot{width:6px;height:6px;border-radius:50%;background:var(--magenta);display:inline-block;}

    /* Wheel */
    .wctitle{font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;margin-bottom:.45rem;}
    .wcdesc{font-size:.8rem;color:var(--muted);line-height:1.5;margin-bottom:1.1rem;}
    .wbtn{width:100%;background:linear-gradient(135deg,var(--violet),var(--magenta));border:none;color:#fff;font-family:'Space Grotesk',sans-serif;font-size:.88rem;font-weight:700;padding:.82rem;border-radius:var(--rs);cursor:pointer;transition:all .3s;position:relative;overflow:hidden;}
    .wbtn::after{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.16),transparent);transition:left .5s;}
    .wbtn:hover::after{left:100%;}
    .wbtn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(124,58,237,.48);}
    .wbtn:disabled{opacity:.35;cursor:not-allowed;transform:none;box-shadow:none;}
    .wres{display:none;text-align:center;margin-top:1.1rem;}
    .wpct{font-family:'Syne',sans-serif;font-size:2.8rem;font-weight:800;color:var(--success);text-shadow:0 0 22px rgba(57,255,138,.38);animation:pop .5s cubic-bezier(.34,1.56,.64,1);}
    @keyframes pop{from{transform:scale(.35);opacity:0}to{transform:scale(1);opacity:1}}
    .wcode{display:none;margin-top:.55rem;background:rgba(57,255,138,.07);border:1px solid rgba(57,255,138,.22);border-radius:var(--rs);padding:.45rem .9rem;font-family:'JetBrains Mono',monospace;font-size:.9rem;color:var(--success);letter-spacing:3px;}
    .wleft{font-family:'JetBrains Mono',monospace;font-size:.62rem;color:var(--muted);margin-top:.8rem;text-align:center;}

    /* Stats */
    .sc{display:flex;flex-direction:column;justify-content:space-between;}
    .sr{padding:.85rem 0;border-bottom:1px solid rgba(255,255,255,.05);}
    .sr:last-child{border-bottom:none;padding-bottom:0;}
    .sr:first-child{padding-top:0;}
    .sl{font-size:.75rem;color:var(--muted);margin-bottom:.18rem;}
    .sv{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;}
    .g{color:var(--success);}.a{color:var(--acid);}.m{color:var(--magenta);}.c{color:var(--cyan);}

    /* ════════════════ PRODUCTS ════════════════ */
    .prods{display:grid;grid-template-columns:repeat(auto-fill,minmax(285px,1fr));gap:1.3rem;padding:0 5%;margin-bottom:6.5rem;}

    .pcard{background:var(--glass);border:1px solid var(--glass-b);border-radius:var(--rl);overflow:hidden;transition:all .45s cubic-bezier(.4,0,.2,1);position:relative;}
    .pcard:hover{border-color:rgba(200,255,0,.26);transform:translateY(-10px);box-shadow:0 28px 65px rgba(0,0,0,.52),0 0 0 1px rgba(200,255,0,.07),0 0 45px rgba(200,255,0,.055);}

    .pimg{position:relative;height:310px;overflow:hidden;background:var(--surface);}
    .pimg img{width:100%;height:100%;object-fit:cover;transition:transform .7s cubic-bezier(.4,0,.2,1),filter .4s;filter:brightness(.87);}
    .pcard:hover .pimg img{transform:scale(1.1);filter:brightness(1);}
    .pgrad{position:absolute;bottom:0;left:0;right:0;height:52%;background:linear-gradient(to top,rgba(3,1,10,.9),transparent);pointer-events:none;}

    .ptag-stock{position:absolute;top:11px;left:11px;background:rgba(3,1,10,.72);backdrop-filter:blur(10px);border:1px solid var(--glass-b);border-radius:50px;color:var(--magenta);font-family:'JetBrains Mono',monospace;font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.22rem .7rem;}
    .ptag-disc{position:absolute;top:11px;right:11px;background:var(--acid);color:#000;font-family:'Space Grotesk',sans-serif;font-size:.7rem;font-weight:700;padding:.22rem .7rem;border-radius:50px;}

    .pbody{padding:1.25rem 1.35rem 1.55rem;}
    .pname{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;letter-spacing:-.35px;margin-bottom:.65rem;}
    .ppr{display:flex;align-items:baseline;gap:.55rem;margin-bottom:1rem;}
    .pprice{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:var(--acid);}
    .pold{font-size:.85rem;color:var(--faint);text-decoration:line-through;}

    .btnadd{width:100%;display:inline-block;text-align:center;text-decoration:none;background:transparent;border:1px solid rgba(255,255,255,.09);color:rgba(240,236,255,.65);font-family:'Space Grotesk',sans-serif;font-size:.83rem;font-weight:600;padding:.82rem;border-radius:var(--rs);cursor:pointer;transition:all .3s;letter-spacing:.3px;}
    .btnadd:hover{background:var(--acid);color:#000;border-color:var(--acid);box-shadow:0 0 20px var(--acid-glow);}
    .btnadd:active{transform:scale(.97);}

    /* ════════════════ PAGES INTERNES ════════════════ */
    .pinner{max-width:760px;margin:0 auto;padding:4rem 5%;}
    .pt{font-family:'Syne',sans-serif;font-size:2.7rem;font-weight:800;letter-spacing:-2px;margin-bottom:2.3rem;}

    /* CART */
    .empty{text-align:center;padding:4.5rem 0;color:var(--muted);}
    .eico{font-size:3.2rem;margin-bottom:1.1rem;opacity:.28;}

    .ci{background:var(--glass);border:1px solid var(--glass-b);border-radius:var(--rm);padding:1rem 1.2rem;margin-bottom:.85rem;display:flex;gap:1rem;align-items:center;transition:border-color .25s;}
    .ci:hover{border-color:rgba(255,255,255,.1);}
    .ci img{width:68px;height:68px;object-fit:cover;border-radius:var(--rs);flex-shrink:0;}
    .ci-info{flex:1;}
    .ci-name{font-weight:600;font-size:.9rem;margin-bottom:.22rem;}
    .ci-price{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:var(--acid);}
    .ci-disc{font-size:.7rem;color:var(--success);margin-top:.12rem;}
    .btnrm{background:transparent;border:1px solid rgba(255,61,61,.22);color:var(--danger);font-family:'Space Grotesk',sans-serif;font-size:.72rem;font-weight:600;padding:.32rem .8rem;border-radius:50px;cursor:pointer;transition:all .25s;flex-shrink:0;}
    .btnrm:hover{background:rgba(255,61,61,.1);border-color:var(--danger);}

    .csum{background:var(--glass);border:1px solid var(--glass-b);border-radius:var(--rm);padding:1.7rem;margin-top:1.1rem;}
    .ctr{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;}
    .ctl{font-size:.82rem;color:var(--muted);}
    .ctp{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--acid);}
    .cperks{display:flex;gap:1.1rem;flex-wrap:wrap;margin-bottom:1.2rem;}
    .cpk{display:flex;align-items:center;gap:.32rem;font-size:.73rem;color:var(--muted);}
    .cpkd{width:5px;height:5px;border-radius:50%;background:var(--success);flex-shrink:0;}

    /* FORMS */
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:.9rem;}
    @media(max-width:500px){.frow{grid-template-columns:1fr;}}
    .fg{margin-bottom:1rem;}
    .fl{display:block;font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:.4rem;}
    .fi,.fsel,.fta{width:100%;background:rgba(255,255,255,.03);border:1px solid var(--glass-b);border-radius:var(--rs);color:var(--text);font-family:'Space Grotesk',sans-serif;font-size:.88rem;padding:.82rem .95rem;transition:all .22s;-webkit-appearance:none;}
    .fsel option{background:#0a0616;}
    .fta{resize:vertical;min-height:108px;}
    .fi:focus,.fsel:focus,.fta:focus{outline:none;border-color:rgba(200,255,0,.42);background:rgba(200,255,0,.028);box-shadow:0 0 0 3px rgba(200,255,0,.065);}
    .fi::placeholder,.fta::placeholder{color:var(--faint);}
    .sdiv{height:1px;background:var(--glass-b);margin:1.5rem 0;}

    .pgrid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin:1.1rem 0;}
    .pm{background:var(--glass);border:1px solid var(--glass-b);border-radius:var(--rs);padding:.85rem;cursor:pointer;transition:all .25s;text-align:center;}
    .pm:hover,.pm.sel{border-color:rgba(200,255,0,.38);background:rgba(200,255,0,.048);}
    .pmico{font-size:1.25rem;margin-bottom:.22rem;}
    .pmlbl{font-size:.72rem;font-weight:600;color:var(--muted);}

    /* SUCCESS */
    .sov{text-align:center;padding:3.5rem 2rem;display:none;}
    .sico{width:68px;height:68px;border-radius:50%;background:rgba(57,255,138,.1);border:1px solid rgba(57,255,138,.22);display:flex;align-items:center;justify-content:center;font-size:1.7rem;margin:0 auto 1.3rem;animation:pop .6s ease;}
    .stitle{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--success);margin-bottom:.55rem;}
    .ssub{color:var(--muted);font-size:.88rem;line-height:1.65;}

    /* NOTIF */
    .notif{position:fixed;bottom:18px;right:18px;background:rgba(10,6,22,.9);border:1px solid var(--glass-b);backdrop-filter:blur(20px);border-radius:var(--rm);padding:.85rem 1.3rem;font-size:.83rem;font-weight:500;z-index:999;box-shadow:0 14px 38px rgba(0,0,0,.58);animation:nsl .38s cubic-bezier(.34,1.56,.64,1);display:flex;align-items:center;gap:.65rem;max-width:290px;}
    @keyframes nsl{from{transform:translateX(110%) scale(.88);opacity:0}to{transform:translateX(0) scale(1);opacity:1}}

    /* REVEAL */
    .rv{opacity:0;transform:translateY(28px);transition:opacity .6s,transform .6s;}
    .rv.in{opacity:1;transform:translateY(0);}

    @keyframes up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

    /* RESPONSIVE */
    @media(max-width:768px){
      header{width:calc(100% - 22px);top:9px;}
      .logo{font-size:1.35rem;}
      .ntabs{display:none;}
      .drop-badge{display:none;}
      .htitle{letter-spacing:-5px;}
      .sh,.prods{padding:0 4%;}
      .gz{padding:0 4%;}
      .pinner{padding:3rem 4%;}
    }
    @media(max-width:480px){
      .hmeta{flex-direction:column;gap:.9rem;}
      .hbtn span{display:none;}
    }
  </style>
</head>
<body>

<canvas id="bgc"></canvas>
<div class="orb o1"></div>
<div class="orb o2"></div>
<div class="orb o3"></div>

<!-- ════ HEADER ════ -->
<header>
  <div class="hl">
    <div class="logo" onclick="nav('shop')">NJAKA</div>
    <div class="drop-badge">Drop 09</div>
    <nav class="ntabs">
      <button class="act" onclick="nav('shop');act(this)">Boutique</button>
      <button onclick="nav('cart');act(this)">Panier</button>
      <button onclick="nav('reclamation');act(this)">Réclamation</button>
    </nav>
  </div>
  <div class="hr">
    <button class="hbtn" onclick="nav('reclamation')"><span>⚠</span> <span>Réclamation</span></button>
    <button class="cartbtn" onclick="nav('cart')">
      🛒 <span class="cn" id="cartCount">0</span>
    </button>
  </div>
</header>

<main>

  <!-- ════ BOUTIQUE ════ -->
  <div id="shop" class="page">

    <section class="hero">
      <div class="eyebrow">Collection exclusive · Édition limitée</div>
      <h1 class="htitle"><span class="l1">DROP</span><span class="l2">09</span></h1>
      <p class="hsub">Cyberpunk fashion pour ceux qui refusent l'ordinaire.<br>Pièces en quantité limitée.</p>
      <div class="hcta">
        <button class="btn-acid" onclick="document.querySelector('.prods').scrollIntoView({behavior:'smooth'})">Découvrir la collection →</button>
        <button class="btn-wire" onclick="spinWheel()">Gagner une réduction</button>
      </div>
      <div class="hmeta">
        <div class="ticker">
          <div class="tdot"></div>
          <div>
            <div class="tlabel">Fin du drop</div>
            <div class="tval" id="countdown">00:00</div>
          </div>
        </div>
        <div class="smeter">
          <div class="smtop"><span>Stock vendu</span><span style="color:var(--magenta)">68% · Vite !</span></div>
          <div class="smtrack"><div class="smfill"></div></div>
        </div>
      </div>
    </section>

    <div class="sh rv"><div class="stitle">Offres du moment</div><div class="stag">Exclusivités · Drop 09</div></div>
    <div class="gz">

      <div class="gcard fc rv">
        <div class="fctop">
          <div>
            <div class="fcbadge">⚡ Vente Flash</div>
            <div class="fctitle" style="margin-top:.8rem;">–10% sur tout</div>
            <div class="fcdesc">Réduction automatique sur toute la boutique pendant ce temps imparti.</div>
            <div class="fcpill"><span class="livedot"></span> Actif maintenant</div>
          </div>
          <div style="text-align:right;flex-shrink:0;">
            <div style="font-family:'JetBrains Mono',monospace;font-size:.58rem;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-bottom:.25rem;">Expire dans</div>
            <div class="fctimer" id="flashTimer">15:00</div>
          </div>
        </div>
      </div>

      <div class="gcard rv">
        <div class="wctitle">🎰 Roue chance</div>
        <div class="wcdesc">Jusqu'à <strong style="color:var(--success)">50% off</strong> sur votre prochaine commande.</div>
        <button class="wbtn" id="spinBtn" onclick="spinWheel()">Tourner la roue</button>
        <div class="wres" id="wRes">
          <div class="wpct" id="wPct"></div>
          <div class="wcode" id="wCode"></div>
        </div>
        <div class="wleft"><span id="spinsLeft">3</span> tentatives restantes</div>
      </div>

      <div class="gcard sc rv">
        <div class="sr"><div class="sl">Clients satisfaits</div><div class="sv g">+1 200</div></div>
        <div class="sr"><div class="sl">Livraison</div><div class="sv a">24 – 48h</div></div>
        <div class="sr"><div class="sl">Avis 5★</div><div class="sv m">98%</div></div>
        <div class="sr"><div class="sl">Retours</div><div class="sv c">Gratuits</div></div>
      </div>

    </div>

    <div class="sh rv"><div class="stitle">Collection</div><div class="stag">Drop 09 · <?= count($produits) ?> pièces</div></div>
    <div class="prods">
<?php foreach($produits as $prod):
  $imgPath = trim($prod['image'] ?? '');
  if ($imgPath && !preg_match('/^https?:\/\//i', $imgPath)) {
    $imgPath = ltrim($imgPath, './');
    if (!preg_match('/\.(jpe?g|png|webp|gif)$/i', $imgPath)) {
      foreach(['jpg','jpeg','png','webp','gif'] as $ext) {
        if (file_exists($imgPath.'.'.$ext)) { $imgPath .= '.'.$ext; break; }
      }
      if (!preg_match('/\.(jpe?g|png|webp|gif)$/i', $imgPath)) $imgPath .= '.jpg';
    }
  }
  if (empty($imgPath)) $imgPath = 'images/placeholder.jpg';
  $pOrig = $prod['prix'];
  $red = !empty($promotions) ? ($promotions[0]['reduction'] ?? 0) : 0;
  $pFinal = $red > 0 ? round($pOrig * (1 - $red/100)) : $pOrig;
?>
      <div class="pcard rv">
        <div class="pimg">
          <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" onerror="this.src='images/placeholder.jpg'">
          <div class="pgrad"></div>
          <div class="ptag-stock">Stock · <?= htmlspecialchars($prod['stock']) ?></div>
          <?php if($red>0): ?><div class="ptag-disc">-<?= $red ?>%</div><?php endif; ?>
        </div>
        <div class="pbody">
          <div class="pname"><?= htmlspecialchars($prod['nom']) ?></div>
          <div class="ppr">
            <span class="pprice"><?= number_format($pFinal,0,',',' ') ?> Ar</span>
            <?php if($red>0): ?><span class="pold"><?= number_format($pOrig,0,',',' ') ?> Ar</span><?php endif; ?>
          </div>
          <a class="btnadd" href="Ajouterpanier.php?id=<?= $prod['id'] ?>">
               Ajouter au panier
          </a>
        </div>
      </div>
<?php endforeach; ?>
    </div>

  </div>

  <!-- ════ PANIER ════ -->
  <div id="cart" class="page">
    <div class="pinner">
      <div class="pt">Panier</div>
      <div id="cartItems"></div>
      <div id="cartSum" style="display:none">
        <div class="csum">
          <div class="ctr"><span class="ctl">Total commande</span><span class="ctp" id="cartTotal">0 Ar</span></div>
          <div class="cperks">
            <div class="cpk"><div class="cpkd"></div> Livraison 24–48h</div>
            <div class="cpk"><div class="cpkd"></div> Paiement sécurisé</div>
            <div class="cpk"><div class="cpkd"></div> Retours gratuits</div>
          </div>
          <button class="btn-acid" style="width:100%;font-size:.92rem;padding:.95rem;" onclick="nav('payment')">Passer au paiement →</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ════ PAIEMENT ════ -->
  <div id="payment" class="page">
    <div class="pinner">
      <div class="pt">Finalisation</div>
      <form method="POST" action="paiement.php" id="payForm">
        <input type="hidden" name="mode_paiement" id="modePaiement" value="">
        <div class="frow">
          <div class="fg"><label class="fl">Nom</label><input type="text" name="nom" class="fi" placeholder="Rakoto" required></div>
          <div class="fg"><label class="fl">Prénom</label><input type="text" name="prenom" class="fi" placeholder="Njaka" required></div>
        </div>
        <div class="fg"><label class="fl">Email / Téléphone</label><input type="text" name="email" class="fi" placeholder="+261 34 00 000 00" required></div>
        <div class="fg"><label class="fl">Adresse de livraison</label><input type="text" name="adresse" class="fi" placeholder="Lot IIY …, Antananarivo" required></div>
        <div class="sdiv"></div>
        <div class="fl" style="margin-bottom:.75rem;">Mode de paiement</div>
        <div class="pgrid">
          <div class="pm" onclick="selPay(this,'MVola')"><div class="pmico">💰</div><div class="pmlbl">MVola</div></div>
          <div class="pm" onclick="selPay(this,'Orange Money')"><div class="pmico">🟠</div><div class="pmlbl">Orange Money</div></div>
          <div class="pm" onclick="selPay(this,'Airtel Money')"><div class="pmico">🔴</div><div class="pmlbl">Airtel Money</div></div>
          <div class="pm" onclick="selPay(this,'Visa / Mastercard')"><div class="pmico">💳</div><div class="pmlbl">Visa / MC</div></div>
        </div>
        <button type="submit" class="btn-acid" style="width:100%;font-size:.92rem;padding:.95rem;margin-top:1.1rem;">Confirmer & Payer</button>
      </form>
      <div class="sov" id="payOk" style="display:none">
        <div class="sico">✓</div>
        <div class="stitle">Commande confirmée !</div>
        <p class="ssub">Merci pour votre achat NJAKA.<br>Livraison sous 24–48h.</p>
      </div>
    </div>
  </div>

  <!-- ════ RÉCLAMATION ════ -->
  <div id="reclamation" class="page">
  <div class="pinner">
    <div class="pt">Réclamation</div>

    <form id="rForm" method="POST" enctype="multipart/form-data" action="reclamation.php">

      <div class="frow">

        <div class="fg">
          <label class="fl">Nom complet</label>
          <input 
            type="text" 
            class="fi" 
            id="rNom" 
            placeholder="Votre nom" 
            name="nom"
          >
        </div>

        <div class="fg">
          <label class="fl">Email / Téléphone</label>
          <input 
            type="text" 
            class="fi" 
            id="rEmail" 
            placeholder="+261 34 00 000 00" 
            name="email"
          >
        </div>

      </div>

      <div class="fg">
        <label class="fl">N° de commande</label>

        <input 
          name="CommandeNumero" 
          type="text" 
          class="fi" 
          id="rCmd" 
          placeholder="#CMD-001"
        >
      </div>

      <div class="fg">
        <label class="fl">Type de réclamation</label>

        <select class="fsel" id="rType" name="type_reclamation">
          <option value="">— Sélectionnez —</option>
          <option>Produit défectueux</option>
          <option>Retard de livraison</option>
          <option>Produit manquant</option>
          <option>Mauvaise taille / couleur</option>
          <option>Autre</option>
        </select>
      </div>

      <div class="fg">
        <label class="fl">Description</label>

        <textarea 
          name="description" 
          class="fta" 
          id="rDesc" 
          placeholder="Décrivez votre problème en détail…"
        ></textarea>
      </div>

      <div class="fg">

        <label class="fl">Photos (optionnel)</label>

        <input 
          type="file" 
          id="rPhotos" 
          accept="image/*" 
          multiple 
          style="display:none"
          name="photo"
        >

        <button 
          type="button" 
          onclick="document.getElementById('rPhotos').click()"
          style="width:100%;background:transparent;border:1px dashed rgba(200,255,0,.22);color:var(--muted);font-family:'Space Grotesk',sans-serif;font-size:.83rem;padding:.95rem;border-radius:var(--rs);cursor:pointer;transition:all .25s;"
          onmouseover="this.style.borderColor='rgba(200,255,0,.5)';this.style.color='var(--acid)'"
          onmouseout="this.style.borderColor='rgba(200,255,0,.22)';this.style.color='var(--muted)'"
          name="ajouter"
        >
          + Ajouter des photos

        </button>

        <div id="rPinfo" style="margin-top:.38rem;font-size:.7rem;color:var(--muted);"></div>

      </div>

      <button 
        type="submit"
        class="btn-acid"
        style="width:100%;font-size:.92rem;padding:.95rem;">

        Envoyer la réclamation

      </button>

    </form>

    <div class="sov" id="rOk">
      <div class="sico">✓</div>
      <div class="stitle">Réclamation envoyée !</div>
      <p class="ssub">Nous vous répondrons sous 24–48h.</p>
    </div>

  </div>
</div>

</main>

<script>
/* PARTICLES */
(()=>{
  const c=document.getElementById('bgc'),x=c.getContext('2d');
  let W,H,P;
  const rsz=()=>{W=c.width=innerWidth;H=c.height=innerHeight;};
  const ini=()=>{P=Array.from({length:48},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.1+.3,vx:(Math.random()-.5)*.2,vy:(Math.random()-.5)*.2,a:Math.random()*.6+.1}));};
  const drw=()=>{
    x.clearRect(0,0,W,H);
    P.forEach(p=>{
      p.x+=p.vx;p.y+=p.vy;
      if(p.x<0)p.x=W;if(p.x>W)p.x=0;if(p.y<0)p.y=H;if(p.y>H)p.y=0;
      x.beginPath();x.arc(p.x,p.y,p.r,0,Math.PI*2);
      x.fillStyle=`rgba(200,255,0,${p.a*.32})`;x.fill();
    });
    for(let i=0;i<P.length;i++)for(let j=i+1;j<P.length;j++){
      const dx=P[i].x-P[j].x,dy=P[i].y-P[j].y,d=Math.sqrt(dx*dx+dy*dy);
      if(d<115){x.beginPath();x.moveTo(P[i].x,P[i].y);x.lineTo(P[j].x,P[j].y);x.strokeStyle=`rgba(200,255,0,${(1-d/115)*.04})`;x.lineWidth=.5;x.stroke();}
    }
    requestAnimationFrame(drw);
  };
  addEventListener('resize',()=>{rsz();ini();});
  rsz();ini();drw();
})();

/* REVEAL */
const ro=new IntersectionObserver(e=>{e.forEach(v=>{if(v.isIntersecting){v.target.classList.add('in');ro.unobserve(v.target);}});},{threshold:.1});
document.querySelectorAll('.rv').forEach(el=>ro.observe(el));

/* STATE */
let cart = <?= json_encode(array_values(array_map(function($item) {
    return [
        'cart_id' => $item['id'] . '_' . $item['quantite'],
        'id'    => (int)$item['id'],
        'name'  => $item['nom'],
        'price' => (float)$item['prix'] * (int)$item['quantite'],
        'orig'  => (float)$item['prix'],
        'img'   => $item['image'] ?? 'images/placeholder.jpg',
        'qty'   => (int)$item['quantite'],
        'disc'  => 0
    ];
}, $cartData))) ?>;
let spinsLeft = 3, curDisc = 0;

/* NAV */
function nav(id){document.querySelectorAll('.page').forEach(p=>p.style.display='none');document.getElementById(id).style.display='block';if(id==='cart')renderCart();scrollTo({top:0,behavior:'smooth'});}
function act(b){document.querySelectorAll('.ntabs button').forEach(x=>x.classList.remove('act'));b.classList.add('act');}
function selPay(el, mode){document.querySelectorAll('.pm').forEach(m=>m.classList.remove('sel'));el.classList.add('sel');document.getElementById('modePaiement').value=mode;}

/* CART */
function addCart(name,price,orig,img){
  let fp=price;if(curDisc>0)fp=Math.round(price*(1-curDisc/100));
  cart.push({name,price:fp,orig,img,id:Date.now(),disc:curDisc});
  updateN();notify('✓',name+' ajouté'+(curDisc>0?' (−'+curDisc+'%)':''));
}
function rmCart(id){window.location='Ajouterpanier.php?remove='+id;}
function updateN(){document.getElementById('cartCount').textContent=cart.length;}
function renderCart(){
  const box=document.getElementById('cartItems'),sum=document.getElementById('cartSum');
  if(!cart.length){
    box.innerHTML=`<div class="empty"><div class="eico">🛒</div><p style="margin-bottom:1.4rem">Votre panier est vide.</p><button class="btn-wire" style="padding:.75rem 1.9rem;cursor:pointer;border-radius:50px;" onclick="nav('shop')">← Continuer mes achats</button></div>`;
    sum.style.display='none';return;
  }
  box.innerHTML='';let tot=0;
  cart.forEach(it=>{
    tot+=it.price;const d=document.createElement('div');d.className='ci';
    const qtyHtml = it.qty > 1 ? `<div style="color:var(--muted);font-size:.75rem;margin-top:3px;">Qté: ${it.qty} × ${it.orig.toLocaleString()} Ar</div>` : '';
      d.innerHTML=`<img src="${it.img}" alt="${it.name}" onerror="this.src='images/placeholder.jpg'">
      <div class="ci-info"><div class="ci-name">${it.name}</div><div class="ci-price">${it.price.toLocaleString()} Ar</div>${qtyHtml}</div>
      <button class="btnrm" onclick="rmCart(${it.id})">Retirer</button>`;
    box.appendChild(d);
  });
  document.getElementById('cartTotal').textContent=tot.toLocaleString()+' Ar';
  sum.style.display='block';
}

/* RECLAMATION */
document.getElementById('rPhotos').addEventListener('change',function(){document.getElementById('rPinfo').textContent=this.files.length?`${this.files.length} photo(s) sélectionnée(s)`:''});
function subRecl(){
  const n=document.getElementById('rNom').value,e=document.getElementById('rEmail').value,c=document.getElementById('rCmd').value,t=document.getElementById('rType').value,d=document.getElementById('rDesc').value;
  if(!n||!e||!c||!t||!d){notify('!','Veuillez remplir tous les champs');return;}
  document.getElementById('rForm').style.display='none';document.getElementById('rOk').style.display='block';
  setTimeout(()=>{nav('shop');document.getElementById('rForm').style.display='block';document.getElementById('rOk').style.display='none';notify('✓','Réclamation envoyée !');},3500);
}

/* WHEEL */
function spinWheel(){
  if(spinsLeft<=0){notify('!','Plus de tentatives disponibles');return;}
  const btn=document.getElementById('spinBtn');btn.disabled=true;btn.textContent='Rotation…';
  let n=0;const iv=setInterval(()=>{
    if(++n>16){
      clearInterval(iv);
      const won=[5,10,15,20,25,30,50][Math.floor(Math.random()*7)];
      curDisc=won;document.getElementById('wPct').textContent=won+'% off !';
      const wc=document.getElementById('wCode');wc.textContent='NJAKA'+won+'DROP09';wc.style.display='block';
      document.getElementById('wRes').style.display='block';
      spinsLeft--;document.getElementById('spinsLeft').textContent=spinsLeft;
      notify('🎉',won+'% de réduction gagné !');
      setTimeout(()=>{btn.disabled=spinsLeft<=0;btn.textContent=spinsLeft>0?'Tourner à nouveau':'Terminé';},700);
    }
  },95);
}

/* TIMERS */
function startCD(){let t=3600;const el=document.getElementById('countdown');setInterval(()=>{if(--t<=0){el.textContent='Terminé';return;}el.textContent=String(Math.floor(t/60)).padStart(2,'0')+':'+String(t%60).padStart(2,'0');},1000);}
function startFlash(){let t=900;const el=document.getElementById('flashTimer');setInterval(()=>{if(--t<=0){el.textContent='Expiré';el.style.opacity='.35';return;}el.textContent=String(Math.floor(t/60)).padStart(2,'0')+':'+String(t%60).padStart(2,'0');},1000);}

/* NOTIFY */
function notify(icon,msg){
  const el=document.createElement('div');el.className='notif';
  el.innerHTML=`<span>${icon}</span><span>${msg}</span>`;
  document.body.appendChild(el);
  setTimeout(()=>{el.style.transition='opacity .28s,transform .28s';el.style.opacity='0';el.style.transform='translateX(38px)';setTimeout(()=>el.remove(),280);},3000);
}

startCD();startFlash();updateN();
if(cart.length)renderCart();
</script>
</body>
</html>