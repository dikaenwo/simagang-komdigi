<?php 

define("BASE_URL", "http://localhost/simama/");

/**
 * Absen_masuk Page Controller
 * @category  Controller
 */
class Absen_masukController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "absen_masuk";
	}
	
	/**
     * List page records
     * @param $fieldname (filter record by a field) 
     * @param $fieldvalue (filter field value)
     * @return BaseView
     */
	function index($fieldname = null , $fieldvalue = null){
		$request = $this->request;
		$db = $this->GetModel();
		$tablename = $this->tablename;
		$fields = array("id", 
			"nama_mahasiswa", 
			"jam_masuk", 
			"bukti_foto", 
			"jam_pulang", 
			"bukti_foto_pulang");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT);
		
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				absen_masuk.id LIKE ? OR 
				absen_masuk.nama_mahasiswa LIKE ? OR 
				absen_masuk.jam_masuk LIKE ? OR 
				absen_masuk.bukti_foto LIKE ? OR 
				absen_masuk.jam_pulang LIKE ? OR 
				absen_masuk.bukti_foto_pulang LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			$db->where($search_condition, $search_params);
			$this->view->search_template = "absen_masuk/search.php";
		}
		
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("absen_masuk.id", ORDER_TYPE);
		}
		
		$allowed_roles = array ('admin');
		if(!in_array(strtolower(USER_ROLE), $allowed_roles)){
			$db->where("absen_masuk.nama_mahasiswa", get_active_user('name') );
		}
		
		if($fieldname){
			$db->where($fieldname , $fieldvalue);
		}
		
		$tc = $db->withTotalCount();
		$records = $db->get($tablename, $pagination, $fields);
		$records_count = count($records);
		$total_records = intval($tc->totalCount);
		$page_limit = $pagination[1];
		$total_pages = ceil($total_records / $page_limit);
		
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = $records_count;
		$data->total_records = $total_records;
		$data->total_page = $total_pages;
		
		if($db->getLastError()){
			$this->set_page_error();
		}
		
		$page_title = $this->view->page_title = "Absen Masuk";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("absen_masuk/list.php", $data);
	}
	
	/**
     * View record detail 
	 * @param $rec_id (select record by table primary key) 
     * @param $value value (select record by value of field name(rec_id))
     * @return BaseView
     */
	function view($rec_id = null, $value = null){
		$request = $this->request;
		$db = $this->GetModel();
		$rec_id = $this->rec_id = urldecode($rec_id);
		$tablename = $this->tablename;
		$fields = array("id", 
			"nama_mahasiswa", 
			"jam_masuk", 
			"bukti_foto", 
			"jam_pulang", 
			"bukti_foto_pulang");
			
		$allowed_roles = array ('admin');
		if(!in_array(strtolower(USER_ROLE), $allowed_roles)){
			$db->where("absen_masuk.nama_mahasiswa", get_active_user('name') );
		}
		
		if($value){
			$db->where($rec_id, urldecode($value));
		}
		else{
			$db->where("absen_masuk.id", $rec_id);
		}
		
		$record = $db->getOne($tablename, $fields );
		
		if($record){
			$page_title = $this->view->page_title = "View  Absen Masuk";
			$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
			$this->view->report_title = $page_title;
			$this->view->report_layout = "report_layout.php";
			$this->view->report_paper_size = "A4";
			$this->view->report_orientation = "portrait";
		}
		else{
			if($db->getLastError()){
				$this->set_page_error();
			}
			else{
				$this->set_page_error("No record found");
			}
		}
		return $this->render_view("absen_masuk/view.php", $record);
	}
	
	/**
     * Insert new record to the database table
	 * @param $formdata array() from $_POST
     * @return BaseView
     */
	function add($formdata = null){
		if($formdata){
			$db = $this->GetModel();
			$tablename = $this->tablename;
			$request = $this->request;
			
			// Validasi base64 image
			if(!empty($formdata['bukti_foto']) && strpos($formdata['bukti_foto'], 'data:image') === 0){
				// Extract base64 data
				$image_data = $formdata['bukti_foto'];
				$image_parts = explode(";base64,", $image_data);
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type = $image_type_aux[1];
				$image_base64 = base64_decode($image_parts[1]);
				
				// Generate unique filename
				$upload_dir = 'uploads/files/';
				if(!is_dir($upload_dir)){
					mkdir($upload_dir, 0755, true);
				}
				
				$filename = 'absen_' . time() . '_' . uniqid() . '.' . $image_type;
				$file_path = $upload_dir . $filename;
				
				// Save file
				if(file_put_contents($file_path, $image_base64)){
					$formdata['bukti_foto'] = BASE_URL . "uploads/files/" . $filename;
				}
				else{
					$this->set_page_error("Gagal menyimpan foto");
					return $this->render_view("absen_masuk/add.php");
				}
			}
			else{
				$this->set_page_error("Format foto tidak valid atau foto tidak diambil");
				return $this->render_view("absen_masuk/add.php");
			}
			
			// Fillable fields
			$fields = $this->fields = array("nama_mahasiswa","bukti_foto");
			$postdata = $this->format_request_data($formdata);
			
			$this->rules_array = array(
				'nama_mahasiswa' => 'required',
				'bukti_foto' => 'required',
			);
			
			$this->sanitize_array = array(
				'nama_mahasiswa' => 'sanitize_string',
				'bukti_foto' => 'sanitize_string',
			);
			
			$this->filter_vals = true;
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			
			if($this->validated()){
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if($rec_id){
					$this->set_flash_msg("Absen berhasil dicatat", "success");
					return $this->redirect("absen_masuk");
				}
				else{
					$this->set_page_error();
				}
			}
		}
		
		$page_title = $this->view->page_title = "Add New Absen Masuk";
		$this->render_view("absen_masuk/add.php");
	}
	
	/**
     * Update table record with formdata (untuk absen pulang)
	 * @param $rec_id (select record by table primary key)
	 * @param $formdata array() from $_POST
     * @return array
     */
	function edit($rec_id = null, $formdata = null){
		$request = $this->request;
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename;
		$fields = $this->fields = array("id","nama_mahasiswa","jam_pulang","bukti_foto_pulang");
		
		if($formdata){
			// Handle base64 image untuk foto pulang
			if(!empty($formdata['bukti_foto_pulang']) && strpos($formdata['bukti_foto_pulang'], 'data:image') === 0){
				$image_data = $formdata['bukti_foto_pulang'];
				$image_parts = explode(";base64,", $image_data);
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type = $image_type_aux[1];
				$image_base64 = base64_decode($image_parts[1]);
				
				$upload_dir = 'uploads/files/';
				if(!is_dir($upload_dir)){
					mkdir($upload_dir, 0755, true);
				}
				
				$filename = 'absen_pulang_' . time() . '_' . uniqid() . '.' . $image_type;
				$file_path = $upload_dir . $filename;
				
				if(file_put_contents($file_path, $image_base64)){
					$formdata['bukti_foto_pulang'] = BASE_URL . "uploads/files/" . $filename;
				}
				else{
					$this->set_page_error("Gagal menyimpan foto pulang");
					$allowed_roles = array ('admin');
					if(!in_array(strtolower(USER_ROLE), $allowed_roles)){
						$db->where("absen_masuk.nama_mahasiswa", get_active_user('name') );
					}
					$db->where("absen_masuk.id", $rec_id);
					$data = $db->getOne($tablename, $fields);
					return $this->render_view("absen_masuk/edit.php", $data);
				}
			}
			
			$postdata = $this->format_request_data($formdata);
			
			$this->rules_array = array(
				'nama_mahasiswa' => 'required',
				'jam_pulang' => 'required',
				'bukti_foto_pulang' => 'required',
			);
			
			$this->sanitize_array = array(
				'nama_mahasiswa' => 'sanitize_string',
				'jam_pulang' => 'sanitize_string',
				'bukti_foto_pulang' => 'sanitize_string',
			);
			
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			
			if($this->validated()){
				$allowed_roles = array ('admin');
				if(!in_array(strtolower(USER_ROLE), $allowed_roles)){
					$db->where("absen_masuk.nama_mahasiswa", get_active_user('name') );
				}
				$db->where("absen_masuk.id", $rec_id);
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount();
				
				if($bool && $numRows){
					$this->set_flash_msg("Absen pulang berhasil dicatat", "success");
					return $this->redirect("absen_masuk");
				}
				else{
					if($db->getLastError()){
						$this->set_page_error();
					}
					elseif(!$numRows){
						$page_error = "No record updated";
						$this->set_page_error($page_error);
						$this->set_flash_msg($page_error, "warning");
						return $this->redirect("absen_masuk");
					}
				}
			}
		}
		
		$allowed_roles = array ('admin');
		if(!in_array(strtolower(USER_ROLE), $allowed_roles)){
			$db->where("absen_masuk.nama_mahasiswa", get_active_user('name') );
		}
		$db->where("absen_masuk.id", $rec_id);
		$data = $db->getOne($tablename, $fields);
		
		$page_title = $this->view->page_title = "Edit  Absen Masuk";
		if(!$data){
			$this->set_page_error();
		}
		return $this->render_view("absen_masuk/edit.php", $data);
	}
	
	/**
     * Delete record from the database
	 * Support multi delete by separating record id by comma.
     * @return BaseView
     */
	function delete($rec_id = null){
		Csrf::cross_check();
		$request = $this->request;
		$db = $this->GetModel();
		$tablename = $this->tablename;
		$this->rec_id = $rec_id;
		
		$arr_rec_id = array_map('trim', explode(",", $rec_id));
		$db->where("absen_masuk.id", $arr_rec_id, "in");
		
		$allowed_roles = array ('admin');
		if(!in_array(strtolower(USER_ROLE), $allowed_roles)){
			$db->where("absen_masuk.nama_mahasiswa", get_active_user('name') );
		}
		
		$bool = $db->delete($tablename);
		
		if($bool){
			$this->set_flash_msg("Record deleted successfully", "success");
		}
		elseif($db->getLastError()){
			$page_error = $db->getLastError();
			$this->set_flash_msg($page_error, "danger");
		}
		return $this->redirect("absen_masuk");
	}
}