<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengajuan extends OPPController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('general_m');
	}	

	public function index() {
		$this->load->model('pinjaman_m');
		$this->data['judul_browser'] = 'Pengajuan Pinjaman';
		$this->data['judul_utama'] = 'PENGAJUAN';
		$this->data['judul_sub'] = 'Pinjaman';

		//table
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.css';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table-id-ID.js';

		//modal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-modal/css/bootstrap-modal-bs3patch.css';
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-modal/css/bootstrap-modal.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/bootstrap-modalmanager.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/bootstrap-modal.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/nsi_modal_default.js';

		// datepicker
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/datepicker/datepicker3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/datepicker/bootstrap-datepicker.js';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/datepicker/locales/bootstrap-datepicker.id.js';
		//$this->data['barang_id'] = $this->pinjaman_m->get_id_barang();

		//daterange
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		//select2
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		//editable
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap3-editable/css/bootstrap-editable.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap3-editable/js/bootstrap-editable.min.js';

		//easyui
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/default/easyui.css';
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/icon.css';
		$this->data['js_files'][] = base_url() . 'assets/easyui/jquery.easyui.min.js';

		$this->data['jenis_ags'] = $this->pinjaman_m->get_data_angsuran();

		$this->data['isi'] = $this->load->view('pengajuan_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	function export_excel() {
		$this->load->model('pinjaman_m');
		$this->load->model('setting_m');
		$data_ajuan = $this->pinjaman_m->get_pengajuan_cetak();
		$opsi_val_arr = $this->setting_m->get_key_val();
		foreach ($opsi_val_arr as $key => $value) {
			$out[$key] = $value;
		}

		//var_dump($data_ajuan);
		//exit();
		if($data_ajuan['total'] == 0) {
			echo 'Data Kosong';
			exit();
		}
		$list = $data_ajuan['rows'];

		$fr_jenis = isset($_REQUEST['fr_jenis']) ? explode(',', $_REQUEST['fr_jenis']) : array();
		$fr_status = isset($_REQUEST['fr_status']) ? explode(',', $_REQUEST['fr_status']) : array();		
		
		$fr_jenis = array_diff($fr_jenis, array(NULL)); // NULL / FALSE / ''
		$fr_status = array_diff($fr_status, array(NULL)); // NULL / FALSE / ''

		$fr_bulan = isset($_REQUEST['fr_bulan']) ? $_REQUEST['fr_bulan'] : '';
		
		if($fr_bulan != '') {
			$bln_dari = date("Y-m-d", strtotime($fr_bulan . "-01 -1 month"));
			$tgl_dari = substr($bln_dari, 0, 7) . '-18';
			$tgl_sampai = $fr_bulan . '-17';
		} else {
			$tgl_dari = $_REQUEST['tgl_dari']; 
			$tgl_sampai = $_REQUEST['tgl_sampai'];
		}	


		//$fr_jenis = explode(',', $fr_jenis);
		//$fr_status = explode(',', $fr_status);

		if(! empty($fr_jenis)) {
			$txt_jenis = implode(', ', $fr_jenis);
		} else {
			$txt_jenis = "Semua";
		}
		$status_arr = array(0 => 'Menunggu Konfirmasi', 1 => 'Disetujui', 2 => 'Ditolak', 3 => 'Sudah Terlaksana', 4 => 'Batal');
		if(! empty($fr_status)) {
			$status_rep = str_replace(
				array('0', '1', '2', '3', '4'), 
				array('Menunggu Konfirmasi', 'Disetujui', 'Ditolak', 'Sudah Terlaksana', 'Batal'), 
				$fr_status);
			$txt_status = implode(', ', $status_rep);
			//echo $txt_status; exit();
		} else {
			$txt_status = "Semua";
		}

		$this->load->library('excel');
		// variable
		$align_left = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		$align_center = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
		$align_right = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
		
		// Set Properties
		$app_info = 'NSI';
		$this->excel->getProperties()->setCreator($app_info)
									 ->setLastModifiedBy($app_info)
									 ->setTitle($app_info)
									 ->setSubject($app_info)
									 ->setDescription($app_info)
									 ->setKeywords($app_info)
									 ->setCategory($app_info);
									 
		$sheet = $this->excel->getActiveSheet();
									 
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$sheet->setTitle('Pengajuan');

		// A4
		$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToPage(true);
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageSetup()->setFitToHeight(0);
		
		$pageMargins = $sheet->getPageMargins();

		// margin is set in inches (0.5cm) = 0.5 / 2.54
		$margin = 0.5 / 2.54;

		$pageMargins->setTop($margin);
		$pageMargins->setBottom($margin);
		$pageMargins->setLeft($margin);
		$pageMargins->setRight($margin);
		
		// $worksheet->getStyle('A1')->getAlignment()->setShrinkToFit(true);
		$sheet->getDefaultStyle()->getFont()->setName('Segoe UI');
		//$sheet->getDefaultStyle()->getFont()->setSize(11);
		$sheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$currencyFormat = '_ Rp * #,##0_ ;_ Rp * -#,##0_ ;_ Rp * "-"_ ;_ @_ ';

		//
		$judul = 'Laporan Data Pengajuan Periode '.jin_date_ina($tgl_dari).' - '.jin_date_ina($tgl_sampai).' | Jenis: '.$txt_jenis.' | Status: '.$txt_status;
		$sheet->setCellValueExplicit('A1', $judul, PHPExcel_Cell_DataType::TYPE_STRING);
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal($align_center);
		$sheet->mergeCells('A1:L1');
		//$headings = array('No', 'Tanggal', 'Keterangan', 'M/K', 'Nominal', 'Saldo');
		$headings = array('No', 'ID Ajuan', 'NIK', 'Nama', 'Dept', 'Tanggal', 'Nominal', 'Pelunasan', 'Bln', 'Rp/Bln', 'Jenis Bayar', 'Status');
		$sheet->getStyle('A2:L2')->applyFromArray(
			array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'DDDDDD')
				)
			)
		);
		$sheet->setAutoFilter('A2:L2');
		$rowNumber = 2;
		$col = 'A';
		foreach($headings as $heading) {
			if($heading == 'No') {
				$sheet->getStyle($col)->getAlignment()->setHorizontal($align_center);
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getColumnDimension($col)->setWidth(3);
			}
			if($heading == 'ID Ajuan') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getColumnDimension($col)->setWidth(10);
			}
			if($heading == 'NIK') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getStyle($col)->getAlignment()->setHorizontal($align_center);
				$sheet->getColumnDimension($col)->setWidth(10);
			}
			if($heading == 'Nama') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_left);
				$sheet->getColumnDimension($col)->setWidth(30);
			}
			if($heading == 'Dept') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_left);
				$sheet->getColumnDimension($col)->setWidth(20);
			}
			if($heading == 'Tanggal') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getStyle($col)->getAlignment()->setHorizontal($align_center);
				$sheet->getColumnDimension($col)->setWidth(15);
			}
			if($heading == 'Nominal' || $heading == 'Pelunasan' || $heading == 'Rp/Bln') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getColumnDimension($col)->setWidth(15);
				$sheet->getStyle($col)->getNumberFormat()->setFormatCode($currencyFormat);
			}
			if($heading == 'Bln') {
				$sheet->getStyle($col)->getAlignment()->setHorizontal($align_center);
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getColumnDimension($col)->setWidth(5);
			}
			if($heading == 'Jenis Bayar') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_left);
				$sheet->getColumnDimension($col)->setWidth(30);
			}
			if($heading == 'Status') {
				$sheet->getStyle($col.$rowNumber)->getAlignment()->setHorizontal($align_center);
				$sheet->getStyle($col)->getAlignment()->setHorizontal($align_left);
				$sheet->getColumnDimension($col)->setWidth(20);
			}
			$sheet->setCellValueExplicit($col.$rowNumber, $heading, PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->getStyle($col.$rowNumber)->getFont()->setBold(true);
			$col++;
		}
		$sheet->getPageSetup()->setRowsToRepeatAtTop(array(0,1));

		// HASIL
		$rowNumber = 3;
		$no = 1;
		foreach ($list as $row) {
			$row->nominal = str_replace(',', '', $row->nominal);
			$row->sisa_tagihan = str_replace(',', '', $row->sisa_tagihan);
			$row->lama_ags = str_replace(',', '', $row->lama_ags);
			$row->num_ags = str_replace(',', '', $row->num_ags);
			$col = 'A';
			$sheet->setCellValue($col.$rowNumber, $no); $col++;

			$sheet->setCellValueExplicit($col.$rowNumber, ($row->ajuan_id), PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$sheet->setCellValueExplicit($col.$rowNumber, ($row->identitas), PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$sheet->setCellValueExplicit($col.$rowNumber, ($row->nama), PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$sheet->setCellValueExplicit($col.$rowNumber, ($row->departement), PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$sheet->setCellValueExplicit($col.$rowNumber, ($row->tgl_input_txt), PHPExcel_Cell_DataType::TYPE_STRING);$col++;
			$sheet->setCellValue($col.$rowNumber, $row->nominal); $col++;
			$sheet->setCellValue($col.$rowNumber, $row->sisa_tagihan); $col++;
			$sheet->setCellValue($col.$rowNumber, $row->lama_ags); $col++;
			$sheet->setCellValue($col.$rowNumber, $row->num_ags); $col++;
			$sheet->setCellValue($col.$rowNumber, $row->jenis_bayar_txt); $col++;
			$sheet->setCellValueExplicit($col.$rowNumber, ($status_arr[$row->status]), PHPExcel_Cell_DataType::TYPE_STRING);$col++;

			//$sheet->getStyle($col.$rowNumber)->getAlignment()->setWrapText(true);
			//$sheet->setCellValueExplicit($col.$rowNumber, trim($val['ket']), PHPExcel_Cell_DataType::TYPE_STRING);$col++;

			//$sheet->setCellValueExplicit($col.$rowNumber, trim($row['mas_kel']), PHPExcel_Cell_DataType::TYPE_STRING);$col++;

			$rowNumber++;
			$no++;					
		}
		// border
		$styleArray = array(
						   'borders' => array(
								 'allborders' => array(
										'style' => PHPExcel_Style_Border::BORDER_THIN,
										'color' => array('rgb' => '000000'),
								 ),
						   ),
					);
		$sheet->getStyle('A2:L'.($rowNumber - 1).'')->applyFromArray($styleArray);
		$sheet->freezePane('A3');

		$filename = 'Pengajuan_'.date('Ymd_His').'.xls';
		//$filename = str_replace(' ', '_', $filename);
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
					 
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	public function ajax_pengajuan() {
		$this->load->model('pinjaman_m');
		$out = $this->pinjaman_m->get_pengajuan();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}

	function aksi() {
		$this->load->model('pinjaman_m');
		if($this->pinjaman_m->pengajuan_aksi()) {
			echo 'OK';
		} else {
			echo 'Gagal';
		}
	}

	function edit() {
		$this->load->model('pinjaman_m');
		$res = $this->pinjaman_m->pengajuan_edit();
		echo $res;
	}

}
