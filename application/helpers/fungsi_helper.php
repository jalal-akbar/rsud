<?php

if (!function_exists('get_gaji_net')) {
	function get_gaji_net() {
		$out = array(
			0 => array(
				'nama' 		=> 'Default',
				'nominal' 	=> '0'
			),
			1 => array(
				'nama' 		=> 'Harian',
				'nominal' 	=> '3000000'
			),
			2 => array(
				'nama' 		=> 'Opr/Staff',
				'nominal' 	=> '4000000'
			),
			3 => array(
				'nama' 		=> 'Foreman',
				'nominal' 	=> '4500000'
			),
			4 => array(
				'nama' 		=> 'Supervisor',
				'nominal' 	=> '6500000'
			),
			5 => array(
				'nama' 		=> 'Chief',
				'nominal' 	=> '8000000'
			),
			6 => array(
				'nama' 		=> 'Mgr',
				'nominal' 	=> '20000000'
			)
		);
		return $out;
	}
}

if (!function_exists('get_pengajuan_max')) {
	function get_pengajuan_max($gaji_net) {
		return ( (int) $gaji_net * 0.75 );
	}
}

if (!function_exists('jin_date_ina')) {
	function jin_date_ina($date_sql, $tipe = 'full', $time = false) {
		$date = '';
		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		if($time) {
			$exp = explode(' ', $date_sql);
			$exp = explode('-', $exp[0]);
			if(count($exp) == 3) {
				$bln = $exp[1] * 1;
				$date = $exp[2].' '.$nama_bulan[$bln].' '.$exp[0];
			}		
			$exp_time = $exp = explode(' ', $date_sql);
			$date .= ' jam ' . substr($exp_time[1], 0, 5);
		} else {
			$exp = explode('-', $date_sql);
			if(count($exp) == 3) {
				$bln = $exp[1] * 1;
				if($bln > 0) {
					$date = $exp[2].' '.$nama_bulan[$bln].' '.$exp[0];
				}
			}
		}
		return $date;
	}
}

if (!function_exists('jin_nama_bulan')) {
	function jin_nama_bulan($bln, $tipe='full') {
		$bln = $bln * 1;
		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		return $nama_bulan[$bln];
	}
}

if (!function_exists('nsi_round')) {
	function nsi_round($x) {
		//$x = ceil($x / 100) * 100;
		return $x;
	}
}




