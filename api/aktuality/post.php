<?php
require 'base-write.php';

$stmt = mysqli_prepare($link, "INSERT into zpravy
(id_jmeno, text)
values (?,?)
");
$stmt->bind_param('is', $_SESSION['id_jmeno'], $obj['text']);

echo $stmt->error;

if($stmt->execute()) {
    $id = $stmt->insert_id;

    $invalidAddresses = sendEmails($obj);
    $invalidAddressesJson = json_encode($invalidAddresses);

    echo "{\"id\":$id,\"invalidAddresses\":$invalidAddressesJson}";
    
    http_response_code(200);
}
else {
    http_response_code(500);
}
