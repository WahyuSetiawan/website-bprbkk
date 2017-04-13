<?php
include "koneksi.php";
populateDb(); 
miningC45(null, null);
updateKeputusanUnknown();
generateRuleFinalPrePruning();
insertRuleC45PrePruning();

//---------- KUMPULAN FUNGSI YANG AKAN DILAKUKAN DALAM PROSES MINING ----------
function miningC45($variabel, $nilai_variabel)
{
    perhitunganC45($variabel, $nilai_variabel);
    insertVariabelPohonKeputusan($variabel, $nilai_variabel);
    getInfGainMax($variabel, $nilai_variabel);
}

//#1# Hapus semua DB dan insert default variabel dan nilai variabel
function populateVariabel() 
{
    mysql_query("TRUNCATE variabel");
    mysql_query("insert into `variabel` values
                ('', 'kelayakan', 'Kelayakan'),
				('', 'pinjaman', 'Low'),
                ('', 'pinjaman', 'Upper'),
				('', 'pinjaman', 'High'),
                ('', 'penghasilan', 'Low'),
                ('', 'penghasilan', 'Upper'),
				('', 'penghasilan', 'High'),
                ('', 'angunan', 'Low'),
                ('', 'angunan', 'Upper'),
                ('', 'angunan', 'High'),
                ('', 'blbi', 'Termasuk'),
                ('', 'blbi', 'Tidak Termasuk')
				('', 'syarat', 'Lengkap'),
                ('', 'syarat', 'Kurang')
                ");
}

function populateDb() 
{
    //#1# Hapus semua DB dan insert default variabel dan nilai variabel
    mysql_query("TRUNCATE rule_prediksi");
    mysql_query("TRUNCATE pohon_keputusan");
    mysql_query("TRUNCATE rule_c45");
    mysql_query("TRUNCATE perhitungan_c45");
    populateVariabel();
}

// ================ FUNGSI PERHITUNGAN C45 =================
function perhitunganC45($variabel, $nilai_variabel) 
{
    if (empty($variabel) AND empty($nilai_variabel)) {
//#2# Jika variabel yg diinputkan kosong, maka lakukan perhitungan awal
        $kondisiVariabel = ""; // set kondisi variabel kosong
    } else if (!empty($variabel) AND !empty($nilai_variabel)) { 
        // jika variabel tdk kosong, maka select kondisi variabel dari DB
        $sqlKondisiVariabel = mysql_query("SELECT kondisi_variabel FROM pohon_keputusan WHERE variabel = '$variabel' AND nilai_variabel = '$nilai_variabel' order by id DESC LIMIT 1");
        $rowKondisiVariabel = mysql_fetch_array($sqlKondisiVariabel);
        $kondisiVariabel = str_replace("~", "'", $rowKondisiVariabel['kondisi_variabel']); // replace string ~ menjadi '
    } 
    
    // ambil seluruh variabel
    $sqlVariabel = mysql_query("SELECT distinct variabel FROM variabel");
    while($rowGetVariabel = mysql_fetch_array($sqlVariabel)) {
        $getVariabel = $rowGetVariabel['variabel'];
        if ($getVariabel === 'kelayakan') { 
//#3# Jika variabel = kelancaran, maka hitung jumlah kasus kelancaran, jumlah kasus Ya dan jumlah kasus Tidak
            // hitung jumlah kasus kelancaran
            $sqlJumlahKasusTotal = mysql_query("SELECT COUNT(*) as jumlah_kelayakan FROM data_nasabah WHERE kelayakan is not null $kondisiVariabel");
            $rowJumlahKasusTotal = mysql_fetch_array($sqlJumlahKasusTotal);
            $getJumlahKasusTotal = $rowJumlahKasusTotal['jumlah_kelayakan'];

            // hitung jumlah kasus Ya
            $sqlJumlahKasusYa = mysql_query("SELECT COUNT(*) as jumlah_ya FROM data_nasabah WHERE kelayakan = 'Ya' AND kelayakan is not null $kondisiVariabel");
            $rowJumlahKasusYa = mysql_fetch_array($sqlJumlahKasusYa);
            $getJumlahKasusYa = $rowJumlahKasusYa['jumlah_ya'];

            // hitung jumlah kasus Tidak
            $sqlJumlahKasusTidak = mysql_query("SELECT COUNT(*) as jumlah_tidak FROM data_nasabah WHERE kelayakan = 'Tidak' AND kelayakan is not null $kondisiVariabel");
            $rowJumlahKasusTidak = mysql_fetch_array($sqlJumlahKasusTidak);
            $getJumlahKasusTidak = $rowJumlahKasusTidak['jumlah_tidak'];

//#4# Insert jumlah kasus kelancaran, jumlah kasus Ya dan jumlah kasus Tidak ke DB
            // insert ke database perhitungan_c45
            mysql_query("INSERT INTO perhitungan_c45 VALUES ('', 'Total', 'Total', '$getJumlahKasusTotal', '$getJumlahKasusTidak', '$getJumlahKasusYa', '', '', '')");

        } else {
//#5# Jika atribut != kelancaran (atribut lainnya), maka hitung jumlah kasus kelancaran, jumlah kasus Ya dan jumlah kasus Tidak masing2 atribut
            // ambil nilai variabel
            $sqlNilaiVariabel = mysql_query("SELECT nilai_variabel FROM variabel WHERE variabel = '$getVariabel' ORDER BY id");
            while($rowNilaiVariabel = mysql_fetch_array($sqlNilaiVariabel)) {
                $getNilaiVariabel = $rowNilaiVariabel['nilai_variabel'];

                // set kondisi dimana nilai_variabel = berdasakan masing2 variabel dan status data = data training
                $kondisi = "$getVariabel = '$getNilaiVariabel' AND kelayakan is not null $kondisiVariabel";

                // hitung jumlah kasus per variabel
                $sqlJumlahKasusTotalVariabel = mysql_query("SELECT COUNT(*) as jumlah_kelayakan FROM data_nasabah WHERE $kondisi");
                $rowJumlahKasusTotalVariabel = mysql_fetch_array($sqlJumlahKasusTotalVariabel);
                $getJumlahKasusTotalVariabel = $rowJumlahKasusTotalVariabel['jumlah_kelayakan'];

                // hitung jumlah kasus Ya
                $sqlJumlahKasusYaVariabel = mysql_query("SELECT COUNT(*) as jumlah_ya FROM data_nasabah WHERE $kondisi AND kelayakan = 'Ya'");
                $rowJumlahKasusYaVariabel = mysql_fetch_array($sqlJumlahKasusYaVariabel);
                $getJumlahKasusYaVariabel = $rowJumlahKasusYaVariabel['jumlah_ya'];

                // hitung jumlah kasus Tidak
                $sqlJumlahKasusTidakVariabel = mysql_query("SELECT COUNT(*) as jumlah_tidak FROM data_nasabah WHERE $kondisi AND kelayakan = 'Tidak'");
                $rowJumlahKasusTidakVariabel = mysql_fetch_array($sqlJumlahKasusTidakVariabel);
                $getJumlahKasusTidakVariabel = $rowJumlahKasusTidakVariabel['jumlah_tidak'];

//#6# Insert jumlah kasus kelancaran, jumlah kasus Ya dan jumlah kasus Tidak masing2 variabel ke DB
                // insert ke database perhitungan_c45
                mysql_query("INSERT INTO perhitungan_c45 VALUES ('', '$getVariabel', '$getNilaiVariabel', '$getJumlahKasusTotalVariabel', '$getJumlahKasusTidakVariabel', '$getJumlahKasusYaVariabel', '', '', '')");
                
//#7# Lakukan perhitungan entropy
                // perhitungan entropy
                $sqlEntropy = mysql_query("SELECT id, jml_kasus_kelayakan, jml_kasus_ya, jml_kasus_tidak FROM perhitungan_c45");
                while($rowEntropy = mysql_fetch_array($sqlEntropy)) {
                    $getJumlahKasusTotalEntropy = $rowEntropy['jml_kasus_kelayakan'];
                    $getJumlahKasusYaEntropy = $rowEntropy['jml_kasus_ya'];
                    $getJumlahKasusTidakEntropy = $rowEntropy['jml_kasus_tidak'];
                    $idEntropy = $rowEntropy['id'];

                    // jika jml kasus = 0 maka entropy = 0
                    if ($getJumlahKasusTotalEntropy == 0 OR $getJumlahKasusYaEntropy == 0 OR $getJumlahKasusTidakEntropy == 0) {
                        $getEntropy = 0;
                    // jika jml kasus Ya = jml kasus Tidak, maka entropy = 1
                    } else if ($getJumlahKasusYaEntropy == $getJumlahKasusTidakEntropy) {
                        $getEntropy = 1;
                    } else { // jika jml kasus != 0, maka hitung rumus entropy:
                        $perbandingan_ya = $getJumlahKasusYaEntropy / $getJumlahKasusTotalEntropy;
                        $perbandingan_tidak = $getJumlahKasusTidakEntropy / $getJumlahKasusTotalEntropy;

                        $rumusEntropy = (-($perbandingan_ya) * log($perbandingan_ya,2)) + (-($perbandingan_tidak) * log($perbandingan_tidak,2));
                        $getEntropy = round($rumusEntropy,4); // 4 angka di belakang koma
                    }

//#8# Update nilai entropy
                    // update nilai entropy
                    mysql_query("UPDATE perhitungan_c45 SET entropy = $getEntropy WHERE id = $idEntropy");
                }
                
//#9# Lakukan perhitungan information gain
                // perhitungan information gain
                // ambil nilai entropy dari kelancaran (jumlah kasus kelayakan)
                $sqlJumlahKasusTotalInfGain = mysql_query("SELECT jml_kasus_kelayakan, entropy FROM perhitungan_c45 WHERE variabel = 'Total'");
                $rowJumlahKasusTotalInfGain = mysql_fetch_array($sqlJumlahKasusTotalInfGain);
                $getJumlahKasusTotalInfGain = $rowJumlahKasusTotalInfGain['jml_kasus_kelayakan'];
                // rumus information gain
                $getInfGain = (-(($getJumlahKasusTotalEntropy / $getJumlahKasusTotalInfGain) * ($getEntropy))); 

//#10# Update information gain tiap nilai variabel (temporary)
                // update inf_gain_temp (utk mencari nilai masing2 variabel)
                mysql_query("UPDATE perhitungan_c45 SET inf_gain_temp = $getInfGain WHERE id = $idEntropy");
                $getEntropy = $rowJumlahKasusTotalInfGain['entropy'];

                // jumlahkan masing2 inf_gain_temp variabel 
                $sqlVariabelInfGain = mysql_query("SELECT SUM(inf_gain_temp) as inf_gain FROM perhitungan_c45 WHERE variabel = '$getVariabel'");
                while ($rowVariabelInfGain = mysql_fetch_array($sqlVariabelInfGain)) {
                    $getVariabelInfGain = $rowVariabelInfGain['inf_gain'];

                    // hitung inf gain
                    $getInfGainFix = round(($getEntropy + $getVariabelInfGain),4);

//#11# Looping perhitungan information gain, sehingga mendapatkan information gain tiap variabel. Update information gain
                    // update inf_gain (fix)
                    mysql_query("UPDATE perhitungan_c45 SET inf_gain = $getInfGainFix WHERE variabel = '$getVariabel'");
                }
                
            }
            
        }
    }
}

//#17# Insert variabel dgn information gain max ke DB pohon keputusan
function insertVariabelPohonKeputusan($variabel, $nilai_variabel)
{
    // ambil nilai inf gain tertinggi dimana hanya 1 variabel saja yg dipilih
    $sqlInfGainMaxTemp = mysql_query("SELECT distinct variabel, inf_gain FROM perhitungan_c45 WHERE inf_gain in (SELECT max(inf_gain) FROM `perhitungan_c45`) LIMIT 1");
    $rowInfGainMaxTemp = mysql_fetch_array($sqlInfGainMaxTemp);
    // hanya ambil variabel dimana jumlah kasus kelancarannya tidak kosong
    if ($rowInfGainMaxTemp['inf_gain'] > 0) {
        // ambil nilai variabel yang memiliki nilai inf gain max
        $sqlInfGainMax = mysql_query("SELECT * FROM perhitungan_c45 WHERE variabel = '$rowInfGainMaxTemp[variabel]'");
        while($rowInfGainMax = mysql_fetch_array($sqlInfGainMax)) {
            if ($rowInfGainMax['jml_kasus_ya'] == 0 AND $rowInfGainMax['jml_kasus_tidak'] == 0) {
                $kelayakan = 'Null'; // jika jml_kasus_ya = 0 dan jml_kasus_tidak = 0, maka kelayakan Null
            } else if ($rowInfGainMax['jml_kasus_ya'] !== 0 AND $rowInfGainMax['jml_kasus_tidak'] == 0) {
                $kelayakan = 'Ya'; // jika jml_kasus_ya != 0 dan jml_kasus_tidak = 0, maka kelayakan Ya
            } else if ($rowInfGainMax['jml_kasus_ya'] == 0 AND $rowInfGainMax['jml_kasus_tidak'] !== 0) {
                $kelayakan= 'Tidak'; // jika jml_kasus_ya = 0 dan jml_kasus_tidak != 0, maka kelayakan Tidak
            } else {
                $kelayakan = '?'; // jika jml_kasus_ya != 0 dan jml_kasus_tidak != 0, maka kelayakan ?
            }
            
            if (empty($variabel) AND empty($nilai_variabel)) {
//#18# Jika variabel yang diinput kosong (variabel awal) maka insert ke pohon kelayakan id_parent = 0
                // set kondisi variabel = AND variabel = nilai variabel
                $kondisiVariabel = "AND $rowInfGainMax[variabel] = ~$rowInfGainMax[nilai_variabel]~";
                // insert ke tabel pohon kelayakan
                mysql_query("INSERT INTO pohon_keputusan VALUES ('', '$rowInfGainMax[variabel]', '$rowInfGainMax[nilai_variabel]', 0, '$rowInfGainMax[jml_kasus_tidak]', '$rowInfGainMax[jml_kasus_ya]', '$kelayakan', 'Belum', '$kondisiVariabel', 'Belum')");
            }

//#19# Jika variabel yang diinput tidak kosong maka insert ke pohon keputusan dimana id_parent diambil dari tabel pohon keputusan sebelumnya (where variabel = variabel yang diinput)
            else if (!empty($variabel) AND !empty($nilai_variabel)) {
                $sqlIdParent = mysql_query("SELECT id, variabel, nilai_variabel, jml_kasus_ya, jml_kasus_tidak FROM pohon_keputusan WHERE variabel = '$variabel' AND nilai_variabel = '$nilai_variabel' order by id DESC LIMIT 1");
                $rowIdParent = mysql_fetch_array($sqlIdParent);
                    // insert ke tabel pohon keputusan
                    mysql_query("INSERT INTO pohon_keputusan VALUES ('', '$rowInfGainMax[variabel]', '$rowInfGainMax[nilai_variabel]', $rowIdParent[id], '$rowInfGainMax[jml_kasus_tidak]', '$rowInfGainMax[jml_kasus_ya]', '$kelayakan', 'Belum', '', 'Belum')");
                    
                    //#PRE PRUNING#
                    // hitung Pessimistic error rate parent dan child 
                    $perhitunganParentPrePruning = loopingPerhitunganPrePruning($rowIdParent['jml_kasus_ya'], $rowIdParent['jml_kasus_tidak']);
                    $perhitunganChildPrePruning = loopingPerhitunganPrePruning($rowInfGainMax['jml_kasus_ya'], $rowInfGainMax['jml_kasus_tidak']);
                    
                    // hitung average Pessimistic error rate child 
                    $perhitunganPessimisticChild = (($rowInfGainMax['jml_kasus_ya'] + $rowInfGainMax['jml_kasus_tidak']) / ($rowIdParent['jml_kasus_ya'] + $rowIdParent['jml_kasus_tidak'])) * $perhitunganChildPrePruning;
                    // Increment average Pessimistic error rate child
                    $perhitunganPessimisticChildIncrement += $perhitunganPessimisticChild;
                    $perhitunganPessimisticChildIncrement = round($perhitunganPessimisticChildIncrement, 4);
                    
                    // jika error rate pada child lebih besar dari error rate parent
                    if ($perhitunganPessimisticChildIncrement >= $perhitunganParentPrePruning) {
                        // hapus child (child tidak diinginkan)
                        mysql_query("DELETE FROM pohon_keputusan WHERE id_parent = $rowIdParent[id]");
                        
                        // jika jml kasus Ya lbh besar, maka keputusan == Ya
                        if ($rowIdParent['jml_kasus_ya'] > $rowIdParent['jml_kasus_tidak']) {
                            $kelayakanPrePruning = 'Ya';
                        // jika jml tdk kasus Ya lbh besar, maka keputusan == Tidak
                        } else if ($rowIdParent['jml_kasus_ya'] < $rowIdParent['jml_kasus_tidak']) {
                            $kelayakanPrePruning = 'Tidak';
                        } 
                        // update keputusan parent
                        mysql_query("UPDATE pohon_keputusan SET kelayakan = '$kelayakanPrePruning' where id = $rowIdParent[id]");
                    }
                
            }
        }
    }
    loopingKondisiVariabel();
}

//#20# Lakukan looping kondisi variabel untuk diproses pada fungsi perhitunganC45()
function loopingKondisiVariabel() 
{
    // ambil semua id dan kondisi variabel
    $sqlLoopingKondisi = mysql_query("SELECT id, kondisi_variabel FROM pohon_keputusan");
    while($rowLoopingKondisi = mysql_fetch_array($sqlLoopingKondisi)) {
        // select semua data dimana id_parent = id awal
        $sqlUpdateKondisi = mysql_query("SELECT * FROM pohon_keputusan WHERE id_parent = $rowLoopingKondisi[id] AND looping_kondisi = 'Belum'");
        while($rowUpdateKondisi = mysql_fetch_array($sqlUpdateKondisi)) {
            // set kondisi: kondisi sebelumnya yg diselect berdasarkan id_parent ditambah 'AND variabel = nilai variabel'
            $kondisiVariabel = "$rowLoopingKondisi[kondisi_variabel] AND $rowUpdateKondisi[variabel] = ~$rowUpdateKondisi[nilai_variabel]~";
            // update kondisi variabel
            mysql_query("UPDATE pohon_keputusan SET kondisi_variabel = '$kondisiVariabel', looping_kondisi = 'Sudah' WHERE id = $rowUpdateKondisi[id]");
        }
    }
}

//#22# Ambil information gain max untuk diproses pada fungsi loopingMiningC45()
function getInfGainMax($variabel, $nilai_variabel)
{
    // select inf gain max
    $sqlInfGainMaxVariabel = mysql_query("SELECT distinct variabel FROM perhitungan_c45 WHERE inf_gain in (SELECT max(inf_gain) FROM `perhitungan_c45`) LIMIT 1");
    while($rowInfGainMaxVariabel = mysql_fetch_array($sqlInfGainMaxVariabel)) {
        $inf_gain_max_variabel = "$rowInfGainMaxVariabel[variabel]";
        if (empty($variabel) AND empty($nilai_variabel)) {
            // jika variabel kosong, proses variabel dgn inf gain max pada fungsi loopingMiningC45()
            loopingMiningC45($inf_gain_max_variabel);
        } else if (!empty($variabel) AND !empty($nilai_variabel)) {
            // jika variabel tdk kosong, maka update diproses = sudah pada tabel pohon_keputusan_c45
            mysql_query("UPDATE pohon_keputusan SET diproses = 'Sudah' WHERE variabel = '$variabel' AND nilai_variabel = '$nilai_variabel'");
            // proses variabel dgn inf gain max pada fungsi loopingMiningC45()
            loopingMiningC45($inf_gain_max_variabel);
        }
    }
}

//#23# Looping proses perhitungan dimana variabel dgn information gain max yang akan diproses pada fungsi perhitunganC45()
function loopingMiningC45($inf_gain_max_variabel) 
{
    $sqlBelumAdaKeputusanLagi = mysql_query("SELECT * FROM pohon_keputusan WHERE kelayakan = '?' and diproses = 'Belum' AND variabel = '$inf_gain_max_variabel'");
    while($rowBelumAdaKeputusanLagi = mysql_fetch_array($sqlBelumAdaKeputusanLagi)) {
        if ($rowBelumAdaKeputusanLagi['id_parent'] == 0) {
            populateVariabel();
        }
        $variabel = "$rowBelumAdaKeputusanLagi[variabel]";
        $nilai_variabel = "$rowBelumAdaKeputusanLagi[nilai_variabel]";

        $kondisiVariabel = "AND $variabel = \'$nilai_variabel\'";
        mysql_query("TRUNCATE perhitungan_c45");
        mysql_query("DELETE FROM variabel WHERE variabel = '$inf_gain_max_variabel'");
        miningC45($variabel, $nilai_variabel);
    }
}

// rumus menghitung Pessimistic error rate
function perhitunganPrePruning($r, $z, $n)
{
    $rumus = ($r + (($z * $z) / (2 * $n)) + ($z * (sqrt(($r / $n) - (($r * $r) / $n) + (($z * $z) / (4 * ($n * $n))))))) / (1 + (($z * $z) / $n));
    $rumus = round($rumus, 4);
    return $rumus;
}

// looping perhitungan Pessimistic error rate
function loopingPerhitunganPrePruning($positif, $negatif)
{
    $confidenceLevel = $_POST['confidence'];

    $z = (1 / 0.5775) * ($confidenceLevel / 100); // z = batas kepercayaan (confidence treshold)
    $z = round($z, 4);
    $n = $positif + $negatif; // n = kelancaran jml kasus
    $n = round($n, 4);
    // r = perbandingan child thd parent
    if ($positif < $negatif) {
        $r = $positif / ($n);
        $r = round($r, 4);
        return perhitunganPrePruning($r, $z, $n);
    } elseif ($positif > $negatif) {
        $r = $negatif / ($n);
        $r = round($r, 4);
        return perhitunganPrePruning($r, $z, $n);
    } elseif ($positif == $negatif) {
        $r = $negatif / ($n);
        $r = round($r, 4);
        return perhitunganPrePruning($r, $z, $n);
    }
}

// update keputusan jika ada keputusan yg Null dan ?
function updateKeputusanUnknown()
{
    $sqlReplaceNull = mysql_query("SELECT id, id_parent FROM pohon_keputusan WHERE kelayakan = 'Null'");
    while($rowReplaceNull = mysql_fetch_array($sqlReplaceNull)) {
        $sqlReplaceNullIdParent = mysql_query("SELECT jml_kasus_ya, jml_kasus_tidak, kelayakan FROM pohon_keputusan WHERE id = $rowReplaceNull[id_parent]");
        $rowReplaceNullIdParent = mysql_fetch_array($sqlReplaceNullIdParent);
        if ($rowReplaceNullIdParent['jml_kasus_ya'] > $rowReplaceNullIdParent['jml_kasus_tidak']) {
            $kelayakanNull = 'Ya'; // jika jml_kasus_ya != 0 dan jml_kasus_tidak = 0, maka keputusan Ya
        } else if ($rowReplaceNullIdParent['jml_kasus_ya'] < $rowReplaceNullIdParent['jml_kasus_tidak']) {
            $kelayakanNull = 'Tidak'; // jika jml_kasus_ya = 0 dan jml_kasus_tidak != 0, maka keputusan Tidak
        }
        mysql_query("UPDATE pohon_keputusan SET kelayakan = '$kelayakanNull' WHERE id = $rowReplaceNull[id]");
    }

    $sqlReplaceUnknown = mysql_query("SELECT id, id_parent FROM pohon_keputusan WHERE kelayakan = '?' and id not in (select id_parent from pohon_keputusan)");
    while($rowReplaceUnknown = mysql_fetch_array($sqlReplaceUnknown)) {
        $sqlReplaceUnknownIdParent = mysql_query("SELECT jml_kasus_tidak, jml_kasus_ya FROM pohon_keputusan WHERE id = $rowReplaceUnknown[id_parent]");
        $rowReplaceUnknownIdParent = mysql_fetch_array($sqlReplaceUnknownIdParent);
        if ($rowReplaceUnknownIdParent['jml_kasus_ya'] > $rowReplaceUnknownIdParent['jml_kasus_tidak']) {
            $kelayakanUnknown = 'Ya'; // jika jml_kasus_ya != 0 dan jml_kasus_tidak = 0, maka keputusan Ya
        } else if ($rowReplaceUnknownIdParent['jml_kasus_ya'] < $rowReplaceUnknownIdParent['jml_kasus_tidak']) {
            $kelayakanUnknown = 'Tidak'; // jika jml_kasus_ya = 0 dan jml_kasus_tidak != 0, maka keputusan Tidak
        }
        mysql_query("UPDATE pohon_keputusan SET kelayakan = '$kelayakanUnknown' WHERE id = $rowReplaceUnknown[id]");
    }
}

function generateRuleAwal($idparent, $spasi)
{
    // ambil data pohon keputusan
    $sqlGetIdParent = mysql_query("select * from pohon_keputusan where id_parent='$idparent'");
    while($rowGetIdParent = mysql_fetch_array($sqlGetIdParent)){
        if (!empty($rowGetIdParent)) {
            // ambil data pohon keputusan dimana id = idparent
            $sqlGetId = mysql_query("select * from pohon_keputusan where id='$rowGetIdParent[id_parent]'");
            $rowGetId = mysql_fetch_array($sqlGetId);
            // jika variabel dan nilai variabel masih kosong
            if (empty($rowGetId['variabel']) AND empty($rowGetId['nilai_variabel'])){
                // insert pada db rule_c45
                mysql_query("insert into rule_c45 values ('', '$rowGetIdParent[id_parent]', '$rowGetIdParent[variabel] == $rowGetIdParent[nilai_variabel]', '$rowGetIdParent[kelayakan]')");
            } else {
                // insert pada db rule_c45
                mysql_query("insert into rule_c45 values ('', '$rowGetIdParent[id_parent]', '$rowGetId[variabel] == $rowGetId[nilai_variabel] AND $rowGetIdParent[variabel] == $rowGetIdParent[nilai_variabel]', '$rowGetIdParent[kelayakan]')");
            }
            // looping dirinya sendiri
            generateRuleAwal($rowGetIdParent['id'], $spasi + 1);
        }
    }
}

function generateRuleLooping()
{
    // ambil data rule
    $sqlGetDataRule = mysql_query("select * from rule_c45 order by id");
    while($rowGetDataRule=mysql_fetch_array($sqlGetDataRule)){
        if (!empty($rowGetDataRule)) {
            // ambil idparent rule dimana id = idparent
            $sqlGetIdParentUpdateRule = mysql_query("select id_parent from pohon_keputusan where id = '$rowGetDataRule[id_parent]'");
            $rowGetIdParentUpdateRule=mysql_fetch_array($sqlGetIdParentUpdateRule);
            
            $sqlGetIdUpdateRule = mysql_query("select * from pohon_keputusan where id = '$rowGetIdParentUpdateRule[id_parent]'");
            while($rowGetIdUpdateRule=mysql_fetch_array($sqlGetIdUpdateRule)){
                // bentuk rule
                $rule = "$rowGetIdUpdateRule[variabel] == $rowGetIdUpdateRule[nilai_variabel] AND $rowGetDataRule[rule]";
                // update rule
                mysql_query("update rule_pohon_keputusan set rule = '$rule', id_parent = '$rowGetIdParentUpdateRule[id_parent]' where id = '$rowGetDataRule[id]'");
            }
            
            // ambil data pohon dimana idparent = 0 (root)
            $sqlGetDataPohonKeputusan = mysql_query("select * from pohon_keputusan where id_parent = 0");
            while($rowGetDataPohonKeputusan=mysql_fetch_array($sqlGetDataPohonKeputusan)){
                // jika idparent rule == id pohon
                if ($rowGetDataRule['id_parent'] == $rowGetDataPohonKeputusan['id']){
                    // update rule set id = id rule
                    mysql_query("update rule_c45 set id_parent = 0 where id = '$rowGetDataRule[id]'");
                }
            }
        }
    }
}

function generateRuleFinalPrePruning()
{
    // panggil fungsi generateRuleAwal()
    generateRuleAwal("0", 0);
    
    // ambil data rule
    $sqlUpdateRule = mysql_query("select * from rule_c45 order by id" );
    while($rowUpdateRule=mysql_fetch_array($sqlUpdateRule)){
        if (!empty($rowUpdateRule)) {
            // jika idparent rule == 0
            if ($rowUpdateRule['id_parent'] !== 0){
                // lakukan fungsi generateRuleLooping()
                generateRuleLooping();
                // delete rule dimana keputusan == ?
                mysql_query("delete from rule_c45 where kelayakan = '?'");
            }
        }
    }
}

function insertRuleC45PrePruning()
{
    // ambil data pada db rule_pohon_keputusan
    $sqlRuleC45 = mysql_query("SELECT id, rule, kelayakan FROM rule_c45");
    while($rowRuleC45 = mysql_fetch_array($sqlRuleC45)) {
        $RuleC45 = "$rowRuleC45[rule]";
        // explode string ' AND ' utk mendapatkan variabel
        $explodeRuleC45 = explode(" AND ", $RuleC45);
        foreach ($explodeRuleC45 as $dataExplodeRuleC45) {
            // explode string ' == ' utk mendapatkan nilai variabel
            $dataFixRuleC45 = explode(" == ", $dataExplodeRuleC45);
            // insert into db
            mysql_query("INSERT INTO rule_prediksi VALUES('', $rowRuleC45[id], '$dataFixRuleC45[0]', '$dataFixRuleC45[1]', '$rowRuleC45[kelayakan]', '', 'C45')");
        }
    }
}