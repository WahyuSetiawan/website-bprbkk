<?php
// Fungsi ini digunakan untuk menghitung waktu mulai
function timer_start() {
    global $timestart;
    $mtime = explode( ' ', microtime() );
    $timestart = $mtime[1] + $mtime[0];
    return true;
}
 
// Fungsi ini digunakan untuk menghitung waktu selesai
function timer_stop( $precision = 3 ) {
    global $timestart, $timeend;
    $mtime = microtime();
    $mtime = explode( ' ', $mtime );
    $timeend = $mtime[1] + $mtime[0];
    $timetotal = $timeend - $timestart;
    $r = number_format( $timetotal, $precision );
    return $r;
}
