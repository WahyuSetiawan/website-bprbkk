<?php
switch($_GET[act]){
    default:
        include "pohon_keputusan.php";
    break;
    case "mining";
        
        include "menu_c45.php";
        timer_start();
        include "function/miningPrePruningC45.php";
        $waktu = timer_stop(3);
        echo "<p>Proses Mining Selesai</p>";
    break;
}