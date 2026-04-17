<?php
require_once 'includes/config.php';
$msg=''; $err=''; $submitted=false;

if($_SERVER['REQUEST_METHOD']==='POST'){
    $student_name  = san($_POST['student_name']??'');
    $roll_no       = san($_POST['roll_no']??'');
    $department    = san($_POST['department']??'');
    $visitor_name  = san($_POST['visitor_name']??'');
    $visitor_mobile= san($_POST['visitor_mobile']??'');
    $relation      = san($_POST['relation']??'');
    $visit_date    = san($_POST['visit_date']??'');
    $entry_time    = san($_POST['entry_time']??'');
    $exit_time     = san($_POST['exit_time']??'');
    $purpose       = san($_POST['purpose']??'');

    if(!$student_name||!$roll_no||!$department||!$visitor_name||!$relation||!$visit_date||!$entry_time||!$exit_time){
        $err='Please fill all required fields.';
    } elseif(strtotime($exit_time)<strtotime($entry_time)){
        $err='Exit time must be after entry time.';
    } else {
        // Look up student by roll_no (optional matching)
        $st_row = $conn->query("SELECT id FROM students WHERE roll_no='".mysqli_real_escape_string($conn,$roll_no)."' AND status='active' LIMIT 1")->fetch_assoc();
        
        $entry_dt = $visit_date.' '.$entry_time.':00';
        $exit_dt  = $visit_date.' '.$exit_time.':00';

        if($st_row){
            // Student found in DB - insert into visitors table
            $sid = $st_row['id'];
            $vm  = mysqli_real_escape_string($conn,$visitor_mobile);
            $vn  = mysqli_real_escape_string($conn,$visitor_name);
            $rel = mysqli_real_escape_string($conn,$relation);
            $pur = mysqli_real_escape_string($conn,$purpose);
            $conn->query("INSERT INTO visitors(student_id,visitor_name,visitor_mobile,relation_type,purpose,entry_time,exit_time,status) VALUES($sid,'$vn','$vm','$rel','$pur','$entry_dt','$exit_dt','in')");
        }

        // Also insert into student_visit_requests for admin review
        $sn  = mysqli_real_escape_string($conn,$student_name);
        $rn  = mysqli_real_escape_string($conn,$roll_no);
        $dep = mysqli_real_escape_string($conn,$department);
        $vn2 = mysqli_real_escape_string($conn,$visitor_name);
        $vm2 = mysqli_real_escape_string($conn,$visitor_mobile);
        $rel2= mysqli_real_escape_string($conn,$relation);
        $pur2= mysqli_real_escape_string($conn,$purpose);
        $conn->query("INSERT INTO student_visit_requests(student_name,roll_no,department,visitor_name,visitor_mobile,relation_type,purpose,visit_date,entry_time,exit_time) VALUES('$sn','$rn','$dep','$vn2','$vm2','$rel2','$pur2','$visit_date','$entry_dt','$exit_dt')");

        $submitted = true;
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Register Visit | HMS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--pri:#1e3a5f;--pri2:#2563a8;--acc:#10b981;--dark:#1f2937;--grey:#64748b;--border:#e2e8f0;--bg:#f0f4f8;--white:#fff;}
body{font-family:'Nunito',sans-serif;background:var(--bg);min-height:100vh;}

/* NAVBAR */
.navbar{background:var(--pri);padding:0 5%;height:64px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 20px rgba(0,0,0,0.3);}
.nav-brand{display:flex;align-items:center;gap:10px;}
.nav-brand-ico{width:38px;height:38px;background:#f59e0b;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--pri);}
.nav-brand-txt{font-family:'Poppins',sans-serif;font-size:18px;font-weight:800;color:#fff;}
.nav-links{display:flex;align-items:center;gap:4px;}
.nav-links a{color:rgba(255,255,255,0.8);font-weight:700;font-size:13px;padding:7px 13px;border-radius:7px;transition:.2s;}
.nav-links a:hover{background:rgba(255,255,255,0.12);color:#fff;}
.nav-cta{background:#f59e0b!important;color:var(--pri)!important;font-weight:800!important;}

/* PAGE HERO */
.page-hero{
  background:linear-gradient(135deg,var(--pri) 0%,#2563a8 100%);
  padding:44px 5% 50px;position:relative;overflow:hidden;
}
.page-hero::before{content:'';position:absolute;top:-30%;right:-10%;width:400px;height:400px;border-radius:50%;background:radial-gradient(rgba(255,255,255,0.06),transparent 70%);}
.page-hero-inner{position:relative;z-index:2;max-width:700px;}
.page-hero h1{font-family:'Poppins',sans-serif;font-size:clamp(22px,4vw,36px);font-weight:900;color:#fff;margin-bottom:10px;}
.page-hero p{color:rgba(255,255,255,0.7);font-size:14.5px;line-height:1.65;}
.breadcrumb{display:flex;align-items:center;gap:8px;margin-bottom:14px;font-size:13px;}
.breadcrumb a{color:rgba(255,255,255,0.6);font-weight:700;transition:.2s;}
.breadcrumb a:hover{color:#fbbf24;}
.breadcrumb span{color:rgba(255,255,255,0.35);}
.breadcrumb .cur{color:#fbbf24;font-weight:800;}

/* MAIN */
.main-wrap{max-width:860px;margin:0 auto;padding:36px 5% 60px;}

/* INFO BARS */
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:30px;}
.info-card{background:#fff;border-radius:12px;padding:16px 18px;box-shadow:0 2px 12px rgba(0,0,0,0.07);display:flex;align-items:center;gap:12px;border-left:4px solid;}
.info-card.blue{border-color:#3b82f6;}.info-card.green{border-color:#10b981;}.info-card.orange{border-color:#f97316;}
.info-card-ico{font-size:20px;}
.info-card.blue .info-card-ico{color:#3b82f6;}.info-card.green .info-card-ico{color:#10b981;}.info-card.orange .info-card-ico{color:#f97316;}
.info-card h4{font-size:13px;font-weight:800;color:var(--dark);}
.info-card p{font-size:12px;color:var(--grey);margin-top:1px;}

/* FORM CARD */
.form-card{background:#fff;border-radius:18px;box-shadow:0 4px 24px rgba(0,0,0,0.09);overflow:hidden;}
.form-card-hdr{background:linear-gradient(135deg,var(--pri),#2563a8);padding:22px 28px;display:flex;align-items:center;gap:12px;}
.form-card-hdr h2{font-family:'Poppins',sans-serif;font-size:18px;font-weight:800;color:#fff;}
.form-card-hdr p{color:rgba(255,255,255,0.7);font-size:12.5px;margin-top:2px;}
.form-card-hdr-ico{width:46px;height:46px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;flex-shrink:0;}
.form-body{padding:28px;}

/* SECTION DIVIDER */
.fsec{margin-bottom:24px;}
.fsec-title{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:1.5px;color:var(--pri2);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid #e0f2fe;display:flex;align-items:center;gap:7px;}
.fgrid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.fgrid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.fg{margin-bottom:0;}
.fg label{display:block;font-size:12.5px;font-weight:800;color:#374151;margin-bottom:6px;}
.fg label .req{color:#ef4444;}
.fc{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:9px;font-size:13.5px;font-family:'Nunito',sans-serif;color:var(--dark);outline:none;transition:.2s;background:#fff;}
.fc:focus{border-color:var(--pri2);box-shadow:0 0 0 3px rgba(37,99,168,0.1);}
textarea.fc{resize:vertical;}

/* ALERTS */
.alert-err{background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:9px;}
.alert-ok{background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:9px;}

/* SUCCESS PAGE */
.success-page{text-align:center;padding:50px 30px;}
.success-ico-big{width:90px;height:90px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;font-size:38px;color:#059669;margin:0 auto 22px;}
.success-page h2{font-family:'Poppins',sans-serif;font-size:24px;font-weight:900;color:var(--dark);margin-bottom:10px;}
.success-page p{color:var(--grey);font-size:14.5px;line-height:1.7;max-width:460px;margin:0 auto;}
.success-detail{background:#f8fafc;border-radius:12px;padding:20px 24px;margin:22px auto;max-width:420px;text-align:left;}
.sd-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px;}
.sd-row:last-child{border:none;}
.sd-row span:first-child{color:var(--grey);font-weight:700;}
.sd-row span:last-child{font-weight:800;color:var(--dark);}
.btn-back{display:inline-flex;align-items:center;gap:8px;background:var(--pri);color:#fff;font-weight:800;font-size:14px;padding:12px 26px;border-radius:10px;margin-top:10px;transition:.2s;}
.btn-back:hover{background:var(--pri2);}
.btn-another{display:inline-flex;align-items:center;gap:8px;background:#f1f5f9;color:#475569;font-weight:800;font-size:14px;padding:12px 26px;border-radius:10px;margin-top:10px;margin-right:10px;transition:.2s;}
.btn-another:hover{background:#e2e8f0;}

/* SUBMIT BTN */
.form-footer{padding:20px 28px;background:#f8fafc;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
.btn-submit{background:linear-gradient(135deg,var(--pri),var(--pri2));color:#fff;border:none;padding:13px 32px;border-radius:10px;font-size:15px;font-weight:800;cursor:pointer;font-family:'Nunito',sans-serif;display:inline-flex;align-items:center;gap:8px;transition:.2s;}
.btn-submit:hover{opacity:.9;transform:translateY(-1px);}
.form-note{font-size:12px;color:var(--grey);display:flex;align-items:center;gap:5px;}

@media(max-width:680px){.fgrid-2,.fgrid-3{grid-template-columns:1fr;}.nav-links{display:none;}}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="nav-brand">
    <div class="nav-brand-ico"><i class="fas fa-building"></i></div>
    <span class="nav-brand-txt">HMS</span>
  </div>
  <div class="nav-links">
    <a href="home.php"><i class="fas fa-home"></i> Home</a>
    <a href="home.php#rooms"><i class="fas fa-door-open"></i> Rooms</a>
    <a href="visit_register.php" style="color:#fbbf24;"><i class="fas fa-clipboard-list"></i> Register Visit</a>
    <a href="login.php" class="nav-cta"><i class="fas fa-sign-in-alt"></i> Login</a>
  </div>
</nav>

<!-- PAGE HERO -->
<div class="page-hero">
  <div class="page-hero-inner">
    <div class="breadcrumb">
      <a href="home.php"><i class="fas fa-home"></i> Home</a>
      <span>›</span>
      <span class="cur">Register Visit</span>
    </div>
    <h1><i class="fas fa-clipboard-list"></i> Student Visit Registration</h1>
    <p>Fill this form to register a visitor for a hostel student. All fields are required for security purposes. Visitors must carry a valid government ID proof.</p>
  </div>
</div>

<div class="main-wrap">

  <!-- INFO CARDS -->
  <div class="info-grid">
    <div class="info-card blue">
      <div class="info-card-ico"><i class="fas fa-clock"></i></div>
      <div><h4>Visiting Hours</h4><p>9:00 AM – 6:00 PM daily</p></div>
    </div>
    <div class="info-card green">
      <div class="info-card-ico"><i class="fas fa-id-card"></i></div>
      <div><h4>ID Proof Required</h4><p>Aadhaar / Voter ID / Passport</p></div>
    </div>
    <div class="info-card orange">
      <div class="info-card-ico"><i class="fas fa-shield-alt"></i></div>
      <div><h4>Security Check</h4><p>All visitors are verified at gate</p></div>
    </div>
  </div>

  <?php if($submitted): 
    // Get submitted values for display
    $stu_name_disp = $_POST['student_name']??'';
    $roll_disp     = $_POST['roll_no']??'';
    $dept_disp     = $_POST['department']??'';
    $vis_disp      = $_POST['visitor_name']??'';
    $rel_disp      = $_POST['relation']??'';
    $date_disp     = $_POST['visit_date']??'';
    $entry_disp    = $_POST['entry_time']??'';
    $exit_disp     = $_POST['exit_time']??'';
  ?>
  <div class="form-card">
    <div class="success-page">
      <div class="success-ico-big"><i class="fas fa-check"></i></div>
      <h2>Visit Registered Successfully!</h2>
      <p>The visitor registration has been submitted and recorded. Please show this confirmation at the reception desk along with a valid ID proof.</p>
      <div class="success-detail">
        <div class="sd-row"><span>Student Name</span><span><?=esc($stu_name_disp)?></span></div>
        <div class="sd-row"><span>Roll Number</span><span><?=esc($roll_disp)?></span></div>
        <div class="sd-row"><span>Department</span><span><?=esc($dept_disp)?></span></div>
        <div class="sd-row"><span>Visitor Name</span><span><?=esc($vis_disp)?></span></div>
        <div class="sd-row"><span>Relation</span><span><?=esc($rel_disp)?></span></div>
        <div class="sd-row"><span>Visit Date</span><span><?=date('d M Y',strtotime($date_disp))?></span></div>
        <div class="sd-row"><span>Entry Time</span><span><?=date('h:i A',strtotime($entry_disp))?></span></div>
        <div class="sd-row"><span>Exit Time</span><span><?=date('h:i A',strtotime($exit_disp))?></span></div>
        <div class="sd-row"><span>Status</span><span style="color:#059669;">✔ Registered</span></div>
      </div>
      <div>
        <a href="visit_register.php" class="btn-another"><i class="fas fa-plus"></i> Register Another</a>
        <a href="home.php" class="btn-back"><i class="fas fa-home"></i> Back to Home</a>
      </div>
    </div>
  </div>

  <?php else: ?>

  <!-- FORM -->
  <div class="form-card">
    <div class="form-card-hdr">
      <div class="form-card-hdr-ico"><i class="fas fa-user-friends"></i></div>
      <div>
        <h2>Visitor Registration Form</h2>
        <p>Enter complete details of the student and visitor</p>
      </div>
    </div>
    <form method="POST" id="visitForm">
    <div class="form-body">

      <?php if($err):?><div class="alert-err"><i class="fas fa-exclamation-circle"></i> <?=esc($err)?></div><?php endif;?>

      <!-- STUDENT DETAILS -->
      <div class="fsec">
        <div class="fsec-title"><i class="fas fa-user-graduate"></i> Student Details</div>
        <div class="fgrid-3" style="gap:16px;">
          <div class="fg">
            <label>Student Name <span class="req">*</span></label>
            <input type="text" name="student_name" class="fc" placeholder="Full name of student" required value="<?=esc($_POST['student_name']??'')?>">
          </div>
          <div class="fg">
            <label>Roll Number <span class="req">*</span></label>
            <input type="text" name="roll_no" class="fc" placeholder="e.g. 2024CS001" required value="<?=esc($_POST['roll_no']??'')?>">
          </div>
          <div class="fg">
            <label>Department <span class="req">*</span></label>
            <select name="department" class="fc" required>
              <option value="">-- Select Department --</option>
              <?php
              $depts=['Computer Science','Information Technology','Electronics','Mechanical','Civil','Electrical','Biotechnology','Commerce','Arts','Science','Law','Management','Other'];
              foreach($depts as $d): $sel=(($_POST['department']??'')===$d)?'selected':'';?>
              <option value="<?=$d?>" <?=$sel?>><?=$d?></option>
              <?php endforeach;?>
            </select>
          </div>
        </div>
      </div>

      <!-- VISITOR DETAILS -->
      <div class="fsec">
        <div class="fsec-title"><i class="fas fa-user"></i> Visitor Details</div>
        <div class="fgrid-2" style="gap:16px;margin-bottom:16px;">
          <div class="fg">
            <label>Visitor Name <span class="req">*</span></label>
            <input type="text" name="visitor_name" class="fc" placeholder="Full name of visitor" required value="<?=esc($_POST['visitor_name']??'')?>">
          </div>
          <div class="fg">
            <label>Visitor Mobile</label>
            <input type="tel" name="visitor_mobile" class="fc" placeholder="10-digit mobile number" maxlength="15" value="<?=esc($_POST['visitor_mobile']??'')?>">
          </div>
        </div>
        <div class="fg">
          <label>Relation with Student <span class="req">*</span></label>
          <select name="relation" class="fc" required>
            <option value="">-- Select Relation --</option>
            <?php
            $rels=['Father','Mother','Brother','Sister','Guardian','Uncle','Aunt','Grandfather','Grandmother','Friend','Other'];
            foreach($rels as $rel): $sel=(($_POST['relation']??'')===$rel)?'selected':'';?>
            <option value="<?=$rel?>" <?=$sel?>><?=$rel?></option>
            <?php endforeach;?>
          </select>
        </div>
      </div>

      <!-- VISIT TIMING -->
      <div class="fsec">
        <div class="fsec-title"><i class="fas fa-calendar-alt"></i> Visit Schedule</div>
        <div class="fgrid-3" style="gap:16px;margin-bottom:16px;">
          <div class="fg">
            <label>Visit Date <span class="req">*</span></label>
            <input type="date" name="visit_date" class="fc" required
              min="<?=date('Y-m-d')?>"
              value="<?=esc($_POST['visit_date']??date('Y-m-d'))?>">
          </div>
          <div class="fg">
            <label>Entry Time <span class="req">*</span></label>
            <input type="time" name="entry_time" class="fc" required min="09:00" max="18:00" value="<?=esc($_POST['entry_time']??'09:00')?>">
            <div style="font-size:11px;color:#94a3b8;margin-top:4px;"><i class="fas fa-info-circle"></i> Allowed: 9AM–6PM</div>
          </div>
          <div class="fg">
            <label>Exit Time <span class="req">*</span></label>
            <input type="time" name="exit_time" class="fc" required min="09:00" max="18:00" value="<?=esc($_POST['exit_time']??'17:00')?>">
          </div>
        </div>
        <div class="fg">
          <label>Purpose of Visit</label>
          <textarea name="purpose" class="fc" rows="2" placeholder="e.g. Family visit, Collecting documents, Medical emergency..."><?=esc($_POST['purpose']??'')?></textarea>
        </div>
      </div>

    </div>
    <div class="form-footer">
      <div class="form-note"><i class="fas fa-lock"></i> Your data is stored securely and only accessible by hostel admin.</div>
      <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Submit Registration</button>
    </div>
    </form>
  </div>
  <?php endif;?>

</div>

<!-- FOOTER -->
<footer style="background:var(--pri);color:rgba(255,255,255,0.6);text-align:center;padding:20px 5%;font-size:13px;margin-top:0;">
  © <?=date('Y')?> HMS - Hostel Management System | <a href="home.php" style="color:#fbbf24;font-weight:700;">Home</a> | <a href="login.php" style="color:#fbbf24;font-weight:700;">Admin Login</a>
</footer>

<script>
// Client-side time validation
document.getElementById('visitForm')&&document.getElementById('visitForm').addEventListener('submit',function(e){
  const entry=document.querySelector('[name=entry_time]').value;
  const exit=document.querySelector('[name=exit_time]').value;
  if(exit&&entry&&exit<=entry){
    e.preventDefault();
    alert('Exit time must be after entry time!');
  }
});
</script>
</body>
</html>
