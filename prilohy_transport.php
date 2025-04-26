
<?php
die;
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

ini_set('max_execution_time', 1000);

require 'databaze.php';

$result = mysqli_query($link, 'select * from smlouvyPrilohy order by id_smlouvy');

$sourceDir = '/share-new/smlouvy/';
$destDir = '/share-new/reformatted/';

$lastid = -1;

$i = 0;
while(($radek = mysqli_fetch_assoc($result)) && ($i++ < 100000)){
    $dir = $destDir . $radek['id_smlouvy'] . '/';
    $name = (($radek["cislo"]==0)?$radek["nazev"]:$radek["id_smlouvy"]."-".$radek["cislo"]);

    if($radek['id_smlouvy'] != $lastid){
        $lastid = $radek['id_smlouvy'];
        if(!is_dir($dir)){
          mkdir($dir);
        }
    }

    if(file_exists($dir . $name)) continue;
    echo $sourceDir . $name.';'.$dir . $name . '<br>';
// readfile($sourceDir . $name);
    copy($sourceDir . $name, $dir . $name);
}
