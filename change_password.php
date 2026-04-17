<?php
require_once 'includes/auth.php';
$pg="Change Password"; $msg=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $old=$_POST['old_pass']??''; $new=$_POST['new_pass']??''; $cnf=$_POST['cnf_pass']??'';
  $u=$conn->query("SELECT password FROM users WHERE id={$_SESSION['uid']}")->fetch_assoc();
  if(!password_verify($old,$u['password'])) $err="Current password is incorrect!";
  elseif($new!==$cnf) $err="New passwords do not match!";
  elseif(strlen($new)<6) $err="Password must be at least 6 characters!";
  else {
    $h=password_hash($new,PASSWORD_DEFAULT);
    $conn->query("UPDATE users SET password='$h' WHERE id={$_SESSION['uid']}");
    $msg="Password changed successfully!";
  }
}
require_once 'includes/header.php';
?>
<div class="ph"><div class="ph-left"><h1><i class="fas fa-key"></i> Change Password</h1></div></div>
<div class="card" style="max-width:480px;">
  <div class="card-head"><h3><i class="fas fa-lock"></i> Update Your Password</h3></div>
  <div class="card-body">
    <?php if($err): ?><div class="alert alert-danger"><i class="fas fa-times-circle"></i> <?=esc($err)?></div><?php endif;?>
    <?php if($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?=esc($msg)?></div><?php endif;?>
    <form method="POST">
      <div class="fg"><label>Current Password <span class="req">*</span></label><input type="password" name="old_pass" class="fc" required></div>
      <div class="fg"><label>New Password <span class="req">*</span></label><input type="password" name="new_pass" class="fc" required minlength="6"></div>
      <div class="fg"><label>Confirm New Password <span class="req">*</span></label><input type="password" name="cnf_pass" class="fc" required></div>
      <button type="submit" class="btn btn-pri"><i class="fas fa-save"></i> Update Password</button>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php';?>
