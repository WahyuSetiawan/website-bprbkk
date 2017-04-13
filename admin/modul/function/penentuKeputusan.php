<?php
include "../../../config/koneksi.php";
mysql_query("TRUNCATE variabel_prediksi");

// buat variabel baru yg berisi data yg di post-kan
$noktp = $_POST['noktp'];
$nama = $_POST['nama'];
$pinjaman = $_POST['pinjaman'];
$penghasilan = $_POST['penghasilan'];
$angunan = $_POST['angunan'];
$blbi = $_POST['blbi'];
$syarat = $_POST['syarat'];

// masukkan variabel pada array
$array2 = array("pinjaman" => "$pinjaman",
        "penghasilan" => "$penghasilan",
        "angunan" => "$angunan",
        "blbi" => "$blbi",
        "syarat" => "$syarat"
        );

$sqlSelectDistinctVariabel = mysql_query("SELECT variabel FROM pohon_keputusan");
while($rowSelectDistinctVariabel = mysql_fetch_array($sqlSelectDistinctVariabel)) {
    if (!empty($rowSelectDistinctVariabel)) {
        foreach ($array2 as $variabel_array => $nilai_variabel_array) {
            // jika variabel pada pohon sama dgn variabel yg ada di array (variabel yg di post-kan) maka insert variabel dan nilai variabel pada db
            if ($rowSelectDistinctVariabel['variabel'] == $variabel_array) {
                mysql_query("INSERT INTO variabel_prediksi VALUES('', '$variabel_array', '$nilai_variabel_array')");
            }
        }
    }
}

$arrayPenentuKelayakan = array(); // buat array baru
// ambil variabel dan nilai variabel pada db data penentu keputusan
$sqlDataPenentuKelayakan = mysql_query("SELECT variabel, nilai_variabel FROM variabel_prediksi");
while($rowDataPenentuKelayakan= mysql_fetch_array($sqlDataPenentuKelayakan)) {
    // ambil variabel dan nilai variabel pada db rule_penentu_keputusan
    $sqlRulePenentuKelayakan = mysql_query("SELECT id, variabel, nilai_variabel FROM rule_prediksi where pohon = 'C45'");
    while($rowRulePenentuKelayakan = mysql_fetch_array($sqlRulePenentuKelayakan)) {
        if (!empty($rowRulePenentuKelayakan)) {
            // jika variabel pada db data_penentu_keputusan sama dengan variabel pada db rule_c45
            if ($rowRulePenentuKelaykan['variabel'] == $rowDataPenentuKelayakan['variabel']) {
                // jika nilai variabel pada db data_penentu_keputusan sama dengan nilai variabel pada db rule_c45
                if ($rowRulePenentuKelayakan['nilai_variabel'] == $rowDataPenentuKelayakan['nilai_variabel']) {
                    // set nilai id, cocok dan masukkan pada array
                    $arrayPenentuKelayakanTemp['id'] = $rowRulePenentuKelayakan['id'];
                    $arrayPenentuKelayakanTemp['cocok'] = "Ya";
                    $arrayPenentuKelayakan[] = $arrayPenentuKelayakanTemp;
                } elseif ($rowRulePenentuKelayakan['nilai_variabel'] !== $rowDataPenentuKelayakan['nilai_variabel']) {
                    // set nilai id, cocok dan masukkan pada array
                    $arrayPenentuKelayakanTemp['id'] = $rowRulePenentuKelayakan['id'];
                    $arrayPenentuKelayakanTemp['cocok'] = "Tidak";
                    $arrayPenentuKelayakan[] = $arrayPenentuKelayakanTemp;
                }
            }
        }
    }
}

foreach ($arrayPenentuKelayakan as $arrayPenentuKelayakanUpdate) {
    // update nilai cocok dari array sebelumnya
    mysql_query("UPDATE rule_prediksi SET cocok = '$arrayPenentuKelayakanUpdate[cocok]' where id = $arrayPenentuKelayakanUpdate[id]");
}

// queri utk mengambil keputusan dan id rule berdasarkan nilai variabel yg cocok (nilai variabel rule == nilai variabel yg dipost-kan)
$sqlKelayakan = mysql_query("SELECT distinct id_rule, kelayakan FROM `rule_prediksi` WHERE pohon = 'C45' AND cocok = 'Ya' and id_rule not in (select distinct id_rule from `rule_prediksi` where pohon = 'C45' AND cocok = 'Tidak')");
$rowKelayakan = mysql_fetch_array($sqlKelayakan);
if (!empty($rowKelayakan)) {
    $kelayakan = "$rowKelaykan[kelayakan]";
    $idRule = "$rowKelayakan[id_rule]";

    if ($kelayakan == 'Ya') {
        // insert data kelayakan pada db
        mysql_query("INSERT INTO data_prediksi VALUES('',
            '$nokyp',
            '$nama',
            '$pinjaman',
            '$penghasilan',
            '$angunan',
            '$blbi',
            '$syarat',
            '$kelayakan',
            '$idRule')
        ");
		
		
        echo "<script>document.location.href='../../media.php?module=prediksi';</script>\n";
    } elseif ($kelayakan == 'Tidak') {
        // insert data kelancaran pada db
        mysql_query("INSERT INTO data_prediksi VALUES('',
            '$no',
            '$nama',
            '$pinjaman',
            '$penghasilan',
            '$angunan',
            '$blbi',
            '$syarat',
            '$kelayakan',
            '$idRule')
        ");
		echo "<script>document.location.href='../../media.php?module=prediksi';</script>\n";
}}