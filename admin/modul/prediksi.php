<?php
switch($_GET[act]){
    default:
        echo "<h2>Prediksi</h2>";
        include "form_prediksi.php";
		echo " <p><a href=./aksi.php?module=prediksi&act=hapus_semua_prediksi>Hapus Semua Prediksi</a></p>";
        echo "<table bgcolor='#81b9db' border='1' cellspacing='0' cellspading='0'>
            <tr>
                <th>NO</th>
                <th>NO KTP</th>
                <th>NAMA</th>
                <th>PINJAMAN</th>
                <th>PENGHASILAN</th>
                <th>ANGUNAN</th>
                <th>BLBI</th>
                <th>SYARAT</th>
                <th>KELAYAKAN</th>
           </tr>";

    $sql=mysql_query('SELECT * FROM data_prediksi ORDER BY id');
    $warna1 = '#ffffff';
    
    $warna  = $warna1; 
    $no = 1;
    while ($data=mysql_fetch_array($sql)){
        
        echo " <tr bgcolor='$warna'>
                   <td>$no</td>
                   <td>$data[noktp]</td>
                   <td>$data[nama]</td>
                   <td>$data[pinjaman]</td>
                   <td>$data[penghasilan]</td>
                   <td>$data[angunan]</td>
                   <td>$data[blbi]</td>
                   <td>$data[syarat]</td>
                   <td>$data[kelayakan_c45]</td>
               </tr>";
    $no++;
    }
    echo"</table>";
    break;
    
}