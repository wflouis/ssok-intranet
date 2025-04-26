<?php
require 'base-read.php';

$result = mysqli_query($link, "
select id_jmeno as id, jmeno as name, email
from seznam
where jmeno != ''
order by jmeno asc
");

$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($users);