<?php
require 'base-read.php';

$result = mysqli_query($link, "
select id_str as id, zkratka, nazev from seznam_str
");

$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($rows, JSON_PRETTY_PRINT);