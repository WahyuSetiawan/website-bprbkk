<?php
	error_reporting(0);
?>

<html>
	<head>
		<title>Aplikasi Pohon Keputusan C4.5</title>
		<link  href="../include/image/logo.png" rel="shortcut icon" type="image/png" />
		<link rel="stylesheet" href="../include/css/style.css" type="text/css" />
		<script type="text/javascript" src="../include/javascripts/jquery-1.11.0.min.js"></script>
	</head>
	
	<body>
		<div id="outer">
			<div id="header">
				<img src="../include/image/bprbkk.jpg" width="950px" height="300px">
			</div>
			<div style="clear:both"></div>
			
			<div id="dbody">
				<div id="dcontent">
					<?php session_start(); ?>
					<?php if(isset($_SESSION['userLogin']) && $_SESSION['userLogin']){ ?>
						<div id="kiri">
							<?php include "sidebar.php";?>
						</div>
						<div id="tengah">
							<div class="bgf1"><div class="bgf2"><div class="bgf3"></div></div></div>
							<div style="clear:both"></div>
							
							<div class="content">
								<?php include "content.php"; ?>
							</div>
							<div style="clear:both"></div>
							<div class="bgf1"><div class="bgf2"><div class="bgf3"></div></div></div>
							<div class="clear10"></div>
						</div>
						<div class="clear10"></div>
					<?php }else{ ?>
						<!-- redirect to login page -->
						<?php if($_GET['module'] == 'login'){ ?>
							<?php include "login.php"; ?>
						<?php }else{ ?>
							<?php header('location:../admin/media.php?module=login'); ?>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
			<div style="clear:both"></div>
			<div id="footer">
				<p>Copyright&copy; 2015 by:<br />
				<b>Nurul Rahmawati</br>
				14.21.0823</b></p>
				<div style="clear:both"></div>
			</div>
		</div>
	</body>
</html>