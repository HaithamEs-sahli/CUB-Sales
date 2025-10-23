<?php
require __DIR__.'/_db.php';
$listing_id  = filter_input(INPUT_POST, 'listing_id', FILTER_VALIDATE_INT);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
if(!$listing_id || !$category_id){
  http_response_code(400); exit('Bad input');
}

$stmt = $mysqli->prepare("INSERT INTO listing_category (listing_id, category_id) VALUES (?,?)");
$stmt->bind_param("ii", $listing_id, $category_id);

if($stmt->execute()){
  echo "Linked listing #{$listing_id} to category #{$category_id}.";
} else {
  echo "Insert failed: " . $mysqli->error;
}
?>
