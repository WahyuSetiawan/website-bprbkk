<?php
include "koneksi.php";
echo "<h2>Pohon Keputusan</h2>";
include "menu_c45.php";
echo " <p><center><a href=./aksi.php?module=c45&act=hapus_pohon_keputusan>Hapus Semua Data</a></center></p>";
echo "<font face='Courier New' size='2'>";
echo "<h3><b>Pohon Keputusan: <br></b></h3>";
function get_subfolder($idparent, $spasi){
    $result = mysql_query("select * from pohon_keputusan where id_parent= '$idparent'");
    while($row=mysql_fetch_row($result)){
        for($i=1;$i<=$spasi;$i++){
            echo "-&nbsp;";
        }
        if ($row[6] === 'Ya') {
            $kelayakan = "<font color=blue>$row[6]</font>";
        } elseif ($row[6] === 'Tidak') {
            $kelayakan = "<font color=#000000>$row[6]</font>";
        } elseif ($row[6] === '?') {
            $kelayakan = "<font color=#FF00FF>$row[6]</font>";
        } else {
            $kelayakan = "<b>$row[6]</b>";
        }
        echo "<font color=green>$row[1]</font> = $row[2] (Tidak = $row[4], Ya = $row[5]) : <b>$kelayakan</b><br>";

        /*panggil dirinya sendiri*/
        get_subfolder($row[0], $spasi + 1);
    }
}

    get_subfolder('0', 0);
echo "<hr>";

echo "<h3><b>Rule: <br></b></h3>";
$no = 1;
$sqlLihatRule = mysql_query("select * from rule_c45 order by id" );
while($rowLihatRule=mysql_fetch_array($sqlLihatRule)){
    if ($rowLihatRule['kelayakan'] === 'Ya') {
        $kelayakan = "<font color=green>$rowLihatRule[kelayakan ]</font>";
    } elseif ($rowLihatRule['kelayakan '] === 'Tidak') {
        $kelayakan  = "<font color=red>$rowLihatRule[kelayakan ]</font>";
    } elseif ($rowLihatRule['kelayakan '] === '?') {
        $kelayakan = "<font color=blue>$rowLihatRule[kelayakan ]</font>";
    } else {
        $kelayakan  = "<b>$rowLihatRule[kelayakan ]</b>";
    }

    echo "<b>$no.</b> if <b>(</b>$rowLihatRule[rule]<b>)</b> then <b>$kelayakan </b> <font color=blue>(id = $rowLihatRule[id])</font><br>";
$no++;
}
echo "</font>";