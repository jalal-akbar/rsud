<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller {

	public $data = array ('pesan' => '');
	
	public function __construct () {
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Member_m','member', TRUE);
	}

	public function _cek_login() {
		if ($this->session->userdata('login') == FALSE) {
			redirect('member');
		}
	}
	
	public function index() {
		// status user login = BENAR, pindah ke halaman home
		if ($this->session->userdata('login') == TRUE && $this->session->userdata('level') == 'member') {
			redirect('member/view');
		} else {
			// status login salah, tampilkan form login
			// validasi sukses
			if($this->member->validasi()) {
				// cek di database sukses
				if($this->member->cek_user()) {
					redirect('member/view');
				} else {
					// cek database gagal
					$this->data['pesan'] = 'Username atau Password salah.';
				}
			} else {
				// validasi gagal
         }
         $this->data['jenis'] = 'member';
         $this->load->view('themes/login_form_v', $this->data);
		}
	}

	function simulasi() {
		sleep(1);
		$boleh = false;
		$error = array();
		$this->load->helper('fungsi');
		$jenis = $this->input->post('jenis');
		$jenis_bayar = $this->input->post('jenis_bayar');
		$pil_rek = $this->input->post('pil_rek');
		$rek_lain = trim($this->input->post('rek_lain'));
		if($jenis == 'Darurat') {
			$lama_ags = 1;
		} else {
			$lama_ags = $this->input->post('lama_ags');
		}
		$nominal = $this->input->post('nominal');
		$nominal = preg_replace("/[^0-9]/", "", $nominal);
		$sisa_ags_rp = (int) $this->input->post('sisa_ags_rp');

		$min_pinjaman = 50000;
		if($nominal < $min_pinjaman) {
			$error[] = 'Minimal Pinjaman Rp '.number_format($min_pinjaman);
		}

		if ( ($pil_rek == '2') && ($jenis_bayar == 'Bank Transfer') && ($rek_lain == '') ) {
			$error[] = 'Rekening Lain Harus diisi.';
		}

		$this->load->model('bunga_m');

		if(empty($error)) {
			$arr = array();
			$conf_bunga = $this->bunga_m->get_key_val();
			$denda_hari = sprintf('%02d', $conf_bunga['denda_hari']);
			$biaya_admin = $conf_bunga['biaya_adm'];
			$persen_bunga = $conf_bunga['bg_pinjam'];
			$angsuran_pokok = ($nominal / $lama_ags);
			$tgl_pinjam = date('Y-m-d');
			$tgl_tempo_next = 0;
			if($conf_bunga['pinjaman_bunga_tipe'] == 'A') {
				$biaya_bunga = ($angsuran_pokok * $persen_bunga) / 100;
			} else {
				$biaya_bunga = ($nominal * $persen_bunga) / 100;
			}
			$jumlah_ags = $angsuran_pokok + $biaya_admin + $biaya_bunga;

			// cek pengajuan max
			$user_id = $this->session->userdata('u_name');
			$anggota = $this->member->get_data_anggota($user_id);
			$gaji_net_arr = get_gaji_net();
			$gaji_net = (int) $gaji_net_arr[$anggota->gaji_net]['nominal'];
			$pengajuan_max = get_pengajuan_max($gaji_net);
			//print_r($gaji_net);
			//print_r($pengajuan_max);
			//print_r($jumlah_ags);
			// sisa angsuran rp
			$total_ags_ajuan = $jumlah_ags + $sisa_ags_rp;
			if ($total_ags_ajuan > $pengajuan_max) {
				$error[] = 'Pengajuan Pinjaman terlalu besar.<br><br>
					Ada tagihan Angsuran per bln <strong>Rp '.number_format($sisa_ags_rp).'</strong><br> 
					Yang akan diajukan Angsuran per bln <strong>Rp '.number_format($jumlah_ags).'</strong><br>
					___________________________________________ +<br>
					Total Angsuran per bln <strong>Rp '.number_format($total_ags_ajuan).'</strong><br>
					<br>
					Batas Angsuran per bln <strong>Rp '.number_format($pengajuan_max).'</strong><br>
					Total Angsuran Melebihi dari Batas Angsuran per bulan';
			}
		}

		$html = '';
		$form_key = '';
		if(empty($error)) {
			$form_key = uniqid();
			$this->session->set_flashdata('form_key', $form_key);
			$boleh = true;
			for ($i=1; $i <= $lama_ags; $i++) {
				$odat = array();
				$odat['angsuran_pokok'] = number_format($angsuran_pokok);
				$odat['tgl_pinjam'] = $tgl_pinjam;
				
				$odat['biaya_adm'] = number_format($biaya_admin);
				$odat['biaya_bunga'] = number_format($biaya_bunga);
				$odat['jumlah_ags'] = number_format($jumlah_ags);
				$tgl_tempo_var = substr($tgl_pinjam, 0, 7) . '-01';
				$tgl_tempo = date("Y-m-d", strtotime($tgl_tempo_var . " +".$i." month"));
				$tgl_tempo = substr($tgl_tempo, 0, 7) . '-' . $denda_hari;
				$odat['tgl_tempo'] = jin_date_ina($tgl_tempo);
				$arr[] = $odat;
			}
			if(!empty($arr)) {
				$html .= '<h3>Simulasi Pinjaman</h3>';
				$html .= '<table class="table">';
				$html .= '	<tr>';
				$html .= '		<th style="text-align: center;">Ags Ke</th>';
				$html .= '		<th style="text-align: center;">Tanggal Tempo</th>';
				$html .= '		<th style="text-align: center;">Angsuran Pokok</th>';
				$html .= '		<th style="text-align: center;">Biaya Bunga</th>';
				$html .= '		<th style="text-align: center;">Biaya Admin</th>';
				$html .= '		<th style="text-align: center;">Jumlah Tagihan</th>';
				$html .= '	</tr>';
				$no = 1;
				foreach ($arr as $val) {
					$html .= '<tr>';
					$html .= '	<td style="text-align: center;">'.$no.'</td>';
					$html .= '	<td style="text-align: center;">'.$val['tgl_tempo'].'</td>';
					$html .= '	<td style="text-align: center;">'.$val['angsuran_pokok'].'</td>';
					$html .= '	<td style="text-align: center;">'.$val['biaya_bunga'].'</td>';
					$html .= '	<td style="text-align: center;">'.$val['biaya_adm'].'</td>';
					$html .= '	<td style="text-align: center;">'.$val['jumlah_ags'].'</td>';
					$html .= '</tr>';
					$no++;
				}
				$html .= '</table>';
			}
		} else {
			$boleh = false;
		}
		$error_txt = implode('<br>', $error);
		$out = array('html' => $html, 'boleh' => $boleh, 'error' => $error_txt, 'form_key' => $form_key);
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();		
	}


	function edit() {
		$this->load->model('pinjaman_m');
		$res = $this->pinjaman_m->pengajuan_edit();
		echo $res;
	}

	public function view() {
		$this->_cek_login();
		$this->load->helper('fungsi');
		$this->load->model('lap_kas_anggota_m');
		$user_id = $this->session->userdata('u_name');
		$this->data['user_id'] = $user_id;
		$this->data['row'] = $this->member->get_data_anggota($user_id);
		$this->data["data_jns_simpanan"] = $this->lap_kas_anggota_m->get_jenis_simpan();
		$this->data['data_pengajuan'] =  $this->member->get_last_pengajuan();
		$this->load->view('themes/member_v', $this->data);
	}

	public function pengajuan() {
		$this->_cek_login();
		$this->load->model('pinjaman_m');
		$this->load->helper('fungsi');
		//editable
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap3-editable/css/bootstrap-editable.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap3-editable/js/bootstrap-editable.min.js';

		$this->data['jenis_ags'] = $this->pinjaman_m->get_data_angsuran();

		$this->load->view('themes/member_pengajuan_v', $this->data);		
	}

	public function ajax_pengajuan() {
		$this->load->model('member_m');
		$out = $this->member_m->get_pengajuan();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();		
	}

	public function pengajuan_batal($id) {
		$this->_cek_login();
		$this->load->model('member_m');
		if($this->member_m->pengajuan_batal($id)) {
			$this->session->set_flashdata('ajuan_batal', 'Y');
		} else {
			$this->session->set_flashdata('ajuan_batal', 'N');
		}
		redirect('member/pengajuan');

	}


	public function pengajuan_baru() {
		$this->_cek_login();

		$user_id = $this->session->userdata('u_name');
		// userinfo
		$this->data['userinfo'] = $this->member->get_data_anggota($user_id);

		$this->load->model('pinjaman_m');
		// cek pending pinjaman
		$ada_pending = false;
		$cek_pending = $this->pinjaman_m->cek_data_pengajuan_pending($user_id);
		if($cek_pending > 0) {
			$ada_pending = true;
		}
		$this->data['ada_pending'] = $ada_pending;
		// cek sisa angsuran
		$prot_sisa = false;
		$cek_sisa_ags = $this->pinjaman_m->get_sisa_pinjaman_minus_satu_bln($user_id);
		if($cek_sisa_ags['sisa_ags'] > 1) {
			$prot_sisa = $cek_sisa_ags['sisa_ags'];
		}
		$this->data['prot_sisa'] = $prot_sisa;
		$this->data['sisa_ags_rp'] = $cek_sisa_ags['sisa_ags_rp'];

		$lama_ags = $this->pinjaman_m->get_data_angsuran();
		$lama_ags_arr = array();
		foreach ($lama_ags as $row) {
			$lama_ags_arr[$row->ket] = $row->ket . ' bln';
		}
		$this->data['lama_ags'] = $lama_ags_arr;
		$this->data['tersimpan'] = '';
		if ($this->input->post('submit')) {
			if($this->input->post('form_key') == $this->session->flashdata('form_key')) {
				if($this->member->validasi_pengajuan()) {
					$pengajuan_simpan = $this->member->pengajuan_simpan();
					if($pengajuan_simpan) {
						$this->session->set_flashdata('ajuan_baru', 'Y');
						redirect('member/pengajuan');
					} else {
						$this->data['tersimpan'] = 'N';
					}
				}

			} else {
				$this->session->set_flashdata('form_key_error', 'Y');
				redirect('member/pengajuan_baru');
			}
		}
		$this->load->helper('fungsi');
		//number_format
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';
		$this->load->view('themes/member_pengajuan_baru_v', $this->data);		
	}

	public function pinjaman_detil($id) {
		$this->_cek_login();
		$this->load->helper('fungsi');
		$this->load->model('pinjaman_m');
		$this->data['simulasi_tagihan'] = $this->pinjaman_m->get_simulasi_pinjaman($id);
		$this->load->view('themes/member_pinjaman_detil_v', $this->data);
	}


	public function lap_simpanan() {
		$this->_cek_login();
		$this->load->helper('fungsi');
		$this->load->view('themes/member_simpanan_v', $this->data);
	}

	public function ajax_lap_simpanan() {
		$this->load->model('member_m');
		$out = $this->member_m->get_simpanan();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();		
	}

	public function lap_pinjaman() {
		$this->_cek_login();
		$this->load->helper('fungsi');
		$this->load->view('themes/member_pinjaman_v', $this->data);
	}

	public function ajax_lap_pinjaman() {
		$this->load->model('member_m');
		$out = $this->member_m->get_pinjaman();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();		
	}


	public function lap_bayar() {
		$this->_cek_login();
		$this->load->helper('fungsi');
		$this->load->view('themes/member_bayar_v', $this->data);
	}

	public function ajax_lap_bayar() {
		$this->load->model('member_m');
		$out = $this->member_m->get_bayar();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();		
	}
	public function ubah_pass() {
		$this->_cek_login();
		$this->data['tersimpan'] = '';
		if ($this->input->post('submit')) {
			if($this->member->validasi_ubah_pass()) {
				if ($this->input->post('password_baru') == $this->input->post('ulangi_password_baru')) {
					if($this->member->simpan()) {
						$this->data['tersimpan'] = 'Y';
					} else {
						$this->data['tersimpan'] = 'N';
					}
				} else {
					$this->data['pesan'] ='Password Tidak Sama, Silahkan Ulangi';
				}
			}
		}		

		$this->load->view('themes/member_ubah_pass_v', $this->data);
	}

	public function ubah_pic() {
		$this->_cek_login();
		$this->data['tersimpan'] = '';
		$this->data['error'] = '';
		if ($this->input->post('submit')) {
			$ubah_pic = $this->member->ubah_pic();
			if($ubah_pic['success'] == 'OK') {
				$this->data['tersimpan'] = 'Y';
			} else {
				$this->data['tersimpan'] = 'N';
				$this->data['error'] = $ubah_pic['error'];
			}
		}
		$user_id = $this->session->userdata('u_name');
		$this->data['row'] = $this->member->get_data_anggota($user_id);

		$this->load->view('themes/member_ubah_pic_v', $this->data);
	}

	public function logout() {
		$this->member->logout();
		redirect('member');
	}
}