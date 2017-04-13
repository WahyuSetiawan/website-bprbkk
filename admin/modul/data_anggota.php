<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<?php
switch($_GET[act]){
    default:
        echo "<h2>Data Nasabah</h2>";
        include "form_data_anggota.php";
		echo "<form action='' method='post' enctype='multipart/form-data' name='form1' id='form1''>
                  <p>Ambil File .csv : 
                  <input name='csv' type='file' id='csv' />
                  <input type='submit' name='Submit' value='Submit' /></p >
                </form>";
        
                if ($_FILES[csv][size] > 0) {

                    //get the csv file
                    $file = $_FILES[csv][tmp_name];
                    $handle = fopen($file,"r");
                    
                    //loop through the csv file and insert into database
                    mysql_query("TRUNCATE data_anggota");
                    do {
                        if ($data[0]) {
                            mysql_query("INSERT INTO data_nasabah (noktp, nama, pinjaman, penghasilan, angunan, blbi, syarat, kelayakan) VALUES
                                (
                                    '".addslashes($data[0])."',
                                    '".addslashes($data[1])."',
                                    '".addslashes($data[2])."',
                                    '".addslashes($data[3])."',
                                    '".addslashes($data[4])."',
                                    '".addslashes($data[5])."',
									'".addslashes($data[6])."',
									'".addslashes($data[7])."'
                                )
                            ");
                        }
                    } while ($data = fgetcsv($handle,1000,",","'"));
                    //

                    //redirect
                    echo "<script>alert('Data berhasil diinput!'); document.location.href='media.php?module=data_anggota';</script>\n";

                }
        echo "<p><a href='media.php?module=data_anggota&act=lihat_data'>Lihat Data Nasabah</a></p>";

    break;
    case "lihat_data";
        echo " <p><center><a href=./aksi.php?module=data_anggota&act=hapus_semua_data_anggota>Hapus Semua Data</a></center></p>";
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
                <th>Opsi</th>
           </tr>";

    $sql=mysql_query('SELECT * FROM data_nasabah ORDER BY id');
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
				   <td>$data[kelayakan]</td>
                   <td><a href=?module=data_anggota&act=edit_data_anggota&id=$data[id]>Edit</a> |
                   <a href=./aksi.php?module=data_anggota&act=hapus_data_anggota&id=$data[id]>Hapus</a>
                   </td>
               </tr>";
    $no++;
    }
    echo"</table>";
    echo "<p><a href=javascript:history.back(-1)>Kembali</a></p>";
    break;
    
    case "edit_data_anggota";
        echo "<h2>Data Nasabah &#187; Edit Data Nasabah</h2>";
        include "form_edit_data_anggota.php";
    break;
    
}