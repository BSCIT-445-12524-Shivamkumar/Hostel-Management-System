-- ============================================================
-- HMS - HOSTEL MANAGEMENT SYSTEM - Complete Database
-- ============================================================
CREATE DATABASE IF NOT EXISTS `hostel_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hostel_db`;

DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `complaints`;
DROP TABLE IF EXISTS `fees`;
DROP TABLE IF EXISTS `room_allotments`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `notices`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') DEFAULT 'student',
  `otp` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `course` varchar(100) DEFAULT '',
  `mobile` varchar(15) DEFAULT '',
  `address` text,
  `guardian_name` varchar(150) DEFAULT '',
  `guardian_mobile` varchar(15) DEFAULT '',
  `guardian_relation` varchar(50) DEFAULT '',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roll_no` (`roll_no`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `floor_no` varchar(30) DEFAULT '',
  `room_type` enum('2-bed','3-bed','4-bed') DEFAULT '2-bed',
  `capacity` int(11) NOT NULL DEFAULT 2,
  `monthly_rent` decimal(10,2) DEFAULT 3000.00,
  `status` enum('available','full','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `room_allotments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `bed_number` varchar(10) DEFAULT '',
  `check_in_date` date NOT NULL,
  `check_out_date` date DEFAULT NULL,
  `status` enum('active','vacated') DEFAULT 'active',
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `fk_allot_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_allot_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `fee_month` varchar(20) NOT NULL,
  `fee_year` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_mode` enum('cash','upi','online','cheque') DEFAULT 'cash',
  `transaction_id` varchar(100) DEFAULT '',
  `status` enum('pending','paid','partial','overdue') DEFAULT 'pending',
  `receipt_no` varchar(60) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `fk_fee_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `complaint_type` enum('water','electricity','wifi','room','food','security','other') NOT NULL,
  `subject` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','resolved','rejected') DEFAULT 'pending',
  `admin_remarks` text,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `fk_complaint_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave') DEFAULT 'present',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_att` (`student_id`,`attendance_date`),
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `visitor_name` varchar(150) NOT NULL,
  `visitor_mobile` varchar(15) DEFAULT '',
  `relation_type` varchar(50) DEFAULT '',
  `purpose` varchar(255) DEFAULT '',
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `status` enum('in','out') DEFAULT 'in',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `fk_visitor_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `notice_type` enum('general','rules','holiday','maintenance','urgent') DEFAULT 'general',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Admin user: password = Admin@123
INSERT INTO `users` (`name`,`email`,`password`,`role`) VALUES
('Super Admin','admin@hostel.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin');

-- Sample Rooms
INSERT INTO `rooms` (`room_number`,`floor_no`,`room_type`,`capacity`,`monthly_rent`,`status`) VALUES
('101','Ground Floor','2-bed',2,3000.00,'available'),
('102','Ground Floor','2-bed',2,3000.00,'available'),
('103','Ground Floor','3-bed',3,2500.00,'available'),
('201','1st Floor','2-bed',2,3500.00,'available'),
('202','1st Floor','3-bed',3,2800.00,'available'),
('203','1st Floor','4-bed',4,2200.00,'available'),
('301','2nd Floor','2-bed',2,4000.00,'available'),
('302','2nd Floor','4-bed',4,2500.00,'available');

-- Sample Notices
INSERT INTO `notices` (`title`,`content`,`notice_type`,`priority`,`is_active`,`created_by`) VALUES
('Welcome to HMS','Welcome to our Hostel Management System. Please follow all hostel rules and regulations.','general','medium',1,1),
('Hostel Timings & Rules','Entry after 10:00 PM is strictly prohibited. Visitors allowed 9 AM - 6 PM only. Maintain cleanliness in rooms and common areas. Any damage to hostel property will be charged.','rules','high',1,1),
('Monthly Fee Reminder','Hostel fee is due by 10th of every month. Late fee of Rs.100 will be charged after due date. Please pay on time to avoid inconvenience.','general','high',1,1),
('Water Supply Notice','Water supply will be temporarily unavailable this Sunday 8 AM - 12 PM for overhead tank cleaning. Please store water accordingly.','maintenance','medium',1,1);

-- ============================================================
-- NEW TABLES: Room Bookings & Student Visit Requests
-- ============================================================

CREATE TABLE IF NOT EXISTS `room_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `room_type` varchar(20) DEFAULT '',
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `remarks` text,
  `ref_no` varchar(30) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `fk_booking_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `student_visit_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_name` varchar(150) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `visitor_name` varchar(150) NOT NULL,
  `visitor_mobile` varchar(15) DEFAULT '',
  `relation_type` varchar(50) DEFAULT '',
  `purpose` varchar(255) DEFAULT '',
  `visit_date` date NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
