<?php
require_once 'includes/config.php';
if(isLoggedIn()) go(SITE_URL.'/'.($_SESSION['role']).'/dashboard.php');

$step = (int)($_SESSION['fp_step'] ?? 1);
$msg=''; $err=''; $show_otp='';

if($_SERVER['REQUEST_METHOD']==='POST'){

  // STEP 1: Send OTP
  if(isset($_POST['send_otp'])){
    $email = san($_POST['email']??'');
    if(!$email){ $err='Please enter your email address.'; }
    else {
      $st=$conn->prepare("SELECT * FROM users WHERE email=? AND status='active' LIMIT 1");
      $st->bind_param("s",$email); $st->execute();
      $u = $st->get_result()->fetch_assoc();
      if($u){
        $otp = genOTP();
        $exp = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $conn->query("UPDATE users SET otp='$otp', otp_expiry='$exp' WHERE id={$u['id']}");
        $sent = sendOTP($email, $u['name'], $otp);
        $_SESSION['fp_step']  = 2;
        $_SESSION['fp_email'] = $email;
        $step = 2;
        if($sent){
          $msg = "OTP sent to <strong>$email</strong>! Please check your inbox and spam folder.";
        } else {
          // Show OTP on screen for testing when mail() not configured
          $msg = "OTP email could not be sent (configure SMTP). For testing, your OTP is: <strong style='font-size:18px;letter-spacing:4px;color:var(--pri);'>$otp</strong>";
        }
      } else {
        $err = "No account found with this email address.";
      }
    }
  }

  // STEP 2: Verify OTP
  elseif(isset($_POST['verify_otp'])){
    $email = $_SESSION['fp_email'] ?? '';
    $otp   = san($_POST['otp']??'');
    if(!$otp){ $err='Please enter the OTP.'; $step=2; }
    else {
      $st=$conn->prepare("SELECT * FROM users WHERE email=? AND otp=? AND otp_expiry > NOW() LIMIT 1");
      $st->bind_param("ss",$email,$otp); $st->execute();
      $u = $st->get_result()->fetch_assoc();
      if($u){
        $_SESSION['fp_step']   = 3;
        $_SESSION['fp_uid']    = $u['id'];
        $step = 3;
        $msg  = "OTP verified successfully! Now set your new password.";
      } else {
        $err = "Invalid or expired OTP. Please try again.";
        $step = 2;
      }
    }
  }

  // STEP 3: Reset Password
  elseif(isset($_POST['reset_pass'])){
    $uid  = (int)($_SESSION['fp_uid'] ?? 0);
    $p1   = $_POST['password']??'';
    $p2   = $_POST['confirm_password']??'';
    if(!$uid){ $err='Session expired. Start again.'; $step=1; unset($_SESSION['fp_step'],$_SESSION['fp_email'],$_SESSION['fp_uid']); }
    elseif(strlen($p1)<6){ $err='Password must be at least 6 characters.'; $step=3; }
    elseif($p1!==$p2){ $err='Passwords do not match.'; $step=3; }
    else {
      $h = password_hash($p1, PASSWORD_DEFAULT);
      $conn->query("UPDATE users SET password='$h', otp=NULL, otp_expiry=NULL WHERE id=$uid");
      unset($_SESSION['fp_step'],$_SESSION['fp_email'],$_SESSION['fp_uid']);
      go(SITE_URL.'/login.php?msg=Password+reset+successfully!+Please+login.');
    }
  }

  // Resend OTP
  elseif(isset($_POST['resend_otp'])){
    $email = $_SESSION['fp_email'] ?? '';
    if($email){
      $u = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1")->fetch_assoc();
      if($u){
        $otp = genOTP();
        $exp = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $conn->query("UPDATE users SET otp='$otp', otp_expiry='$exp' WHERE id={$u['id']}");
        $sent = sendOTP($email, $u['name'], $otp);
        $step = 2;
        if($sent) $msg = "New OTP sent to <strong>$email</strong>!";
        else $msg = "Resend OTP: <strong style='font-size:18px;letter-spacing:4px;color:var(--pri);'>$otp</strong> (Configure SMTP in includes/config.php for real email)";
      }
    }
  }
}

// If step session is ahead of current, use it
if(isset($_SESSION['fp_step'])) $step = (int)$_SESSION['fp_step'];
?><!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forgot Password | HMS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800;900&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<div class="login-pg">
<div class="login-box">
  <div style="text-align:center;margin-bottom:20px;">
    <div class="login-ico" style="background:var(--yel);"><i class="fas fa-key" style="color:var(--pri);"></i></div>
    <h2 style="font-family:'Poppins',sans-serif;font-size:20px;color:var(--pri);margin-top:10px;">Reset Password</h2>
    <p style="color:var(--grey);font-size:12.5px;">Step <?=$step?> of 3</p>
  </div>

  <!-- Progress Bar -->
  <div class="prog-wrap">
    <div class="prog-step <?=$step>=1?'done':''?>"></div>
    <div class="prog-step <?=$step>=2?'done':''?>"></div>
    <div class="prog-step <?=$step>=3?'done':''?>"></div>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><i class="fas fa-times-circle"></i> <?=$err?></div><?php endif;?>
  <?php if($msg): ?><div class="alert alert-info"><i class="fas fa-info-circle"></i> <?=$msg?></div><?php endif;?>

  <!-- STEP 1: Enter Email -->
  <?php if($step==1): ?>
  <form method="POST">
    <div class="fg"><label><i class="fas fa-envelope"></i> Registered Email Address</label>
      <input type="email" name="email" class="fc" placeholder="Enter your registered email" required autofocus>
    </div>
    <button type="submit" name="send_otp" class="btn btn-pri w100" style="justify-content:center;padding:11px;font-size:14px;"><i class="fas fa-paper-plane"></i> Send OTP to Email</button>
  </form>

  <!-- STEP 2: Enter OTP -->
  <?php elseif($step==2): ?>
  <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;color:#075985;text-align:center;">
    <i class="fas fa-envelope"></i> OTP sent to <strong><?=esc($_SESSION['fp_email']??'')?></strong>
  </div>
  <form method="POST">
    <div class="fg"><label><i class="fas fa-shield-alt"></i> Enter 6-Digit OTP</label>
      <input type="text" name="otp" class="fc" placeholder="0 0 0 0 0 0" maxlength="6" required autofocus
        style="text-align:center;font-size:28px;font-weight:900;letter-spacing:12px;padding:14px;">
    </div>
    <button type="submit" name="verify_otp" class="btn btn-pri w100" style="justify-content:center;padding:11px;font-size:14px;"><i class="fas fa-check-circle"></i> Verify OTP</button>
  </form>
  <form method="POST" style="margin-top:12px;text-align:center;">
    <button type="submit" name="resend_otp" style="background:none;border:none;color:var(--pri2);cursor:pointer;font-size:13px;font-weight:700;"><i class="fas fa-redo"></i> Resend OTP</button>
  </form>
  <form method="POST" style="margin-top:8px;text-align:center;">
    <button type="submit" name="send_otp" value="1" onclick="document.querySelector('[name=fp_email]').value='<?=esc($_SESSION['fp_email']??'')?>';" style="background:none;border:none;color:var(--grey);cursor:pointer;font-size:12px;"><i class="fas fa-arrow-left"></i> Change Email</button>
    <input type="hidden" name="email" value="<?=esc($_SESSION['fp_email']??'')?>">
  </form>

  <!-- STEP 3: New Password -->
  <?php elseif($step==3): ?>
  <form method="POST">
    <div class="fg"><label><i class="fas fa-lock"></i> New Password <span class="req">*</span></label>
      <input type="password" name="password" class="fc" required minlength="6" placeholder="Min 6 characters" autofocus>
    </div>
    <div class="fg"><label><i class="fas fa-lock"></i> Confirm New Password <span class="req">*</span></label>
      <input type="password" name="confirm_password" class="fc" required placeholder="Retype new password">
    </div>
    <button type="submit" name="reset_pass" class="btn btn-suc w100" style="justify-content:center;padding:11px;font-size:14px;"><i class="fas fa-save"></i> Update Password</button>
  </form>
  <?php endif;?>

  <div style="text-align:center;margin-top:16px;">
    <a href="login.php" style="color:var(--pri2);font-size:13px;font-weight:700;"><i class="fas fa-arrow-left"></i> Back to Login</a>
  </div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
</body></html>
