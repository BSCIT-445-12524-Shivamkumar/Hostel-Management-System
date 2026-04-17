╔══════════════════════════════════════════════════════════╗
║        HMS - HOSTEL MANAGEMENT SYSTEM                    ║
║        Complete XAMPP Setup Guide                        ║
╚══════════════════════════════════════════════════════════╝

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 🔑 DEFAULT LOGIN
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Admin Email   : admin@hostel.com
  Admin Password: Admin@123

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 📥 INSTALLATION STEPS (3 Steps)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

STEP 1: Copy Files
  → Extract and place "hms" folder in:
  → C:\xampp\htdocs\hms\

STEP 2: Import Database
  → Open: http://localhost/phpmyadmin
  → Click "Import" tab
  → Select file: hms/database.sql
  → Click "Go"

STEP 3: Open in Browser
  → http://localhost/hms/

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 ⚙️ CONFIGURATION (includes/config.php)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  DB_HOST = localhost
  DB_USER = root
  DB_PASS = (leave empty for default XAMPP)
  DB_NAME = hostel_db
  SITE_URL= http://localhost/hms

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 📧 OTP EMAIL (Forgot Password)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  To send real OTP emails, edit config.php:
    MAIL_FROM = your_gmail@gmail.com

  For Gmail to work in PHP mail():
  1. Open: C:\xampp\php\php.ini
  2. Find [mail function] section
  3. Set: SMTP=smtp.gmail.com
  4. Set: smtp_port=587

  ✅ TEST MODE: If mail not configured, the
  OTP is shown directly on screen for testing.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 ✅ ALL FEATURES INCLUDED
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  ✅ Student Registration (Name, Roll, Course,
     Mobile, Address, Guardian Details)
  ✅ Student List / Edit / Delete
  ✅ Room Management (2/3/4 bed)
  ✅ Room Allotment + Room Change + Vacate
  ✅ Fee Management + Monthly Fee System
  ✅ Fee Payment Status + Receipt Print
  ✅ Pending Fee List
  ✅ Complaint System (Water/Electricity/WiFi etc)
  ✅ Admin Complaint Resolve
  ✅ Daily Attendance (Present/Absent/Leave)
  ✅ Attendance Calendar View
  ✅ Visitor Entry / Exit / Checkout
  ✅ Notice Board (Admin publish/manage)
  ✅ Student Portal (Login, Profile, Room, Fees)
  ✅ Full Admin Panel
  ✅ Reports: Students, Rooms, Fees, Complaints, Attendance
  ✅ PDF/Print Reports (Print → Save as PDF)
  ✅ Forgot Password with OTP via Email
  ✅ Change Password
  ✅ Student Password Reset by Admin

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 📁 PROJECT STRUCTURE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  hms/
  ├── index.php               Auto redirect
  ├── login.php               Login page
  ├── forgot_password.php     OTP Password Reset
  ├── logout.php
  ├── change_password.php
  ├── database.sql            Import this first!
  ├── includes/
  │   ├── config.php          DB + Settings
  │   ├── header.php          Sidebar + Layout
  │   ├── footer.php
  │   ├── auth.php            Login required
  │   └── admin_auth.php      Admin required
  ├── assets/
  │   ├── css/style.css       All styles
  │   └── js/main.js          JavaScript
  ├── admin/
  │   ├── dashboard.php
  │   ├── students.php
  │   ├── rooms.php
  │   ├── allotments.php
  │   ├── fees.php
  │   ├── receipt.php         Print receipt
  │   ├── complaints.php
  │   ├── attendance.php
  │   ├── visitors.php
  │   ├── notices.php
  │   └── reports.php         PDF + Print reports
  └── student/
      ├── dashboard.php
      ├── profile.php
      ├── room.php
      ├── fees.php
      ├── complaints.php
      ├── attendance.php
      └── notices.php

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Built with: PHP, MySQL, HTML5, CSS3, JS
  Requires: XAMPP (PHP 7.4+, MySQL 5.7+)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# Hostel-Management-System
