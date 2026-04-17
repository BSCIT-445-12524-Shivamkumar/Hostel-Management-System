<?php
$host='localhost'; $user='root'; $pass='';
$conn = new mysqli($host,$user,$pass);
if($conn->connect_error) die("<div style='font-family:sans-serif;padding:30px;background:#fee;border-radius:8px;margin:20px;'><h2>MySQL Connection Failed</h2><p>".$conn->connect_error."</p><p>Make sure XAMPP MySQL is running.</p></div>");

$steps=[]; $errors=[];

$sqls = [
"CREATE DATABASE IF NOT EXISTS `hostel_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
"USE `hostel_db`",
"DROP TABLE IF EXISTS `visitors`",
"DROP TABLE IF EXISTS `room_bookings`",
"DROP TABLE IF EXISTS `student_visit_requests`",
"DROP TABLE IF EXISTS `attendance`",
"DROP TABLE IF EXISTS `complaints`",
"DROP TABLE IF EXISTS `fees`",
"DROP TABLE IF EXISTS `room_allotments`",
"DROP TABLE IF EXISTS `rooms`",
"DROP TABLE IF EXISTS `students`",
"DROP TABLE IF EXISTS `notices`",
"DROP TABLE IF EXISTS `users`",
"CREATE TABLE `users` (`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(150) NOT NULL,`email` varchar(150) NOT NULL,`password` varchar(255) NOT NULL,`role` enum('admin','student') DEFAULT 'student',`otp` varchar(10) DEFAULT NULL,`otp_expiry` datetime DEFAULT NULL,`status` enum('active','inactive') DEFAULT 'active',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),UNIQUE KEY `email` (`email`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `students` (`id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) DEFAULT NULL,`name` varchar(150) NOT NULL,`roll_no` varchar(50) NOT NULL,`course` varchar(100) DEFAULT '',`mobile` varchar(15) DEFAULT '',`address` text,`guardian_name` varchar(150) DEFAULT '',`guardian_mobile` varchar(15) DEFAULT '',`guardian_relation` varchar(50) DEFAULT '',`status` enum('active','inactive') DEFAULT 'active',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),UNIQUE KEY `roll_no` (`roll_no`),KEY `user_id` (`user_id`),CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `rooms` (`id` int(11) NOT NULL AUTO_INCREMENT,`room_number` varchar(20) NOT NULL,`floor_no` varchar(30) DEFAULT '',`room_type` enum('2-bed','3-bed','4-bed') DEFAULT '2-bed',`capacity` int(11) NOT NULL DEFAULT 2,`monthly_rent` decimal(10,2) DEFAULT 3000.00,`status` enum('available','full','maintenance') DEFAULT 'available',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),UNIQUE KEY `room_number` (`room_number`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `room_allotments` (`id` int(11) NOT NULL AUTO_INCREMENT,`student_id` int(11) NOT NULL,`room_id` int(11) NOT NULL,`bed_number` varchar(10) DEFAULT '',`check_in_date` date NOT NULL,`check_out_date` date DEFAULT NULL,`status` enum('active','vacated') DEFAULT 'active',`remarks` text,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),KEY `student_id` (`student_id`),KEY `room_id` (`room_id`),CONSTRAINT `fk_allot_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,CONSTRAINT `fk_allot_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `fees` (`id` int(11) NOT NULL AUTO_INCREMENT,`student_id` int(11) NOT NULL,`fee_month` varchar(20) NOT NULL,`fee_year` int(11) NOT NULL,`amount` decimal(10,2) NOT NULL,`paid_amount` decimal(10,2) DEFAULT 0.00,`due_date` date DEFAULT NULL,`payment_date` date DEFAULT NULL,`payment_mode` enum('cash','upi','online','cheque') DEFAULT 'cash',`transaction_id` varchar(100) DEFAULT '',`status` enum('pending','paid','partial','overdue') DEFAULT 'pending',`receipt_no` varchar(60) DEFAULT NULL,`remarks` text,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),KEY `student_id` (`student_id`),CONSTRAINT `fk_fee_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `complaints` (`id` int(11) NOT NULL AUTO_INCREMENT,`student_id` int(11) NOT NULL,`complaint_type` enum('water','electricity','wifi','room','food','security','other') NOT NULL,`subject` varchar(200) NOT NULL,`description` text NOT NULL,`priority` enum('low','medium','high') DEFAULT 'medium',`status` enum('pending','in_progress','resolved','rejected') DEFAULT 'pending',`admin_remarks` text,`resolved_at` datetime DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),KEY `student_id` (`student_id`),CONSTRAINT `fk_complaint_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `attendance` (`id` int(11) NOT NULL AUTO_INCREMENT,`student_id` int(11) NOT NULL,`attendance_date` date NOT NULL,`status` enum('present','absent','leave') DEFAULT 'present',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),UNIQUE KEY `uniq_att` (`student_id`,`attendance_date`),CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `visitors` (`id` int(11) NOT NULL AUTO_INCREMENT,`student_id` int(11) NOT NULL,`visitor_name` varchar(150) NOT NULL,`visitor_mobile` varchar(15) DEFAULT '',`relation_type` varchar(50) DEFAULT '',`purpose` varchar(255) DEFAULT '',`entry_time` datetime NOT NULL,`exit_time` datetime DEFAULT NULL,`status` enum('in','out') DEFAULT 'in',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),KEY `student_id` (`student_id`),CONSTRAINT `fk_visitor_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE `notices` (`id` int(11) NOT NULL AUTO_INCREMENT,`title` varchar(200) NOT NULL,`content` text NOT NULL,`notice_type` enum('general','rules','holiday','maintenance','urgent') DEFAULT 'general',`priority` enum('low','medium','high') DEFAULT 'medium',`is_active` tinyint(1) DEFAULT 1,`created_by` int(11) DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"INSERT INTO `users` (`name`,`email`,`password`,`role`) VALUES ('Super Admin','admin@hostel.com','\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin')",
"INSERT INTO `rooms` (`room_number`,`floor_no`,`room_type`,`capacity`,`monthly_rent`,`status`) VALUES ('101','Ground Floor','2-bed',2,3000.00,'available'),('102','Ground Floor','2-bed',2,3000.00,'available'),('103','Ground Floor','3-bed',3,2500.00,'available'),('201','1st Floor','2-bed',2,3500.00,'available'),('202','1st Floor','3-bed',3,2800.00,'available'),('203','1st Floor','4-bed',4,2200.00,'available'),('301','2nd Floor','2-bed',2,4000.00,'available'),('302','2nd Floor','4-bed',4,2500.00,'available')",
"INSERT INTO `notices` (`title`,`content`,`notice_type`,`priority`,`is_active`,`created_by`) VALUES ('Welcome to HMS','Welcome to our Hostel Management System. Please follow all hostel rules.','general','medium',1,1),('Hostel Timings & Rules','Entry after 10:00 PM strictly prohibited. Visitors allowed 9AM-6PM only.','rules','high',1,1),('Monthly Fee Reminder','Hostel fee is due by 10th of every month. Late fee Rs.100 after due date.','general','high',1,1)",
"CREATE TABLE IF NOT EXISTS `room_bookings` (`id` int(11) NOT NULL AUTO_INCREMENT,`room_id` int(11) NOT NULL,`name` varchar(150) NOT NULL,`email` varchar(150) NOT NULL,`phone` varchar(15) NOT NULL,`room_type` varchar(20) DEFAULT '',`checkin_date` date NOT NULL,`checkout_date` date NOT NULL,`remarks` text,`ref_no` varchar(30) DEFAULT NULL,`status` enum('pending','confirmed','cancelled') DEFAULT 'pending',`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),KEY `room_id` (`room_id`),CONSTRAINT `fk_booking_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE IF NOT EXISTS `student_visit_requests` (`id` int(11) NOT NULL AUTO_INCREMENT,`student_name` varchar(150) NOT NULL,`roll_no` varchar(50) NOT NULL,`department` varchar(100) NOT NULL,`visitor_name` varchar(150) NOT NULL,`visitor_mobile` varchar(15) DEFAULT '',`relation_type` varchar(50) DEFAULT '',`purpose` varchar(255) DEFAULT '',`visit_date` date NOT NULL,`entry_time` datetime NOT NULL,`exit_time` datetime NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

foreach($sqls as $sql){
  if($conn->query($sql)){
    if(preg_match('/CREATE TABLE `(\w+)`/',$sql,$m)) $steps[]="✅ Table <b>{$m[1]}</b> created";
    elseif(preg_match('/INSERT INTO `(\w+)`/',$sql,$m)) $steps[]="✅ Sample data → <b>{$m[1]}</b> ({$conn->affected_rows} rows)";
    elseif(strpos($sql,'CREATE DATABASE')!==false) $steps[]="✅ Database <b>hostel_db</b> ready";
  } else {
    if(strpos($sql,'DROP')===false) $errors[]="❌ ".$conn->error." <small>(".htmlspecialchars(substr($sql,0,50))."...)</small>";
  }
}
$conn->close();
?><!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>HMS Installer</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.1);max-width:620px;width:100%;padding:36px}
h1{font-size:22px;color:#1a202c;margin-bottom:4px}
.sub{color:#718096;font-size:13px;margin-bottom:22px}
.item{padding:8px 12px;border-radius:6px;margin-bottom:5px;font-size:13.5px}
.ok{background:#f0fff4;color:#276749}.er{background:#fff5f5;color:#c53030}
.creds{background:#ebf8ff;border:1px solid #bee3f8;border-radius:10px;padding:16px;margin-top:18px}
.creds h3{color:#2b6cb0;margin-bottom:10px;font-size:14px}
table{width:100%;font-size:13px;border-collapse:collapse}
td{padding:5px 8px}td:first-child{font-weight:700;width:90px;color:#4a5568}
.btn{display:inline-block;margin-top:18px;padding:12px 28px;background:linear-gradient(135deg,#1e3a5f,#2d6a9f);color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px}
.warn{background:#fffbeb;border:1px solid #f6e05e;border-radius:8px;padding:10px 14px;margin-top:14px;font-size:12px;color:#744210}
</style></head><body>
<div class="card">
  <h1>🏢 HMS — Auto Installer</h1>
  <div class="sub">Hostel Management System — Database Setup</div>
  <?php if(empty($errors)):?>
  <div class="item ok" style="font-size:15px;font-weight:700;margin-bottom:12px;">🎉 Installation Successful!</div>
  <?php else:?>
  <div class="item er" style="font-weight:700;margin-bottom:10px;">⚠️ Some errors occurred</div>
  <?php foreach($errors as $e):?><div class="item er"><?=$e?></div><?php endforeach;?>
  <?php endif;?>
  <?php foreach($steps as $s):?><div class="item ok"><?=$s?></div><?php endforeach;?>
  <?php if(empty($errors)):?>
  <div class="creds">
    <h3>🔐 Default Login Credentials</h3>
    <table>
      <tr><td>Admin</td><td>admin@hostel.com &nbsp;/&nbsp; <b>Admin@123</b></td></tr>
      <tr><td>Note</td><td>Add students from Admin panel, then they can login</td></tr>
    </table>
  </div>
  <a href="home.php" class="btn" style="margin-right:10px;">🏠 Go to Homepage</a>
  <a href="login.php" class="btn">➡️ Go to HMS Login</a>
  <div class="warn">⚠️ <b>Security:</b> Delete <code>install.php</code> after setup! &nbsp;|&nbsp; New pages: <b>home.php</b>, <b>visit_register.php</b> (public), <b>admin/bookings.php</b>, <b>admin/visit_requests.php</b></div>
  <?php endif;?>
</div></body></html>
