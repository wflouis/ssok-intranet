<?php
require 'base-read.php';

$result = mysqli_query($link, "
select id_partnera as id, nazev
from partneri
order by nazev asc
");
echo mysqli_error($link);

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));