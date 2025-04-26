<?php
function getZprava($radek){
    $zahlavi = date('d.m.Y', strtotime($radek['datum'])) .' v '. date('H:i', strtotime($radek['datum'])) . ' - autor: ' . $radek['jmeno'];
    return "
    <div class='zprava'>
        <div class='zahlaviZpravy'>$zahlavi</div>
        <div class='textZpravy'>{$radek['text']}</div>
    </div>
    ";
}