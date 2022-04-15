<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Pengajuan Baru - KPRI HUSADA</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>icon.ico" type="image/x-icon" />
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- bootstrap 3.0.2 -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- font Awesome -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />

	
	<link href="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
	
	<?php foreach($js_files as $file) { ?>
		<script src="<?php echo $file; ?>"></script>
	<?php } ?>

	<link href="<?php echo base_url(); ?>assets/theme_admin/css/custome.css" rel="stylesheet" type="text/css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
</head>
<body>

<div class="container">

	<?php $this->load->view('themes/member_menu_v'); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid box-primary">
				<div class="box-header">
					<h3 class="box-title">Formulir Pengajuan Pinjaman</h3>
				</div>
				
				<?php if($this->session->flashdata('form_key_error') == 'Y') { ?>
				<div class="box-body">
					<br><br>
					<div class="alert alert-warning alert-dismissable">
						<i class="fa fa-warning"></i>
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						Kesalahan tehnis session, silahkan Ulangi.
					</div>
				</div>
				<?php } ?>

				<?php if($ada_pending) { ?>
					<div class="box-body">
						<br>
						<div class="alert alert-danger">
							<i class="fa fa-warning"></i> Masih ada Pengajuan yang menunggu Konfirmasi.<br>
							Tidak dapat membuat Pengajuan baru.<br>
							Silahkan Batalkan Pengajuan terakhir jika ingin ubah Mengajukan Yang Baru.<br><br>
							<a href="<?php echo site_url('member/pengajuan'); ?>" class="btn btn-primary">Lihat Pengajuan</a>
						</div>
					</div>
				<?php } else { ?>
				<?php echo form_open(''); ?>
					<div class="box-body">

						<?php if($tersimpan == 'N') { ?>
						<div class="box-body">
							<div class="alert alert-danger alert-dismissable">
								<i class="fa fa-warning"></i>
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								Pengajuan gagal terkirim, silahkan periksa kembali dan ulangi.
							</div>
						</div>
						<?php } ?>

						<div class="form-group">
							<?php 
							$attr_form = 'jenis';
							$attr_form_label = 'Jenis';
							$options = array(
								'Biasa'		=> 'Biasa',
								'Darurat'	=> 'Darurat',
								'Barang'		=> 'Barang'
								);
							if($prot_sisa) {
								unset($options['Biasa']);
								unset($options['Barang']);
								echo '<div class="callout callout-danger">
											Anda masih ada <strong>'.$prot_sisa.'</strong> Sisa Angsuran (lebih dari 1 bln), hanya dapat milih Jenis Darurat.
											
											<a href="'.site_url('member/lap_pinjaman').'" class="btn btn-primary btn-xs">Lihat Laporan Pinjaman</a>
										</div>';
							}
							echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
									<div>';
							echo form_dropdown($attr_form, $options, set_value($attr_form, 'Biasa'), 'id="'.$attr_form.'" class="form-control" style="width: 250px;"');
							echo '</div>';
							?>
						</div>

						<div class="form-group">
							<?php
							$data = array(
								'name'		=> 'nominal',
								'id'		=> 'nominal',
								'class'		=> 'form-control',
								'value'		=> '',
								'maxlength'	=> '255',
								'style'		=> 'width: 250px'
								);
							echo form_label('Nominal', 'nominal');
							echo form_input($data);
							echo form_error('nominal', '<p style="color: red;">', '</p>');
							?>
						</div>


						<div class="form-group">
							<?php
							$attr_form = 'lama_ags';
							$attr_form_label = 'Lama Angsuran';
							echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
									<div>';
							echo form_dropdown($attr_form, $lama_ags, set_value($attr_form, ''), 'id="'.$attr_form.'" class="form-control" style="width: 100px;" ');
							echo '</div><div id="div_lama_ags"></div>';
							?>
						</div>

						<div class="form-group">
							<?php 
							$attr_form = 'jenis_bayar';
							$attr_form_label = 'Jenis Pembayaran';
							$options = array(
								'Tunai'			=> 'Tunai',
								'Bank Transfer'=> 'Bank Transfer'
								);
							echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
									<div>';
							echo form_dropdown($attr_form, $options, set_value($attr_form, 'Tunai'), 'id="'.$attr_form.'" class="form-control" style="width: 250px;"');
							echo '</div>';
							?>
							<p class="help-block" id="div_jenis_bayar"></p>
						</div>

						<div class="form-group" id="group_pil_rek" style="display: none;">
							<?php
							$attr_form = 'pil_rek';
							$attr_form_label = 'Pilih Rekening';
							$options = array(
								'1'	=> 'Rekening Danamon - '.$userinfo->no_rek,
								'2'	=> 'Rekening Lain'
								);
							echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
									<div>';
							echo form_dropdown($attr_form, $options, set_value($attr_form, '1'), 'id="'.$attr_form.'" class="form-control" style="width: 250px;"');
							echo '</div>';
							?>
						</div>

						<div class="form-group" id="group_rek_lain" style="display: none;">
							<?php 
							$attr_form = 'rek_lain';
							$attr_form_label = 'Rekening Lain (Contoh : Romeo (tekan enter) BRI (tekan enter) & NO. Rekening)';
							echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
							<div>
								<textarea name="'.$attr_form.'" id="'.$attr_form.'" class="form-control" rows="2">'.set_value($attr_form, "").'</textarea>
							</div>';
							?>
							<p class="help-block"><i class="fa fa-credit-card"></i> Isi Rekening Bank Lain setelah klik cek validasi untuk menghindari penolakan sistem.</p>
						</div>

						<div class="form-group">
							<?php
							$attr_form = 'keterangan';
							$attr_form_label = 'Keterangan';
							$data = array(
								'name'		=> $attr_form,
								'id'		=> $attr_form,
								'class'		=> 'form-control',
								'value'		=> '',
								'maxlength'	=> '255',
								'style'		=> 'width: 350px'
								);
							echo form_label($attr_form_label, $attr_form);
							echo form_input($data);
							echo form_error($attr_form, '<p style="color: red;">', '</p>');
							echo '<br>'; ?>
						</div>
						<div class="form-group">
							<div><a href="javascript:void(0);" name="btn_simulasi" id="btn_simulasi" class="btn btn-warning btn-sm">Cek Validasi</a></div>
							<div id="div_simulasi"></div>
						</div>

					</div><!-- /.box-body -->
					<div class="box-footer">
						<input type="hidden" name="form_key" id="form_key" value="">
						<div id="info_validasi" class="callout callout-info">Silahkan Lengkapi Data Diatas, kemudian Klik tombol Cek Validasi.</div>
						<?php
						// submit
						$data = array(
							'name' 		=> 'submit',
							'id' 			=> 'submit',
							'class' 		=> 'btn btn-primary',
							'value'		=> 'true',
							'type'	 	=> 'submit',
							'content' 	=> 'Kirim Pengajuan',
							'disabled'	=> 'disabled'
							);
						echo form_button($data);
						?>
					</div>
					<?php echo form_close(); ?>
				<?php } ?>
			</div><!-- box-primary -->
		</div><!-- col -->
	</div><!-- row -->

</div>


	<!-- jQuery 2.0.2 -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table-id-ID.js" type="text/javascript"></script>


<script type="text/javascript">
	$(function() {
		$('#nominal').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			$(this).val(number_format(n, 0, '', '.'));
		});
		$('#jenis').on('change', function() {
			oc_lama_ags();
		});
		oc_lama_ags();
		$('#jenis, #nominal, #lama_ags').on('change keyup paste', function() {
			$('#form_key').val('');
			$('#submit').attr('disabled', true);
			$('#info_validasi').html('Silahkan Lengkapi Data Diatas, kemudian Klik tombol Cek Validasi.');
		});
		$('#btn_simulasi').click(function(event) {
			simulasikan();
		});

		$('#jenis_bayar').on('change', function() {
			var var_jenis_bayar = $(this).val();
			if(var_jenis_bayar == 'Tunai') {
				$('#group_rek_lain').hide();
				$('#group_pil_rek').hide();
			} else {
				//$('#group_rek_lain').show();				
				$('#group_pil_rek').show();
				var var_pil_rek = $('#pil_rek').val();
				if(var_pil_rek == '1') {
					$('#group_rek_lain').hide();
				} else {
					$('#group_rek_lain').show();
					$('#rek_lain').focus();
				}
			}
		});
		$('#jenis_bayar').change();

		$('#pil_rek').on('change', function() {
			var var_pil_rek = $(this).val();
			if(var_pil_rek == '1') {
				$('#group_rek_lain').hide();
			} else {
				$('#group_rek_lain').show();
				$('#rek_lain').focus();
			}
		});
		$('#pil_rek').change();


	});

	function simulasikan() {
		$('#info_validasi').html('<i class="fa fa-refresh fa-spin"></i> Loading...');
		var jenis = $('#jenis').val();
		var var_nominal = $('#nominal').val();
		var num_nominal = parseInt(var_nominal.replace(/\D/g, ''), 10);
		var var_lama_ags = $('#lama_ags').val();
		var var_jenis_bayar = $('#jenis_bayar').val();
		var var_pil_rek = $('#pil_rek').val();
		var var_rek_lain = $('#rek_lain').val();
		var var_ket = $('#keterangan').val();
		var sisa_ags_rp = '<?php echo $sisa_ags_rp; ?>';

		$('#div_jenis_bayar').html('');
		$('#jenis_bayar option[value="Tunai"]').removeAttr('disabled');

		var stop_sblm_ajax = false;
		if(var_ket.trim() == '') {
			$('#info_validasi').html('<div class="text-red"><i class="fa fa-warning"></i> Validasi Error: Keterangan harus diisi.</div>');
			$('#submit').attr('disabled', 'disabled');
			stop_sblm_ajax = true;
		}
		if(num_nominal >= 5000000) {
			$('#jenis_bayar option[value="Tunai"]').attr('disabled', 'disabled');
			$('#jenis_bayar').val('Bank Transfer').change();
			$('#div_jenis_bayar').html('<span class="text-blue"><i class="fa fa-credit-card"></i> Diatas 5jt, harus Bank Transfer</span>');
		}
		if(stop_sblm_ajax) {
			return;
		}
		$.ajax({
			url: '<?php echo site_url('member/simulasi')?>',
			type: 'POST',
			dataType: 'json',
			data: {
				'nominal': var_nominal, 
				'lama_ags': var_lama_ags, 
				'jenis': jenis, 
				'jenis_bayar': var_jenis_bayar, 
				'pil_rek': var_pil_rek, 
				'sisa_ags_rp': sisa_ags_rp, 
				'rek_lain': var_rek_lain
			}
		})
		.done(function(out) {
			if(out.boleh) {
				$('#info_validasi').html('<span class="text-green"><i class="fa fa-check-circle"></i> Validasi OK</span>');
				$('#div_simulasi').html(out.html);
				$('#submit').removeAttr('disabled');
				$('#form_key').val(out.form_key);
			} else {
				$('#info_validasi').html('<div class="text-red"><i class="fa fa-warning"></i> Validasi Ditolak<br>'+out.error+'</div>');
				$('#div_simulasi').html(out.html);
				$('#submit').attr('disabled', 'disabled');
			}
			//console.log("success");
		})
		.fail(function() {
			alert('Koneksi Error, Silahkan Ulangi');
			//console.log("error");
		});
	}

	function oc_lama_ags() {
		var jenis = $('#jenis').val();
		if(jenis == 'Darurat') {
			$('#lama_ags').hide();
			$('#div_lama_ags').html('<input value="1 bln" disabled="disabled" class="form-control" style="width: 35px;">');
			$('#div_lama_ags').show();
		} else {
			$('#div_lama_ags').html('');
			$('#div_lama_ags').hide;
			$('#lama_ags').show();
		}		
	}



</script>

</body>
</html>