<?php
require_once 'includes/config.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['act']??'') !== 'book'){
    echo json_encode(['success'=>false,'error'=>'Invalid request.']);
    exit;
}

$room_id  = (int)($_POST['room_id']??0);
$name     = san($_POST['name']??'');
$email    = san($_POST['email']??'');
$phone    = san($_POST['phone']??'');
$room_type= san($_POST['room_type']??'');
$checkin  = san($_POST['checkin']??'');
$checkout = san($_POST['checkout']??'');
$remarks  = san($_POST['remarks']??'');

// Validate
if(!$room_id||!$name||!$email||!$phone||!$checkin||!$checkout){
    echo json_encode(['success'=>false,'error'=>'Please fill all required fields.']); exit;
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo json_encode(['success'=>false,'error'=>'Invalid email address.']); exit;
}
if(strtotime($checkout) <= strtotime($checkin)){
    echo json_encode(['success'=>false,'error'=>'Check-out must be after check-in date.']); exit;
}

// Check room still available
$room = $conn->query("SELECT * FROM rooms WHERE id=$room_id AND status='available' LIMIT 1")->fetch_assoc();
if(!$room){
    echo json_encode(['success'=>false,'error'=>'Sorry, this room is no longer available.']); exit;
}

// Insert booking
$ref = 'BK-'.date('Ymd').'-'.strtoupper(substr(md5(uniqid(rand(),true)),0,6));

$stmt = $conn->prepare("INSERT INTO room_bookings (room_id, name, email, phone, room_type, checkin_date, checkout_date, remarks, ref_no, status) VALUES (?,?,?,?,?,?,?,?,?,'pending')");
$stmt->bind_param("issssssss", $room_id, $name, $email, $phone, $room_type, $checkin, $checkout, $remarks, $ref);

if($stmt->execute()){
    echo json_encode(['success'=>true,'ref'=>$ref]);
} else {
    echo json_encode(['success'=>false,'error'=>'Database error. Please try again.']);
}
?>
