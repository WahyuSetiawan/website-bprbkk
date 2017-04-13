<?php
	include "../config/koneksi.php";

	if($_POST['username'] && $_POST['password']){
		$username	= strtolower($_POST['username']);
		$password	= md5($_POST['password']);
		$sql		= "select * from karyawan where username = '$username' and password = '$password'";
		
		$query		= mysql_query($sql);
		$result		= mysql_fetch_array($query);
		$data		= mysql_num_rows($query);

		if($data == 1){
			# set session login
			session_start();
			
			$_SESSION['username'] = $data["username"];
			$_SESSION['userLogin'] = true;

			header('location:../admin/media.php?module=home');
		}else{
			header('location:../admin/media.php?module=login&status=error');
		}
	}else{
		header('location:../admin/media.php?module=login&status=error');
	}
?>