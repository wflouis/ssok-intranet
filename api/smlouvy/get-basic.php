<?php
require 'base-read.php';

$result = mysqli_query($link, "SELECT id_smlouvy, cisloSmlouvy FROM smlouvy");

if($result){
    http_response_code(200);
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}
else{
    http_response_code(500);
}
