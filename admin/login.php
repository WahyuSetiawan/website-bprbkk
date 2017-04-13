<div id="loginForm">
	<div class="headLoginForm" align="center">
		<h1>Login Karyawan</h1>
	</div>
	<?php if($_GET['status'] && $_GET['status'] == 'error'){ ?>
		<div class="loginMessage" align="center">
			Username atau Password salah
		</div>
	<?php } ?>
	<div class="fieldLogin">
		<form method="POST" action="loginProses.php">
			<div class="loginRow">
				<label class="loginLabel">Username</label>
				<input type="text" name="username" class="loginInput" required>
			</div>
			<div class="loginRow">
				<label class="loginLabel">Password</label>
				<input type="password" name="password" class="loginInput" required>
			</div>
			<div class="loginRow">
				<label class="loginLabel">&nbsp;</label>
				<input type="submit"class="button" value="Login">
			</div>
		</form>
	</div>
</div> 
