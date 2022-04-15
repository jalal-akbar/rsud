<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Login - KPRI HUSADA RSUD BIMA</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>icon.ico" type="image/x-icon" />
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- bootstrap 3.0.2 -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- font Awesome -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/custome.css" rel="stylesheet" type="text/css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
</head>

<body>
	<div class="form-box" id="login-box">
		<!-- <div class="alert alert-info">
			<center>
				<h4><strong>" INFORMASI & PENGUMUMAN "</strong></h4>
			</center><br />
			<p>
			<h4><strong> <i class="fa fa-money"></i> Pembatasan pinjaman sudah berlaku & Pengajuan Pinjaman diatas Rp. 5.000.000 otomatis akan ditransfer. </strong></h4>

			<h4><strong> <i class="fa fa-exclamation-triangle"></i> Untuk Top-up pinjaman bisa dilakukan jika sisa angsuran 1 bulan. </strong></h4>
			<h4><strong> <i class="fa fa-user"></i> Jika ada kendala Hub. ....</strong></h4><br />

			<center>'Bersama Bisa'</center>

			</p>
		</div> -->
		<div class="header">
			<!-- <img height='60' src="<?php echo base_url() . 'assets/theme_admin/img/tulisan3.png'; ?>"> -->
			<center>
				<h3><strong>APLIKASI</strong></h3>
				<h4><strong>KPRI HUSADA RSUD BIMA</strong></h4>
			</center>
		</div>
		<form action="" method="post">
			<div class="body bg-gray">
				<center>
					<?php if ($jenis == 'member') { ?>
						<h4>Member Login - <a href="<?php echo site_url('login'); ?>" class="btn btn-primary">Admin/Operator</a></h4>
					<?php } else { ?>
						<h4>Admin / Operator Login - <a href="<?php echo site_url('member'); ?>" class="btn btn-primary">Member</a></h4>
					<?php } ?>
				</center>

				<?php
				if (!empty($pesan)) {
					echo '<div style="color: red;">' . $pesan . '</div>';
				}
				?>
				<div class="form-group">
					<input type="text" name="u_name" id="u_name" class="form-control" placeholder="NIK Karyawan" value="<?php echo set_value('u_name'); ?>" />
					<?php echo form_error('u_name', '<p style="color: red;">', '</p>'); ?>
				</div>
				<div class="form-group">
					<input type="password" name="pass_word" class="form-control" placeholder="Password" />
					<?php echo form_error('pass_word', '<p style="color: red;">', '</p>'); ?>
				</div>
				<button type="submit" class="btn btn-primary btn-block">Login</button>
			</div>
			<div class="footer">
				<center>&copy; Copyright <?php echo date('Y'); ?> | Developed by CV. Sinbad Cemerlang</center>
			</div>
		</form>
	</div>

	<!-- jQuery 2.0.2 -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/bootstrap.min.js" type="text/javascript"></script>


	<script type="text/javascript">
		$(document).ready(function() {
			$('#u_name').focus();
		});
	</script>

</body>

</html>