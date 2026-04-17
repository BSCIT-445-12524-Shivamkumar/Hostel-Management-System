<?php
require_once 'includes/config.php';
if(isLoggedIn()) go(SITE_URL.'/'.$_SESSION['role'].'/dashboard.php');
$err=''; $ok='';
if(isset($_GET['msg'])) $ok = san($_GET['msg']);
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = san($_POST['email']??'');
  $pass  = $_POST['password']??'';
  if($email && $pass){
    $st = $conn->prepare("SELECT * FROM users WHERE email=? AND status='active' LIMIT 1");
    $st->bind_param("s",$email); $st->execute();
    $u = $st->get_result()->fetch_assoc();
    if($u && password_verify($pass, $u['password'])){
      $_SESSION['uid']   = $u['id'];
      $_SESSION['name']  = $u['name'];
      $_SESSION['email'] = $u['email'];
      $_SESSION['role']  = $u['role'];
      if($u['role']==='admin'){
        go(SITE_URL.'/admin/dashboard.php');
      } else {
        $sid = $conn->query("SELECT id FROM students WHERE user_id={$u['id']} LIMIT 1")->fetch_assoc();
        $_SESSION['sid'] = $sid ? $sid['id'] : 0;
        go(SITE_URL.'/student/dashboard.php');
      }
    } else $err='Invalid email or password!';
  } else $err='Please fill all fields!';
}
?><!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | HMS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<div class="login-pg">
<div class="login-box">
  <div style="text-align:center;margin-bottom:24px;">
    <div class="login-ico"><i class="fas fa-building"></i></div>
    <h2 style="font-family:'Poppins',sans-serif;font-size:22px;color:var(--pri);margin-top:10px;">HMS Login</h2>
    <p style="color:var(--grey);font-size:13px;">Hostel Management System</p>
  </div>
  <?php if($err): ?><div class="alert alert-danger"><i class="fas fa-times-circle"></i> <?=esc($err)?></div><?php endif;?>
  <?php if($ok):  ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?=esc($ok)?></div><?php endif;?>
  <form method="POST" autocomplete="on">
    <div class="fg"><label>Email Address</label>
      <input type="email" name="email" class="fc" placeholder="Enter your email" required value="<?=isset($_POST['email'])?esc($_POST['email']):''?>"></div>
    <div class="fg"><label>Password</label>
      <input type="password" name="password" class="fc" placeholder="Enter your password" required></div>
    <button type="submit" class="btn btn-pri w100" style="justify-content:center;padding:11px;font-size:15px;margin-top:4px;"><i class="fas fa-sign-in-alt"></i> Login</button>
  </form>
  <div style="text-align:center;margin-top:16px;">
    <a href="forgot_password.php" style="color:var(--pri2);font-size:13px;font-weight:700;"><i class="fas fa-key"></i> Forgot Password? (OTP via Email)</a>
  </div>
  <div style="margin-top:18px;background:var(--light);border-radius:8px;padding:12px;font-size:12.5px;color:var(--grey);text-align:center;">
    <strong>Demo:</strong> admin@hostel.com &nbsp;/&nbsp; Admin@123
  </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
</body></html>
