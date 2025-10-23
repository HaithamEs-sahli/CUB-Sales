<?php
require __DIR__.'/_db.php';
$user_id    = filter_input(INPUT_POST,'user_id',FILTER_VALIDATE_INT);
$listing_id = filter_input(INPUT_POST,'listing_id',FILTER_VALIDATE_INT);
if(!$user_id || !$listing_id){
  http_response_code(400); exit('Bad input');
}

$stmt=$mysqli->prepare("INSERT INTO favorite (user_id, listing_id, created_at) VALUES (?,?,NOW())");
$stmt->bind_param("ii",$user_id,$listing_id);
if($stmt->execute()){
  echo "Favorite added.";
}else{
  echo "Insert failed: ".$mysqli->error;
}
?>
