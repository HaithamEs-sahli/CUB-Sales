<?php
require __DIR__.'/_db.php';
$from_user_id = filter_input(INPUT_POST,'from_user_id',FILTER_VALIDATE_INT);
$listing_id   = filter_input(INPUT_POST,'listing_id',FILTER_VALIDATE_INT);
$method       = $_POST['method'] ?? null;
$message      = trim($_POST['message'] ?? '');
$method_value = trim($_POST['method_value'] ?? '');

if(!$from_user_id || !$listing_id || !in_array($method,['email','phone'],true) || $message==='' || $method_value===''){
  http_response_code(400); exit('Bad input');
}

$mysqli->begin_transaction();
try {
  $stmt=$mysqli->prepare("INSERT INTO contact (listing_id, from_user_id, message, method, created_at) VALUES (?,?,?,?,NOW())");
  $stmt->bind_param("iiss", $listing_id, $from_user_id, $message, $method);
  $stmt->execute();
  $contact_id = $stmt->insert_id;

  if($method==='email'){
    $s2=$mysqli->prepare("INSERT INTO email_contact (contact_id, email) VALUES (?,?)");
    $s2->bind_param("is",$contact_id,$method_value);
  } else {
    $s2=$mysqli->prepare("INSERT INTO phone_contact (contact_id, phone) VALUES (?,?)");
    $s2->bind_param("is",$contact_id,$method_value);
  }
  $s2->execute();

  $mysqli->commit();
  echo "Contact #{$contact_id} recorded.";
} catch(Throwable $e){
  $mysqli->rollback();
  http_response_code(500);
  echo "Insert failed: " . $mysqli->error;
}
?>
