<?php
include "../config/koneksi.php";
include "../config/library.php";
include "../config/fungsi_indotgl.php";
include "../config/hitungWaktu.php";

// Bagian Home
if ($_GET[module]=='home'){
    echo "<h2><center>Selamat Datang di aplikasi </center></h2>
    <table width='100%'>
        <tr>
            <td>
                <h2 align='center'>
                    <br>
                </h2>
            </td>
        </tr>
    </table>";
}

// Modul Data Nasabah
elseif ($_GET[module]=='data_anggota'){
    include "modul/data_anggota.php";
}

// Modul C4.5
elseif ($_GET[module]=='c45'){
    include "modul/c45.php";
}

// Modul Lain-Lain
elseif ($_GET[module]=='about'){
    include "modul/about.php";
}

// Modul Lain-Lain
elseif ($_GET[module]=='bantuan'){
    include "modul/bantuan.php";
}

// Modul Penentu Keputusan
elseif ($_GET[module]=='prediksi'){
    include "modul/prediksi.php";
}

// Modul Lihat Nilai
elseif ($_GET[module]=='lihat_nilai'){
    include "modul/lihat_nilai.php";
}

// Logout
elseif ($_GET[module]=='logout'){
	include "admin/login.php";
    session_destroy();
    header('location:../admin/media.php?module=login');
}

else{
    echo "<p><b>Silahkan Login Terlebih Dahulu</b></p>";
}
?>