<?php
require_once 'includes/config.php';
// Fetch available rooms for display
$rooms = $conn->query("SELECT * FROM rooms WHERE status='available' ORDER BY room_number");
$total_rooms = $conn->query("SELECT COUNT(*) c FROM rooms")->fetch_assoc()['c'];
$avail_rooms = $conn->query("SELECT COUNT(*) c FROM rooms WHERE status='available'")->fetch_assoc()['c'];
$total_students = $conn->query("SELECT COUNT(*) c FROM students WHERE status='active'")->fetch_assoc()['c'];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>HMS - Hostel Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --pri:#1e3a5f;--pri2:#2563a8;--acc:#10b981;--warn:#f97316;
  --dark:#1f2937;--grey:#64748b;--light:#f8fafc;--white:#fff;
  --border:#e2e8f0;
}
body{font-family:'Nunito',sans-serif;color:var(--dark);overflow-x:hidden;}
a{text-decoration:none;color:inherit;}

/* ============ NAVBAR ============ */
.navbar{
  position:fixed;top:0;left:0;right:0;z-index:1000;
  background:rgba(30,58,95,0.95);backdrop-filter:blur(10px);
  padding:0 5%;height:68px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 20px rgba(0,0,0,0.3);
  transition:all .3s;
}
.nav-brand{display:flex;align-items:center;gap:10px;}
.nav-brand-ico{
  width:40px;height:40px;background:#f59e0b;border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;color:var(--pri);
}
.nav-brand-txt{font-family:'Poppins',sans-serif;font-size:20px;font-weight:800;color:#fff;}
.nav-links{display:flex;align-items:center;gap:6px;}
.nav-links a{
  color:rgba(255,255,255,0.8);font-weight:700;font-size:13.5px;
  padding:8px 15px;border-radius:8px;transition:all .2s;
}
.nav-links a:hover,.nav-links a.active{background:rgba(255,255,255,0.12);color:#fff;}
.nav-cta{
  background:#f59e0b;color:var(--pri)!important;font-weight:800!important;
  padding:9px 18px!important;border-radius:8px!important;
}
.nav-cta:hover{background:#fbbf24!important;color:var(--pri)!important;}
.nav-hamburger{display:none;background:none;border:none;color:#fff;font-size:22px;cursor:pointer;}

/* ============ HERO ============ */
.hero{
  min-height:100vh;
  background:linear-gradient(135deg,#0f2240 0%,#1e3a5f 40%,#1a4a7a 70%,#0d3464 100%);
  position:relative;overflow:hidden;
  display:flex;align-items:center;padding:100px 5% 60px;
}
/* Mesh gradient orbs */
.hero::before{
  content:'';position:absolute;top:-20%;right:-10%;
  width:600px;height:600px;border-radius:50%;
  background:radial-gradient(circle,rgba(37,99,168,0.5) 0%,transparent 70%);
  pointer-events:none;
}
.hero::after{
  content:'';position:absolute;bottom:-15%;left:-5%;
  width:500px;height:500px;border-radius:50%;
  background:radial-gradient(circle,rgba(16,185,129,0.25) 0%,transparent 70%);
  pointer-events:none;
}
.hero-float-1{
  position:absolute;top:15%;left:60%;width:300px;height:300px;border-radius:50%;
  background:radial-gradient(circle,rgba(245,158,11,0.15) 0%,transparent 70%);
  animation:float1 8s ease-in-out infinite;
}
.hero-float-2{
  position:absolute;bottom:20%;right:5%;width:200px;height:200px;border-radius:50%;
  background:radial-gradient(circle,rgba(139,92,246,0.2) 0%,transparent 70%);
  animation:float2 6s ease-in-out infinite;
}
@keyframes float1{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-30px) scale(1.1);}}
@keyframes float2{0%,100%{transform:translateY(0);}50%{transform:translateY(20px);}}

/* Grid pattern overlay */
.hero-grid{
  position:absolute;inset:0;
  background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),
                   linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);
  background-size:50px 50px;
  pointer-events:none;
}

.hero-content{position:relative;z-index:2;max-width:600px;}
.hero-badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);
  color:#fbbf24;padding:6px 16px;border-radius:50px;font-size:12.5px;font-weight:700;
  margin-bottom:22px;letter-spacing:0.5px;
}
.hero-title{
  font-family:'Poppins',sans-serif;font-size:clamp(32px,5vw,58px);
  font-weight:900;color:#fff;line-height:1.15;margin-bottom:20px;
}
.hero-title span{
  background:linear-gradient(135deg,#f59e0b,#10b981);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.hero-sub{color:rgba(255,255,255,0.7);font-size:16px;line-height:1.7;margin-bottom:34px;max-width:480px;}
.hero-btns{display:flex;gap:14px;flex-wrap:wrap;}
.btn-hero-pri{
  background:linear-gradient(135deg,#f59e0b,#f97316);color:var(--pri);
  font-weight:800;font-size:15px;padding:13px 28px;border-radius:10px;
  display:inline-flex;align-items:center;gap:8px;
  box-shadow:0 4px 20px rgba(245,158,11,0.4);
  transition:all .2s;border:none;cursor:pointer;font-family:'Nunito',sans-serif;
}
.btn-hero-pri:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(245,158,11,0.5);}
.btn-hero-sec{
  background:rgba(255,255,255,0.1);color:#fff;border:1.5px solid rgba(255,255,255,0.3);
  font-weight:700;font-size:15px;padding:13px 28px;border-radius:10px;
  display:inline-flex;align-items:center;gap:8px;
  transition:all .2s;backdrop-filter:blur(5px);
}
.btn-hero-sec:hover{background:rgba(255,255,255,0.18);transform:translateY(-2px);}

.hero-stats{
  display:flex;gap:30px;margin-top:46px;flex-wrap:wrap;
}
.hero-stat{text-align:center;}
.hero-stat-num{font-family:'Poppins',sans-serif;font-size:30px;font-weight:900;color:#fff;}
.hero-stat-lbl{color:rgba(255,255,255,0.5);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;}
.hero-stat-div{width:1px;background:rgba(255,255,255,0.15);align-self:stretch;margin:0;}

.hero-visual{
  position:absolute;right:5%;top:50%;transform:translateY(-50%);
  z-index:2;display:none;
}
@media(min-width:1000px){.hero-visual{display:block;}}
.hero-card{
  background:rgba(255,255,255,0.08);backdrop-filter:blur(15px);
  border:1px solid rgba(255,255,255,0.15);border-radius:20px;
  padding:24px;width:300px;
}
.hero-card-title{color:#fff;font-weight:800;font-size:14px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.hero-room-item{
  background:rgba(255,255,255,0.06);border-radius:10px;padding:12px 14px;
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:10px;color:#fff;font-size:13px;
  border:1px solid rgba(255,255,255,0.08);
}
.hero-room-badge{
  padding:3px 10px;border-radius:20px;font-size:11px;font-weight:800;
  background:rgba(16,185,129,0.2);color:#6ee7b7;
}

/* ============ SECTIONS ============ */
section{padding:70px 5%;}
.section-badge{
  display:inline-flex;align-items:center;gap:7px;
  background:#dbeafe;color:#1e40af;
  font-size:12px;font-weight:800;padding:5px 14px;border-radius:50px;
  margin-bottom:14px;letter-spacing:0.5px;
}
.section-title{font-family:'Poppins',sans-serif;font-size:clamp(24px,3.5vw,36px);font-weight:900;color:var(--dark);margin-bottom:10px;}
.section-sub{color:var(--grey);font-size:15px;line-height:1.7;max-width:550px;}
.text-center{text-align:center;}.text-center .section-sub{margin:0 auto;}

/* ============ FEATURES ============ */
.features-section{background:#f8fafc;}
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:22px;margin-top:44px;}
.feature-card{
  background:#fff;border-radius:16px;padding:28px 24px;
  box-shadow:0 2px 15px rgba(0,0,0,0.06);
  border:1px solid var(--border);transition:all .25s;
}
.feature-card:hover{transform:translateY(-5px);box-shadow:0 10px 40px rgba(0,0,0,0.1);}
.feature-ico{
  width:54px;height:54px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;font-size:22px;
  margin-bottom:18px;
}
.fic-blue{background:#dbeafe;color:#2563eb;}
.fic-green{background:#d1fae5;color:#059669;}
.fic-orange{background:#ffedd5;color:#ea580c;}
.fic-purple{background:#ede9fe;color:#7c3aed;}
.fic-yellow{background:#fef3c7;color:#d97706;}
.fic-red{background:#fee2e2;color:#dc2626;}
.feature-card h3{font-size:15.5px;font-weight:800;margin-bottom:8px;}
.feature-card p{color:var(--grey);font-size:13.5px;line-height:1.65;}

/* ============ ROOMS ============ */
.rooms-section{background:#fff;}
.rooms-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:22px;margin-top:44px;}
.room-card{
  background:#fff;border-radius:16px;overflow:hidden;
  box-shadow:0 4px 20px rgba(0,0,0,0.08);border:1px solid var(--border);
  transition:all .25s;
}
.room-card:hover{transform:translateY(-6px);box-shadow:0 12px 40px rgba(0,0,0,0.13);}
.room-card-top{
  padding:24px 22px 18px;
  background:linear-gradient(135deg,#1e3a5f,#2563a8);
  position:relative;overflow:hidden;
}
.room-card-top::after{
  content:'';position:absolute;top:-20px;right:-20px;
  width:120px;height:120px;border-radius:50%;
  background:rgba(255,255,255,0.06);
}
.room-number{font-family:'Poppins',sans-serif;font-size:28px;font-weight:900;color:#fff;}
.room-floor{color:rgba(255,255,255,0.6);font-size:12px;margin-top:2px;}
.room-badge-wrap{position:absolute;top:18px;right:18px;}
.room-avail-badge{
  background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.4);
  color:#6ee7b7;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;
}
.room-full-badge{
  background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.4);
  color:#fca5a5;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:800;
}
.room-card-body{padding:20px 22px;}
.room-info-row{display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;}
.room-info-item{
  display:flex;align-items:center;gap:6px;
  background:#f8fafc;border-radius:8px;padding:6px 12px;
  font-size:12.5px;font-weight:700;color:var(--dark);flex:1;min-width:90px;
}
.room-info-item i{color:var(--pri2);font-size:12px;}
.room-price{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.room-price-amt{font-family:'Poppins',sans-serif;font-size:22px;font-weight:900;color:var(--pri);}
.room-price-lbl{color:var(--grey);font-size:11.5px;font-weight:700;}
.btn-book{
  width:100%;padding:11px;border-radius:10px;border:none;cursor:pointer;
  background:linear-gradient(135deg,var(--pri),var(--pri2));color:#fff;
  font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:all .2s;
}
.btn-book:hover{background:linear-gradient(135deg,var(--pri2),#1d4ed8);transform:translateY(-1px);}
.btn-book-dis{
  width:100%;padding:11px;border-radius:10px;border:none;
  background:#e2e8f0;color:#94a3b8;
  font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;
  display:flex;align-items:center;justify-content:center;gap:8px;cursor:not-allowed;
}
.no-rooms{text-align:center;padding:50px;color:var(--grey);font-size:15px;}

/* ============ VISIT REGISTER SECTION ============ */
.visit-section{background:linear-gradient(135deg,#f8fafc,#e8f4fd);}
.visit-inner{display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:center;}
@media(max-width:860px){.visit-inner{grid-template-columns:1fr;}}
.visit-img-side{position:relative;}
.visit-visual{
  background:linear-gradient(135deg,var(--pri),var(--pri2));
  border-radius:20px;padding:36px;
  color:#fff;
}
.visit-visual-title{font-family:'Poppins',sans-serif;font-size:20px;font-weight:800;margin-bottom:20px;}
.visit-step{display:flex;gap:14px;margin-bottom:18px;align-items:flex-start;}
.visit-step-num{
  width:34px;height:34px;border-radius:50%;
  background:rgba(245,158,11,0.2);border:2px solid rgba(245,158,11,0.4);
  color:#fbbf24;font-weight:900;font-size:14px;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.visit-step-txt h4{font-size:13.5px;font-weight:800;margin-bottom:3px;}
.visit-step-txt p{font-size:12px;color:rgba(255,255,255,0.65);line-height:1.5;}
.visit-cta-btn{
  display:inline-flex;align-items:center;gap:8px;
  background:#f59e0b;color:var(--pri);font-weight:800;font-size:14.5px;
  padding:13px 26px;border-radius:10px;margin-top:10px;transition:all .2s;
}
.visit-cta-btn:hover{background:#fbbf24;transform:translateY(-2px);}

/* ============ FOOTER ============ */
footer{background:var(--pri);color:rgba(255,255,255,0.7);text-align:center;padding:28px 5%;font-size:13px;}
footer a{color:#fbbf24;font-weight:700;}

/* ============ BOOKING MODAL ============ */
.modal-ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;}
.modal-ov.show{display:flex;}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
.modal-hdr{padding:22px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;border-radius:20px 20px 0 0;z-index:10;}
.modal-hdr h3{font-size:17px;font-weight:900;color:var(--dark);display:flex;align-items:center;gap:8px;}
.modal-hdr h3 i{color:var(--pri2);}
.modal-close{background:none;border:none;font-size:20px;cursor:pointer;color:var(--grey);width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:.2s;}
.modal-close:hover{background:#f1f5f9;color:var(--dark);}
.modal-bdy{padding:24px;}
.modal-ftr{padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}
.fg{margin-bottom:15px;}
.fg label{display:block;font-size:12.5px;font-weight:800;color:#374151;margin-bottom:5px;}
.fg label span.req{color:#ef4444;}
.fc{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:9px;font-size:13.5px;font-family:'Nunito',sans-serif;color:var(--dark);transition:.2s;outline:none;}
.fc:focus{border-color:var(--pri2);box-shadow:0 0 0 3px rgba(37,99,168,0.1);}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.btn-submit{background:linear-gradient(135deg,var(--pri),var(--pri2));color:#fff;border:none;padding:11px 24px;border-radius:9px;font-size:14px;font-weight:800;cursor:pointer;font-family:'Nunito',sans-serif;display:inline-flex;align-items:center;gap:7px;transition:.2s;}
.btn-submit:hover{opacity:.9;transform:translateY(-1px);}
.btn-cancel{background:#f1f5f9;color:#475569;border:none;padding:11px 20px;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;}
.btn-cancel:hover{background:#e2e8f0;}
.success-box{text-align:center;padding:30px 20px;}
.success-ico{font-size:60px;color:var(--acc);margin-bottom:16px;}
.success-box h3{font-size:20px;font-weight:900;color:var(--dark);margin-bottom:8px;}
.success-box p{color:var(--grey);font-size:14px;line-height:1.6;}
.alert-err{background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px;}

/* Scroll reveal */
.reveal{opacity:0;transform:translateY(30px);transition:all .6s ease;}
.reveal.visible{opacity:1;transform:translateY(0);}

/* ============ RESPONSIVE ============ */
@media(max-width:768px){
  .nav-links{display:none;}
  .nav-links.open{display:flex;flex-direction:column;position:fixed;top:68px;left:0;right:0;background:var(--pri);padding:16px;gap:4px;z-index:999;}
  .nav-hamburger{display:block;}
  .hero{padding:100px 5% 50px;}
  .hero-stats{gap:18px;}
  .fgrid{grid-template-columns:1fr;}
  section{padding:50px 5%;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
  <div class="nav-brand">
    <div class="nav-brand-ico"><i class="fas fa-building"></i></div>
    <span class="nav-brand-txt">HMS</span>
  </div>
  <div class="nav-links" id="navLinks">
    <a href="home.php" class="active"><i class="fas fa-home"></i> Home</a>
    <a href="#rooms"><i class="fas fa-door-open"></i> Rooms</a>
    <a href="visit_register.php"><i class="fas fa-clipboard-list"></i> Register Visit</a>
    <a href="login.php" class="nav-cta"><i class="fas fa-sign-in-alt"></i> Login</a>
  </div>
  <button class="nav-hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
</nav>

<!-- HERO -->
<section class="hero" id="home">
  <div class="hero-grid"></div>
  <div class="hero-float-1"></div>
  <div class="hero-float-2"></div>
  <div class="hero-content">
    <div class="hero-badge"><i class="fas fa-star"></i> Premium Hostel Management</div>
    <h1 class="hero-title">Your Home <span>Away From Home</span></h1>
    <p class="hero-sub">Modern, comfortable and secure hostel accommodation. Enjoy premium amenities, 24/7 security, and a vibrant community.</p>
    <div class="hero-btns">
      <a href="#rooms" class="btn-hero-pri"><i class="fas fa-door-open"></i> View Rooms</a>
      <a href="visit_register.php" class="btn-hero-sec"><i class="fas fa-clipboard-list"></i> Register Visit</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-num"><?=$total_rooms?>+</div>
        <div class="hero-stat-lbl">Total Rooms</div>
      </div>
      <div class="hero-stat-div"></div>
      <div class="hero-stat">
        <div class="hero-stat-num"><?=$avail_rooms?></div>
        <div class="hero-stat-lbl">Available Now</div>
      </div>
      <div class="hero-stat-div"></div>
      <div class="hero-stat">
        <div class="hero-stat-num"><?=$total_students?>+</div>
        <div class="hero-stat-lbl">Happy Students</div>
      </div>
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-card">
      <div class="hero-card-title"><i class="fas fa-bed"></i> Available Rooms</div>
      <?php
      $sample = $conn->query("SELECT room_number,room_type,monthly_rent FROM rooms WHERE status='available' LIMIT 4");
      while($r=$sample->fetch_assoc()):?>
      <div class="hero-room-item">
        <div>
          <div style="font-weight:800;font-size:14px;">Room <?=htmlspecialchars($r['room_number'])?></div>
          <div style="font-size:11px;color:rgba(255,255,255,0.5);"><?=$r['room_type']?> • ₹<?=number_format($r['monthly_rent'],0)?>/mo</div>
        </div>
        <div class="hero-room-badge">Available</div>
      </div>
      <?php endwhile;?>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features-section" id="features">
  <div class="text-center" style="margin-bottom:0;">
    <div class="section-badge" style="display:inline-flex;"><i class="fas fa-sparkles"></i> Why Choose Us</div>
    <h2 class="section-title">Everything You Need</h2>
    <p class="section-sub">We provide all the facilities and services that make hostel life comfortable and enjoyable.</p>
  </div>
  <div class="features-grid">
    <div class="feature-card reveal">
      <div class="feature-ico fic-blue"><i class="fas fa-shield-alt"></i></div>
      <h3>24/7 Security</h3>
      <p>Round-the-clock CCTV surveillance and security personnel ensure your complete safety inside the hostel premises.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feature-ico fic-green"><i class="fas fa-wifi"></i></div>
      <h3>High-Speed WiFi</h3>
      <p>Enjoy seamless high-speed internet connectivity in all rooms and common areas for your academic needs.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feature-ico fic-orange"><i class="fas fa-utensils"></i></div>
      <h3>Healthy Meals</h3>
      <p>Nutritious and hygienic meals served three times a day. Special diet options available on request.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feature-ico fic-purple"><i class="fas fa-broom"></i></div>
      <h3>Housekeeping</h3>
      <p>Regular room cleaning and maintenance services to ensure a clean and healthy living environment.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feature-ico fic-yellow"><i class="fas fa-book-open"></i></div>
      <h3>Study Rooms</h3>
      <p>Dedicated quiet study rooms and library area available 24 hours for focused academic preparation.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feature-ico fic-red"><i class="fas fa-first-aid"></i></div>
      <h3>Medical Support</h3>
      <p>On-call medical assistance and first-aid facilities available for emergencies any time of day or night.</p>
    </div>
  </div>
</section>

<!-- ROOMS -->
<section class="rooms-section" id="rooms">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:14px;margin-bottom:44px;">
    <div>
      <div class="section-badge"><i class="fas fa-door-open"></i> Accommodation</div>
      <h2 class="section-title">Available Rooms</h2>
      <p class="section-sub">Choose from our range of comfortable rooms. All rooms include basic furnishing and amenities.</p>
    </div>
  </div>
  <div class="rooms-grid">
    <?php
    $rooms->data_seek(0);
    if($rooms->num_rows === 0):?>
    <div class="no-rooms" style="grid-column:1/-1;">
      <i class="fas fa-door-closed" style="font-size:40px;margin-bottom:14px;display:block;color:#cbd5e1;"></i>
      No rooms available at the moment. Please check back later.
    </div>
    <?php else: while($r=$rooms->fetch_assoc()): 
      $icons=['2-bed'=>'fa-user-friends','3-bed'=>'fa-users','4-bed'=>'fa-users'];
      $ico=$icons[$r['room_type']]??'fa-bed';
    ?>
    <div class="room-card reveal">
      <div class="room-card-top">
        <div class="room-number">Room <?=htmlspecialchars($r['room_number'])?></div>
        <div class="room-floor"><i class="fas fa-layer-group"></i> <?=htmlspecialchars($r['floor_no'])?></div>
        <div class="room-badge-wrap"><span class="room-avail-badge"><i class="fas fa-circle" style="font-size:7px;"></i> Available</span></div>
      </div>
      <div class="room-card-body">
        <div class="room-info-row">
          <div class="room-info-item"><i class="fas <?=$ico?>"></i> <?=htmlspecialchars($r['room_type'])?></div>
          <div class="room-info-item"><i class="fas fa-bed"></i> <?=$r['capacity']?> Beds</div>
        </div>
        <div class="room-price">
          <div>
            <div class="room-price-amt">₹<?=number_format($r['monthly_rent'],0)?></div>
            <div class="room-price-lbl">per month</div>
          </div>
          <div style="color:#64748b;font-size:12.5px;font-weight:700;"><i class="fas fa-check-circle" style="color:#10b981;"></i> Available</div>
        </div>
        <button class="btn-book" onclick="openBooking(<?=$r['id']?>, '<?=htmlspecialchars($r['room_number'])?>', '<?=$r['room_type']?>', '<?=$r['monthly_rent']?>')">
          <i class="fas fa-calendar-check"></i> Book Now
        </button>
      </div>
    </div>
    <?php endwhile; endif; ?>
  </div>
</section>

<!-- VISIT REGISTER SECTION -->
<section class="visit-section" id="visit">
  <div class="visit-inner">
    <div class="visit-img-side">
      <div class="visit-visual">
        <div class="visit-visual-title"><i class="fas fa-clipboard-list"></i> Student Visit Registration</div>
        <div class="visit-step">
          <div class="visit-step-num">1</div>
          <div class="visit-step-txt">
            <h4>Fill the Form</h4>
            <p>Enter student details, visitor name, relation and visit date/time.</p>
          </div>
        </div>
        <div class="visit-step">
          <div class="visit-step-num">2</div>
          <div class="visit-step-txt">
            <h4>Submit at Reception</h4>
            <p>Submit the form at reception desk or online through the portal.</p>
          </div>
        </div>
        <div class="visit-step">
          <div class="visit-step-num">3</div>
          <div class="visit-step-txt">
            <h4>Visitor Entry Granted</h4>
            <p>Visitor gets entry pass valid from entry time to exit time.</p>
          </div>
        </div>
        <div class="visit-step">
          <div class="visit-step-num">4</div>
          <div class="visit-step-txt">
            <h4>Admin Tracks Record</h4>
            <p>All visit records are stored and visible in admin panel.</p>
          </div>
        </div>
        <a href="visit_register.php" class="visit-cta-btn"><i class="fas fa-arrow-right"></i> Register a Visit</a>
      </div>
    </div>
    <div>
      <div class="section-badge"><i class="fas fa-users"></i> Visitor Management</div>
      <h2 class="section-title">Register Student Visit</h2>
      <p class="section-sub" style="margin-bottom:26px;">Family members or friends visiting a student must register their visit. This ensures safety and security of all hostel residents.</p>
      <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;gap:14px;align-items:flex-start;">
          <div style="width:44px;height:44px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:17px;flex-shrink:0;"><i class="fas fa-user-check"></i></div>
          <div><h4 style="font-weight:800;margin-bottom:4px;">Secure Visitor Tracking</h4><p style="color:var(--grey);font-size:13.5px;line-height:1.6;">Every visitor is logged with entry and exit times for complete security records.</p></div>
        </div>
        <div style="display:flex;gap:14px;align-items:flex-start;">
          <div style="width:44px;height:44px;border-radius:12px;background:#d1fae5;display:flex;align-items:center;justify-content:center;color:#059669;font-size:17px;flex-shrink:0;"><i class="fas fa-clock"></i></div>
          <div><h4 style="font-weight:800;margin-bottom:4px;">Visiting Hours: 9 AM – 6 PM</h4><p style="color:var(--grey);font-size:13.5px;line-height:1.6;">Visitors are allowed only during designated hours to maintain hostel discipline.</p></div>
        </div>
        <div style="display:flex;gap:14px;align-items:flex-start;">
          <div style="width:44px;height:44px;border-radius:12px;background:#ffedd5;display:flex;align-items:center;justify-content:center;color:#ea580c;font-size:17px;flex-shrink:0;"><i class="fas fa-id-card"></i></div>
          <div><h4 style="font-weight:800;margin-bottom:4px;">ID Proof Required</h4><p style="color:var(--grey);font-size:13.5px;line-height:1.6;">Visitor must carry valid government ID proof for entry at reception.</p></div>
        </div>
      </div>
      <a href="visit_register.php" style="display:inline-flex;align-items:center;gap:8px;background:var(--pri);color:#fff;font-weight:800;font-size:14px;padding:12px 24px;border-radius:10px;margin-top:26px;transition:.2s;" onmouseover="this.style.background='#2563a8'" onmouseout="this.style.background='var(--pri)'">
        <i class="fas fa-clipboard-list"></i> Register Visit Now
      </a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <p style="font-family:'Poppins',sans-serif;font-size:17px;font-weight:800;color:#fff;margin-bottom:6px;"><i class="fas fa-building"></i> HMS – Hostel Management System</p>
  <p>© <?=date('Y')?> All Rights Reserved | <a href="login.php">Admin Login</a> | <a href="visit_register.php">Register Visit</a></p>
</footer>

<!-- BOOKING MODAL -->
<div class="modal-ov" id="bookingModal">
  <div class="modal-box">
    <div class="modal-hdr">
      <h3><i class="fas fa-calendar-check"></i> Room Booking</h3>
      <button class="modal-close" onclick="closeBooking()">×</button>
    </div>
    <div id="modalContent">
      <div class="modal-bdy">
        <div id="bookingError" class="alert-err" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span id="bookingErrorMsg"></span></div>
        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:14px 16px;margin-bottom:18px;">
          <div style="font-weight:800;font-size:13px;color:#0369a1;"><i class="fas fa-door-open"></i> Selected Room: <span id="selRoomName" style="color:#1e3a5f;"></span></div>
          <div style="font-size:12px;color:#64748b;margin-top:4px;" id="selRoomInfo"></div>
        </div>
        <input type="hidden" id="selRoomId">
        <div class="fgrid">
          <div class="fg"><label>Full Name <span class="req">*</span></label><input type="text" id="bkName" class="fc" placeholder="Your full name"></div>
          <div class="fg"><label>Email Address <span class="req">*</span></label><input type="email" id="bkEmail" class="fc" placeholder="your@email.com"></div>
        </div>
        <div class="fgrid">
          <div class="fg"><label>Phone Number <span class="req">*</span></label><input type="tel" id="bkPhone" class="fc" placeholder="10-digit mobile"></div>
          <div class="fg"><label>Room Type</label><input type="text" id="bkRoomType" class="fc" readonly style="background:#f8fafc;"></div>
        </div>
        <div class="fgrid">
          <div class="fg"><label>Check-in Date <span class="req">*</span></label><input type="date" id="bkCheckin" class="fc"></div>
          <div class="fg"><label>Check-out Date <span class="req">*</span></label><input type="date" id="bkCheckout" class="fc"></div>
        </div>
        <div class="fg"><label>Special Requirements</label><textarea id="bkRemarks" class="fc" rows="2" placeholder="Any special requirements or notes..."></textarea></div>
      </div>
      <div class="modal-ftr">
        <button class="btn-cancel" onclick="closeBooking()">Cancel</button>
        <button class="btn-submit" onclick="submitBooking()"><i class="fas fa-check"></i> Confirm Booking</button>
      </div>
    </div>
  </div>
</div>

<script>
// Navbar scroll effect
window.addEventListener('scroll',()=>{
  const nb=document.getElementById('navbar');
  if(window.scrollY>50){nb.style.background='rgba(30,58,95,0.98)';nb.style.boxShadow='0 4px 30px rgba(0,0,0,0.4)';}
  else{nb.style.background='rgba(30,58,95,0.95)';nb.style.boxShadow='0 2px 20px rgba(0,0,0,0.3)';}
});
// Hamburger
document.getElementById('hamburger').onclick=()=>document.getElementById('navLinks').classList.toggle('open');
// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a=>a.addEventListener('click',e=>{
  e.preventDefault();const t=document.querySelector(a.getAttribute('href'));
  if(t)t.scrollIntoView({behavior:'smooth',block:'start'});
}));
// Scroll reveal
const obs=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('visible');}),{threshold:0.1});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));

// Booking Modal
function openBooking(id,num,type,price){
  document.getElementById('selRoomId').value=id;
  document.getElementById('selRoomName').textContent='Room '+num;
  document.getElementById('selRoomInfo').textContent=type+' • ₹'+parseFloat(price).toLocaleString('en-IN')+'/month';
  document.getElementById('bkRoomType').value=type;
  document.getElementById('bookingError').style.display='none';
  // Set min date = today
  const today=new Date().toISOString().split('T')[0];
  document.getElementById('bkCheckin').min=today;
  document.getElementById('bkCheckout').min=today;
  document.getElementById('bookingModal').classList.add('show');
}
function closeBooking(){
  document.getElementById('bookingModal').classList.remove('show');
  // Reset
  ['bkName','bkEmail','bkPhone','bkCheckin','bkCheckout','bkRemarks'].forEach(id=>document.getElementById(id).value='');
}
document.getElementById('bookingModal').addEventListener('click',function(e){if(e.target===this)closeBooking();});

function submitBooking(){
  const name=document.getElementById('bkName').value.trim();
  const email=document.getElementById('bkEmail').value.trim();
  const phone=document.getElementById('bkPhone').value.trim();
  const checkin=document.getElementById('bkCheckin').value;
  const checkout=document.getElementById('bkCheckout').value;
  const roomId=document.getElementById('selRoomId').value;
  const roomType=document.getElementById('bkRoomType').value;
  const remarks=document.getElementById('bkRemarks').value.trim();
  const errBox=document.getElementById('bookingError');
  const errMsg=document.getElementById('bookingErrorMsg');

  if(!name||!email||!phone||!checkin||!checkout){
    errBox.style.display='flex'; errMsg.textContent='Please fill all required fields!'; return;
  }
  if(!/^\d{10}$/.test(phone.replace(/\s/g,''))){
    errBox.style.display='flex'; errMsg.textContent='Please enter a valid 10-digit phone number!'; return;
  }
  if(new Date(checkout)<=new Date(checkin)){
    errBox.style.display='flex'; errMsg.textContent='Check-out date must be after check-in date!'; return;
  }
  errBox.style.display='none';

  const formData=new FormData();
  formData.append('act','book');
  formData.append('room_id',roomId);
  formData.append('name',name);
  formData.append('email',email);
  formData.append('phone',phone);
  formData.append('room_type',roomType);
  formData.append('checkin',checkin);
  formData.append('checkout',checkout);
  formData.append('remarks',remarks);

  const btn=document.querySelector('.btn-submit');
  btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Processing...';btn.disabled=true;

  fetch('booking_action.php',{method:'POST',body:formData})
    .then(r=>r.json())
    .then(data=>{
      if(data.success){
        document.getElementById('modalContent').innerHTML=`
          <div class="success-box">
            <div class="success-ico"><i class="fas fa-check-circle"></i></div>
            <h3>Booking Request Submitted!</h3>
            <p>Thank you, <strong>${name}</strong>! Your booking request for <strong>Room ${document.getElementById('selRoomName').textContent.replace('Room ','')}</strong> from <strong>${checkin}</strong> to <strong>${checkout}</strong> has been received.</p>
            <p style="margin-top:10px;color:#10b981;font-weight:700;">Our team will contact you at ${email} or ${phone} shortly.</p>
            <p style="margin-top:8px;font-size:12px;color:#94a3b8;">Booking Reference: #${data.ref}</p>
            <button onclick="closeBooking();document.getElementById('modalContent').innerHTML=''" style="margin-top:20px;background:var(--pri);color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:800;cursor:pointer;font-size:14px;">Close</button>
          </div>`;
      } else {
        errBox.style.display='flex'; errMsg.textContent=data.error||'Something went wrong. Please try again.';
        btn.innerHTML='<i class="fas fa-check"></i> Confirm Booking';btn.disabled=false;
      }
    })
    .catch(()=>{
      errBox.style.display='flex'; errMsg.textContent='Connection error. Please try again.';
      btn.innerHTML='<i class="fas fa-check"></i> Confirm Booking';btn.disabled=false;
    });
}
</script>
</body>
</html>
