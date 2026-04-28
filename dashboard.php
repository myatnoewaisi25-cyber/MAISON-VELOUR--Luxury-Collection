<?php
// ═══════════════════════════════════════
//  MAISON VELOUR — Member Dashboard
// ═══════════════════════════════════════
session_start();

// Auth guard
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$member_name   = $_SESSION['name']             ?? 'Member';
$member_email  = $_SESSION['user']             ?? '';
$member_since  = 'March 2022';
$member_tier   = 'OBSIDIAN';
$loyalty_pts   = 4820;
$pts_to_next   = 5000;

$orders = [
    ['id'=>'MV-20251','date'=>'12 Apr 2025','item'=>'Riviera Tote in Cognac',  'status'=>'delivered','total'=>'$1,240'],
    ['id'=>'MV-20248','date'=>'01 Apr 2025','item'=>'Satin Mule in Ivory',     'status'=>'shipped',  'total'=>'$680'],
    ['id'=>'MV-20231','date'=>'14 Mar 2025','item'=>'Velvet Evening Clutch',   'status'=>'delivered','total'=>'$490'],
    ['id'=>'MV-20219','date'=>'02 Feb 2025','item'=>'Cashmere Longcoat Noir',  'status'=>'delivered','total'=>'$3,800'],
];

$wishlist = [
    ['item'=>'Satin Mule in Ivory',    'price'=>'$680',   'tag'=>'Limited'],
    ['item'=>'Riviera Tote in Cognac', 'price'=>'$1,240', 'tag'=>'New'],
];

$addresses = [
    ['label'=>'Home',  'line1'=>'14 Rue du Faubourg Saint-Honoré','line2'=>'75008 Paris, France',    'default'=>true],
    ['label'=>'Office','line1'=>'One Fashion Avenue, Suite 1200', 'line2'=>'New York, NY 10018, USA','default'=>false],
];

$status_colors = ['delivered'=>'#27ae60','shipped'=>'#B8963E','processing'=>'#8A8278','cancelled'=>'#c0392b'];
$status_icons  = ['delivered'=>'✓','shipped'=>'◈','processing'=>'◇','cancelled'=>'✕'];

$pts_pct = round(($loyalty_pts / $pts_to_next) * 100);
// Avatar initials
$name_parts = explode(' ', $member_name);
$initials   = '';
foreach ($name_parts as $p) $initials .= strtoupper(substr($p,0,1));
$initials = substr($initials, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAISON VELOUR — My Account</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Josefin+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{
  --cream:#F7F3EE;--warm-white:#FAF8F5;--gold:#B8963E;
  --gold-light:#D4AF6A;--gold-pale:#E8D9B5;--charcoal:#1C1A18;
  --stone:#8A8278;--mist:#C9C2B8;--dark-bg:#111009;
  --sidebar-w:280px;
}
html{scroll-behavior:smooth;}
body{font-family:'Josefin Sans',sans-serif;background:var(--warm-white);color:var(--charcoal);cursor:none;overflow-x:hidden;}

/* CURSOR */
.cursor{position:fixed;width:8px;height:8px;background:var(--gold);border-radius:50%;pointer-events:none;z-index:9999;transform:translate(-50%,-50%);transition:width .3s,height .3s;}
.cursor-ring{position:fixed;width:32px;height:32px;border:1px solid var(--gold);border-radius:50%;pointer-events:none;z-index:9998;transform:translate(-50%,-50%);transition:left .12s ease,top .12s ease;}

/* TOAST */
.toast-container{position:fixed;top:24px;right:24px;z-index:9000;display:flex;flex-direction:column;gap:10px;}
.toast{background:var(--charcoal);color:var(--cream);padding:14px 20px;font-size:10px;letter-spacing:.15em;min-width:260px;border-left:3px solid var(--gold);display:flex;align-items:center;gap:12px;transform:translateX(120%);opacity:0;transition:transform .4s cubic-bezier(.34,1.56,.64,1),opacity .3s;}
.toast.show{transform:translateX(0);opacity:1;}
.toast-icon{color:var(--gold);font-size:14px;}

/* TOPBAR */
.topbar{
  position:fixed;top:0;left:0;right:0;height:64px;
  background:rgba(247,243,238,.97);backdrop-filter:blur(14px);
  border-bottom:1px solid var(--gold-pale);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 40px;z-index:200;
}
.topbar-logo{font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:300;letter-spacing:.3em;color:var(--charcoal);text-decoration:none;}
.topbar-logo span{color:var(--gold);}
.topbar-right{display:flex;align-items:center;gap:24px;}
.topbar-greeting{font-size:9px;letter-spacing:.2em;color:var(--stone);}
.topbar-greeting strong{color:var(--charcoal);font-weight:400;}
.topbar-btn{background:none;border:none;cursor:pointer;font-family:'Josefin Sans',sans-serif;font-size:9px;letter-spacing:.2em;text-transform:uppercase;color:var(--stone);padding:8px 16px;transition:color .2s;text-decoration:none;display:inline-flex;align-items:center;}
.topbar-btn:hover{color:var(--charcoal);}
.topbar-store{padding:10px 22px;border:1px solid var(--gold);color:var(--gold) !important;transition:background .3s,color .3s !important;}
.topbar-store:hover{background:var(--gold) !important;color:var(--warm-white) !important;}

/* LAYOUT */
.layout{display:flex;min-height:100vh;padding-top:64px;}

/* SIDEBAR */
.sidebar{
  width:var(--sidebar-w);flex-shrink:0;
  background:var(--dark-bg);
  position:sticky;top:64px;height:calc(100vh - 64px);
  overflow-y:auto;display:flex;flex-direction:column;
}
.sidebar-profile{padding:36px 28px 20px;border-bottom:1px solid rgba(184,150,62,.12);}
.avatar{
  width:56px;height:56px;border-radius:50%;
  background:linear-gradient(135deg,#3A2C18,#6A5030);
  border:1px solid rgba(184,150,62,.4);
  display:flex;align-items:center;justify-content:center;
  font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:300;
  color:var(--gold-light);margin-bottom:14px;letter-spacing:.05em;
}
.sidebar-name{font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:300;color:var(--cream);margin-bottom:4px;line-height:1.2;}
.sidebar-email{font-size:8px;letter-spacing:.12em;color:var(--stone);margin-bottom:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.tier-badge{display:inline-flex;align-items:center;gap:8px;padding:5px 12px;border:1px solid rgba(184,150,62,.35);font-size:7px;letter-spacing:.3em;color:var(--gold);text-transform:uppercase;}
.tier-dot{width:6px;height:6px;background:var(--gold);border-radius:50%;}

/* Sidebar Clock */
.sidebar-clock{
  margin-top:14px;
  font-family:'Cormorant Garamond',serif;
  font-size:24px;font-weight:300;letter-spacing:.12em;
  color:var(--gold-light);padding:10px 0;
  border-top:1px solid rgba(184,150,62,.2);
  border-bottom:1px solid rgba(184,150,62,.2);
  text-align:center;
}

/* Points bar */
.pts-wrap{padding:16px 28px 20px;border-bottom:1px solid rgba(184,150,62,.1);}
.pts-row{display:flex;justify-content:space-between;font-size:8px;letter-spacing:.15em;color:var(--stone);margin-bottom:8px;}
.pts-num{color:var(--gold-light);font-size:11px;letter-spacing:.05em;}
.pts-bar-bg{height:3px;background:rgba(255,255,255,.08);border-radius:2px;}
.pts-bar-fill{height:3px;background:linear-gradient(to right,var(--gold),var(--gold-light));border-radius:2px;width:<?= $pts_pct ?>%;}
.pts-label{font-size:7px;letter-spacing:.15em;color:var(--stone);margin-top:6px;}

/* Nav */
.sidebar-nav{flex:1;padding:20px 0;}
.nav-section-title{font-size:7px;letter-spacing:.35em;text-transform:uppercase;color:rgba(138,130,120,.4);padding:12px 28px 6px;}
.nav-item{
  display:flex;align-items:center;gap:14px;
  padding:12px 28px;font-size:9px;letter-spacing:.2em;text-transform:uppercase;
  color:var(--stone);cursor:pointer;transition:color .25s,background .25s;
  border:none;background:none;width:100%;text-align:left;position:relative;
}
.nav-item:hover{color:var(--cream);background:rgba(255,255,255,.04);}
.nav-item.active{color:var(--gold);background:rgba(184,150,62,.08);}
.nav-item.active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:2px;background:var(--gold);}
.nav-icon{font-size:13px;width:18px;text-align:center;flex-shrink:0;}
.nav-badge{margin-left:auto;background:var(--gold);color:var(--dark-bg);font-size:7px;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;}

.sidebar-logout{padding:20px 28px;border-top:1px solid rgba(184,150,62,.1);}
.logout-btn{width:100%;padding:12px;border:1px solid rgba(138,130,120,.2);background:none;cursor:pointer;font-family:'Josefin Sans',sans-serif;font-size:8px;letter-spacing:.25em;text-transform:uppercase;color:var(--stone);transition:border-color .2s,color .2s;}
.logout-btn:hover{border-color:rgba(192,57,43,.4);color:#c0392b;}

/* MAIN */
.main{flex:1;overflow-x:hidden;}
.panel{display:none;padding:48px 52px;animation:panel-in .4s ease;}
.panel.active{display:block;}
@keyframes panel-in{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

.page-header{margin-bottom:44px;}
.page-label{font-size:8px;letter-spacing:.4em;text-transform:uppercase;color:var(--gold);margin-bottom:10px;display:flex;align-items:center;gap:12px;}
.page-label::before{content:'';width:24px;height:1px;background:var(--gold);}
.page-title{font-family:'Cormorant Garamond',serif;font-size:clamp(32px,3vw,44px);font-weight:300;color:var(--charcoal);line-height:1.1;}
.page-title em{font-style:italic;color:var(--stone);}

/* OVERVIEW */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:48px;}
.stat-card{background:var(--cream);border:1px solid var(--gold-pale);padding:28px 24px;position:relative;overflow:hidden;transition:border-color .3s;}
.stat-card:hover{border-color:var(--gold);}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:var(--gold);transform:scaleX(0);transition:transform .35s;transform-origin:left;}
.stat-card:hover::after{transform:scaleX(1);}
.stat-icon{font-size:20px;color:var(--gold);margin-bottom:16px;display:block;}
.stat-val{font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:300;color:var(--charcoal);line-height:1;margin-bottom:6px;}
.stat-label{font-size:8px;letter-spacing:.25em;text-transform:uppercase;color:var(--stone);}

.section-hd{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.section-hd h3{font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:300;}
.section-hd-link{font-size:8px;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);background:none;border:none;cursor:pointer;transition:opacity .2s;}
.section-hd-link:hover{opacity:.7;}

/* ORDER TABLE */
.order-table{width:100%;border-collapse:collapse;}
.order-table th{font-size:7px;letter-spacing:.3em;text-transform:uppercase;color:var(--stone);padding:10px 16px;text-align:left;border-bottom:1px solid var(--gold-pale);}
.order-table td{padding:18px 16px;border-bottom:1px solid rgba(232,217,181,.4);vertical-align:middle;}
.order-table tr:last-child td{border-bottom:none;}
.order-table tr:hover td{background:rgba(184,150,62,.03);}
.order-item-name{font-family:'Cormorant Garamond',serif;font-size:15px;font-weight:400;}
.order-item-brand{font-size:7px;letter-spacing:.25em;color:var(--stone);margin-bottom:3px;}
.order-id{font-size:9px;letter-spacing:.1em;color:var(--stone);}
.order-date{font-size:9px;color:var(--stone);}
.order-total{font-size:13px;letter-spacing:.08em;}
.status-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;font-size:7px;letter-spacing:.2em;text-transform:uppercase;}
.order-action{font-size:8px;letter-spacing:.15em;text-transform:uppercase;background:none;border:1px solid var(--gold-pale);cursor:pointer;padding:7px 14px;color:var(--stone);transition:border-color .2s,color .2s;}
.order-action:hover{border-color:var(--gold);color:var(--gold);}

/* WISHLIST */
.wishlist-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;}
.wish-card{background:var(--cream);border:1px solid var(--gold-pale);position:relative;overflow:hidden;transition:border-color .3s;}
.wish-card:hover{border-color:var(--gold);}
.wish-img{width:100%;aspect-ratio:3/4;overflow:hidden;position:relative;}
.wish-img-bg{width:100%;height:100%;transition:transform .6s;}
.wish-card:hover .wish-img-bg{transform:scale(1.05);}
.wish-tag{position:absolute;top:12px;left:12px;background:var(--gold);color:var(--dark-bg);font-size:7px;letter-spacing:.2em;padding:4px 9px;text-transform:uppercase;}
.wish-remove{position:absolute;top:12px;right:12px;width:28px;height:28px;background:rgba(247,243,238,.9);border:none;cursor:pointer;font-size:13px;color:var(--stone);display:flex;align-items:center;justify-content:center;transition:color .2s,background .2s;}
.wish-remove:hover{background:#c0392b;color:white;}
.wish-info{padding:16px 18px;}
.wish-brand{font-size:7px;letter-spacing:.25em;color:var(--stone);margin-bottom:4px;}
.wish-name{font-family:'Cormorant Garamond',serif;font-size:17px;font-weight:400;margin-bottom:8px;}
.wish-price{font-size:12px;letter-spacing:.08em;margin-bottom:14px;}
.wish-add{width:100%;padding:12px;background:var(--charcoal);border:none;cursor:pointer;font-family:'Josefin Sans',sans-serif;font-size:8px;letter-spacing:.25em;text-transform:uppercase;color:var(--cream);position:relative;overflow:hidden;}
.wish-add::before{content:'';position:absolute;inset:0;background:var(--gold);transform:translateX(-100%);transition:transform .4s cubic-bezier(.77,0,.18,1);}
.wish-add:hover::before{transform:translateX(0);}
.wish-add span{position:relative;z-index:1;}

/* SETTINGS */
.settings-grid{display:grid;grid-template-columns:1.6fr 1fr;gap:28px;align-items:start;}
.settings-card{background:var(--cream);border:1px solid var(--gold-pale);padding:36px;}
.settings-card-title{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:300;margin-bottom:28px;padding-bottom:16px;border-bottom:1px solid var(--gold-pale);display:flex;align-items:center;justify-content:space-between;}
.edit-toggle{font-size:8px;letter-spacing:.2em;text-transform:uppercase;background:none;border:none;cursor:pointer;color:var(--gold);}
.field{margin-bottom:20px;}
.field-label{font-size:7px;letter-spacing:.3em;text-transform:uppercase;color:var(--stone);display:block;margin-bottom:7px;}
.field-val{font-size:11px;letter-spacing:.1em;color:var(--charcoal);}
.field-input{width:100%;padding:13px 16px;background:var(--warm-white);border:1px solid var(--gold-pale);font-family:'Josefin Sans',sans-serif;font-size:11px;letter-spacing:.1em;color:var(--charcoal);outline:none;transition:border-color .3s;display:none;}
.field-input:focus{border-color:var(--gold);}
.settings-card.editing .field-val{display:none;}
.settings-card.editing .field-input{display:block;}
.save-btn{width:100%;margin-top:8px;padding:14px;background:var(--gold);border:none;cursor:pointer;font-family:'Josefin Sans',sans-serif;font-size:9px;letter-spacing:.3em;text-transform:uppercase;color:var(--dark-bg);display:none;transition:background .3s;}
.save-btn:hover{background:var(--gold-light);}
.settings-card.editing .save-btn{display:block;}

/* Addresses */
.addr-item{padding:18px 0;border-bottom:1px solid rgba(232,217,181,.5);}
.addr-item:last-child{border-bottom:none;}
.addr-top{display:flex;align-items:center;gap:10px;margin-bottom:6px;}
.addr-label-badge{font-size:7px;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);border:1px solid rgba(184,150,62,.3);padding:3px 8px;}
.addr-default{font-size:7px;letter-spacing:.15em;color:var(--stone);margin-left:auto;}
.addr-line{font-size:10px;letter-spacing:.08em;color:var(--charcoal);line-height:1.7;}
.addr-actions{display:flex;gap:10px;margin-top:10px;}
.addr-btn{font-size:7px;letter-spacing:.2em;text-transform:uppercase;background:none;border:none;cursor:pointer;color:var(--stone);padding:0;transition:color .2s;}
.addr-btn:hover{color:var(--gold);}
.addr-btn.del:hover{color:#c0392b;}

/* LOYALTY */
.loyalty-hero{background:var(--dark-bg);padding:48px 44px;margin-bottom:32px;position:relative;overflow:hidden;}
.loyalty-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 20% 60%,rgba(184,150,62,.15) 0%,transparent 60%);}
.loyalty-bg-text{position:absolute;right:-20px;top:50%;transform:translateY(-50%);font-family:'Cormorant Garamond',serif;font-size:140px;font-weight:300;color:rgba(184,150,62,.05);pointer-events:none;user-select:none;}
.loyalty-hero-inner{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;}
.loyalty-pts-big{font-family:'Cormorant Garamond',serif;font-size:72px;font-weight:300;color:var(--gold-light);line-height:1;}
.loyalty-pts-label{font-size:9px;letter-spacing:.3em;text-transform:uppercase;color:var(--stone);margin-top:6px;}
.loyalty-tier-info{text-align:right;}
.loyalty-tier-name{font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:300;color:var(--cream);margin-bottom:6px;}
.loyalty-tier-sub{font-size:8px;letter-spacing:.2em;color:var(--stone);}
.loyalty-bar-wrap{margin-top:32px;position:relative;z-index:1;}
.loyalty-bar-bg{height:4px;background:rgba(255,255,255,.1);border-radius:2px;}
.loyalty-bar-fill{height:4px;background:linear-gradient(to right,var(--gold),var(--gold-light));border-radius:2px;width:<?= $pts_pct ?>%;}
.loyalty-bar-labels{display:flex;justify-content:space-between;margin-top:8px;font-size:7px;letter-spacing:.2em;color:var(--stone);}
.perks-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
.perk-card{background:var(--cream);border:1px solid var(--gold-pale);padding:28px 20px;text-align:center;transition:border-color .3s;}
.perk-card:hover{border-color:var(--gold);}
.perk-icon{font-size:22px;color:var(--gold);margin-bottom:14px;display:block;}
.perk-name{font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:400;margin-bottom:6px;}
.perk-desc{font-size:8px;letter-spacing:.1em;line-height:1.8;color:var(--stone);}

/* Corner decorations */
.corner-deco{position:absolute;bottom:40px;right:40px;width:60px;height:60px;opacity:.12;border-right:1px solid var(--gold);border-bottom:1px solid var(--gold);pointer-events:none;}

/* Product placeholder colors */
.p-img-1{background:linear-gradient(135deg,#C4A882,#8B6914);}
.p-img-2{background:linear-gradient(135deg,#2C2C2C,#1A1A1A);}
.p-img-3{background:linear-gradient(135deg,#F0EDE8,#D4C9B8);}
.p-img-4{background:linear-gradient(135deg,#4A3060,#2A1840);}

@media(max-width:900px){
  .sidebar{display:none;}
  .panel{padding:28px 20px;}
  .stats-grid{grid-template-columns:repeat(2,1fr);}
  .wishlist-grid{grid-template-columns:repeat(2,1fr);}
  .settings-grid{grid-template-columns:1fr;}
  .perks-grid{grid-template-columns:repeat(2,1fr);}
  .topbar{padding:0 20px;}
}
</style>
</head>
<body>

<div class="cursor" id="cursor"></div>
<div class="cursor-ring" id="cursorRing"></div>

<div class="toast-container" id="toastContainer"></div>

<!-- ══ TOPBAR ══ -->
<header class="topbar">
  <a href="index.php" class="topbar-logo">MAISON <span>VELOUR</span></a>
  <div class="topbar-right">
    <span class="topbar-greeting">Welcome back, <strong><?= htmlspecialchars($member_name) ?></strong></span>
    <a href="index.php" class="topbar-btn topbar-store">← Back to Store</a>
    <a href="logout.php" class="topbar-btn">Sign Out</a>
  </div>
</header>

<!-- ══ LAYOUT ══ -->
<div class="layout">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar">
    <div class="sidebar-profile">
      <div class="avatar"><?= htmlspecialchars($initials) ?></div>
      <div class="sidebar-name"><?= htmlspecialchars($member_name) ?></div>
      <div class="sidebar-email"><?= htmlspecialchars($member_email) ?></div>
      <div class="tier-badge"><div class="tier-dot"></div><?= $member_tier ?></div>
      <div class="sidebar-clock" id="sidebarClock">--:--:--</div>
    </div>

    <div class="pts-wrap">
      <div class="pts-row">
        <span>Loyalty Points</span>
        <span class="pts-num"><?= number_format($loyalty_pts) ?></span>
      </div>
      <div class="pts-bar-bg"><div class="pts-bar-fill"></div></div>
      <div class="pts-label"><?= number_format($pts_to_next - $loyalty_pts) ?> pts to next tier</div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Account</div>
      <button class="nav-item active" onclick="showPanel('overview',this)">
        <span class="nav-icon">◈</span> Overview
      </button>
      <button class="nav-item" onclick="showPanel('orders',this)">
        <span class="nav-icon">◇</span> My Orders
        <span class="nav-badge"><?= count($orders) ?></span>
      </button>
      <button class="nav-item" onclick="showPanel('wishlist',this)">
        <span class="nav-icon">♡</span> Wishlist
        <span class="nav-badge"><?= count($wishlist) ?></span>
      </button>
      <div class="nav-section-title">Membership</div>
      <button class="nav-item" onclick="showPanel('loyalty',this)">
        <span class="nav-icon">✦</span> Loyalty & Perks
      </button>
      <button class="nav-item" onclick="showPanel('settings',this)">
        <span class="nav-icon">◎</span> Settings
      </button>
    </nav>

    <div class="sidebar-logout">
      <button class="logout-btn" onclick="window.location='logout.php'">Sign Out</button>
    </div>
  </aside>

  <!-- ══ MAIN CONTENT ══ -->
  <main class="main">

    <!-- OVERVIEW PANEL -->
    <section class="panel active" id="panel-overview">
      <div class="page-header">
        <div class="page-label">Dashboard</div>
        <h1 class="page-title">Good to see you, <em><?= htmlspecialchars($member_name) ?></em></h1>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-icon">◆</span>
          <div class="stat-val"><?= number_format($loyalty_pts) ?></div>
          <div class="stat-label">Loyalty Points</div>
        </div>
        <div class="stat-card">
          <span class="stat-icon">◇</span>
          <div class="stat-val"><?= count($orders) ?></div>
          <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
          <span class="stat-icon">♡</span>
          <div class="stat-val"><?= count($wishlist) ?></div>
          <div class="stat-label">Saved Items</div>
        </div>
        <div class="stat-card">
          <span class="stat-icon">◈</span>
          <div class="stat-val"><?= $member_tier ?></div>
          <div class="stat-label">Member Tier</div>
        </div>
      </div>

      <div class="section-hd">
        <h3>Recent Orders</h3>
        <button class="section-hd-link" onclick="showPanel('orders', document.querySelector('[onclick*=orders]'))">View All →</button>
      </div>
      <table class="order-table">
        <thead>
          <tr>
            <th>Order</th><th>Date</th><th>Item</th><th>Status</th><th>Total</th><th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach(array_slice($orders,0,3) as $o): ?>
          <tr>
            <td><div class="order-id"><?= $o['id'] ?></div></td>
            <td><div class="order-date"><?= $o['date'] ?></div></td>
            <td>
              <div class="order-item-brand">MAISON VELOUR</div>
              <div class="order-item-name"><?= htmlspecialchars($o['item']) ?></div>
            </td>
            <td>
              <div class="status-pill" style="background:<?= $status_colors[$o['status']] ?>18;color:<?= $status_colors[$o['status']] ?>;">
                <?= $status_icons[$o['status']] ?> <?= ucfirst($o['status']) ?>
              </div>
            </td>
            <td><div class="order-total"><?= $o['total'] ?></div></td>
            <td><button class="order-action" onclick="toast('Tracking info sent to your email.')">Track</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- ORDERS PANEL -->
    <section class="panel" id="panel-orders">
      <div class="page-header">
        <div class="page-label">History</div>
        <h1 class="page-title">My <em>Orders</em></h1>
      </div>
      <table class="order-table">
        <thead>
          <tr><th>Order</th><th>Date</th><th>Item</th><th>Status</th><th>Total</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach($orders as $o): ?>
          <tr>
            <td><div class="order-id"><?= $o['id'] ?></div></td>
            <td><div class="order-date"><?= $o['date'] ?></div></td>
            <td>
              <div class="order-item-brand">MAISON VELOUR</div>
              <div class="order-item-name"><?= htmlspecialchars($o['item']) ?></div>
            </td>
            <td>
              <div class="status-pill" style="background:<?= $status_colors[$o['status']] ?>18;color:<?= $status_colors[$o['status']] ?>;">
                <?= $status_icons[$o['status']] ?> <?= ucfirst($o['status']) ?>
              </div>
            </td>
            <td><div class="order-total"><?= $o['total'] ?></div></td>
            <td><button class="order-action" onclick="toast('Tracking info sent to your email.')">Track</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- WISHLIST PANEL -->
    <section class="panel" id="panel-wishlist">
      <div class="page-header">
        <div class="page-label">Saved Items</div>
        <h1 class="page-title">My <em>Wishlist</em></h1>
      </div>
      <div class="wishlist-grid" id="wishlistGrid">
        <?php foreach($wishlist as $i => $w): ?>
        <div class="wish-card" id="wish-<?= $i ?>">
          <div class="wish-img">
            <div class="wish-img-bg p-img-<?= ($i%4)+1 ?>"></div>
            <div class="wish-tag"><?= $w['tag'] ?></div>
            <button class="wish-remove" onclick="removeWish(<?= $i ?>)">✕</button>
          </div>
          <div class="wish-info">
            <div class="wish-brand">MAISON VELOUR</div>
            <div class="wish-name"><?= htmlspecialchars($w['item']) ?></div>
            <div class="wish-price"><?= $w['price'] ?></div>
            <button class="wish-add" onclick="toast('Added to cart — <?= htmlspecialchars($w['item']) ?>')"><span>Add to Cart</span></button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- LOYALTY PANEL -->
    <section class="panel" id="panel-loyalty">
      <div class="page-header">
        <div class="page-label">Rewards</div>
        <h1 class="page-title">Loyalty <em>& Perks</em></h1>
      </div>

      <div class="loyalty-hero">
        <div class="loyalty-bg-text">VELOUR</div>
        <div class="loyalty-hero-inner">
          <div>
            <div class="loyalty-pts-big"><?= number_format($loyalty_pts) ?></div>
            <div class="loyalty-pts-label">Available Points</div>
          </div>
          <div class="loyalty-tier-info">
            <div class="loyalty-tier-name"><?= $member_tier ?></div>
            <div class="loyalty-tier-sub">Current Tier · Member since <?= $member_since ?></div>
          </div>
        </div>
        <div class="loyalty-bar-wrap">
          <div class="loyalty-bar-bg"><div class="loyalty-bar-fill"></div></div>
          <div class="loyalty-bar-labels">
            <span><?= number_format($loyalty_pts) ?> pts</span>
            <span><?= number_format($pts_to_next - $loyalty_pts) ?> pts to next tier</span>
            <span><?= number_format($pts_to_next) ?> pts</span>
          </div>
        </div>
      </div>

      <div class="section-hd" style="margin-bottom:20px;">
        <h3>Member Perks</h3>
      </div>
      <div class="perks-grid">
        <div class="perk-card">
          <span class="perk-icon">✦</span>
          <div class="perk-name">Early Access</div>
          <div class="perk-desc">First look at new collections before public release.</div>
        </div>
        <div class="perk-card">
          <span class="perk-icon">◈</span>
          <div class="perk-name">Private Styling</div>
          <div class="perk-desc">Complimentary sessions with our personal stylists.</div>
        </div>
        <div class="perk-card">
          <span class="perk-icon">◇</span>
          <div class="perk-name">Lifetime Care</div>
          <div class="perk-desc">Full repair and restoration on every piece.</div>
        </div>
        <div class="perk-card">
          <span class="perk-icon">⬡</span>
          <div class="perk-name">Members Rewards</div>
          <div class="perk-desc">Earn and redeem points with every purchase.</div>
        </div>
      </div>
    </section>

    <!-- SETTINGS PANEL -->
    <section class="panel" id="panel-settings">
      <div class="page-header">
        <div class="page-label">Account</div>
        <h1 class="page-title">My <em>Settings</em></h1>
      </div>

      <div class="settings-grid">
        <div class="settings-card" id="profileCard">
          <div class="settings-card-title">
            Profile Information
            <button class="edit-toggle" onclick="toggleEdit('profileCard',this)">Edit</button>
          </div>
          <div class="field">
            <label class="field-label">Full Name</label>
            <div class="field-val"><?= htmlspecialchars($member_name) ?></div>
            <input class="field-input" type="text" value="<?= htmlspecialchars($member_name) ?>">
          </div>
          <div class="field">
            <label class="field-label">Email Address</label>
            <div class="field-val"><?= htmlspecialchars($member_email) ?></div>
            <input class="field-input" type="email" value="<?= htmlspecialchars($member_email) ?>">
          </div>
          <div class="field">
            <label class="field-label">Member Since</label>
            <div class="field-val"><?= $member_since ?></div>
            <input class="field-input" type="text" value="<?= $member_since ?>">
          </div>
          <button class="save-btn" onclick="toast('Profile updated successfully.');toggleEdit('profileCard', document.querySelector('#profileCard .edit-toggle'))">Save Changes</button>
        </div>

        <div class="settings-card">
          <div class="settings-card-title">Saved Addresses</div>
          <?php foreach($addresses as $a): ?>
          <div class="addr-item">
            <div class="addr-top">
              <div class="addr-label-badge"><?= $a['label'] ?></div>
              <?php if($a['default']): ?><div class="addr-default">Default</div><?php endif; ?>
            </div>
            <div class="addr-line"><?= htmlspecialchars($a['line1']) ?><br><?= htmlspecialchars($a['line2']) ?></div>
            <div class="addr-actions">
              <button class="addr-btn" onclick="toast('Edit address — coming soon.')">Edit</button>
              <button class="addr-btn del" onclick="toast('Address removed.')">Remove</button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  </main>
</div>

<script>
// Cursor
const cursor = document.getElementById('cursor');
const ring   = document.getElementById('cursorRing');
document.addEventListener('mousemove', e => {
  cursor.style.left = e.clientX + 'px';
  cursor.style.top  = e.clientY + 'px';
  ring.style.left   = e.clientX + 'px';
  ring.style.top    = e.clientY + 'px';
});

// Live clock
function updateClock() {
  const now = new Date();
  const h = String(now.getHours()).padStart(2,'0');
  const m = String(now.getMinutes()).padStart(2,'0');
  const s = String(now.getSeconds()).padStart(2,'0');
  const el = document.getElementById('sidebarClock');
  if (el) el.textContent = h + ':' + m + ':' + s;
}
updateClock();
setInterval(updateClock, 1000);

// Panel switching
function showPanel(id, btn) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('panel-' + id).classList.add('active');
  if (btn) btn.classList.add('active');
}

// Toast
function toast(msg) {
  const container = document.getElementById('toastContainer');
  const el = document.createElement('div');
  el.className = 'toast';
  el.innerHTML = '<span class="toast-icon">◆</span><span>' + msg + '</span>';
  container.appendChild(el);
  setTimeout(() => el.classList.add('show'), 10);
  setTimeout(() => {
    el.classList.remove('show');
    setTimeout(() => el.remove(), 400);
  }, 3000);
}

// Edit toggle
function toggleEdit(cardId, btn) {
  const card = document.getElementById(cardId);
  if (card) {
    card.classList.toggle('editing');
    btn.textContent = card.classList.contains('editing') ? 'Cancel' : 'Edit';
  }
}

// Remove wishlist item
function removeWish(id) {
  const el = document.getElementById('wish-' + id);
  if (el) {
    el.style.opacity = '0';
    el.style.transform = 'scale(.95)';
    el.style.transition = 'all .3s';
    setTimeout(() => el.remove(), 300);
    toast('Item removed from wishlist.');
  }
}
</script>
</body>
</html>