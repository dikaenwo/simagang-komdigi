<?php 

/**
 * SharedController Controller
 * @category  Controller / Model
 */
class SharedController extends BaseController{
	
	/**
     * users_name_value_exist Model Action
     * @return array
     */
	function users_name_value_exist($val){
		$db = $this->GetModel();
		$db->where("name", $val);
		$exist = $db->has("users");
		return $exist;
	}

	/**
     * users_email_value_exist Model Action
     * @return array
     */
	function users_email_value_exist($val){
		$db = $this->GetModel();
		$db->where("email", $val);
		$exist = $db->has("users");
		return $exist;
	}

	/**
     * getcount_jumlahanakmagang Model Action
     * @return Value
     */
	function getcount_jumlahanakmagang(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM users";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_laporanterkumpul Model Action
     * @return Value
     */
	function getcount_laporanterkumpul(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM laporan";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_absenhariini Model Action
     * @return Value
     */
	function getcount_absenhariini(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num 
FROM absen_masuk
WHERE DATE(jam_masuk) = CURDATE();
";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
	* piechart_berdasarkanjeniskelamin Model Action
	* @return array
	*/
	function piechart_berdasarkanjeniskelamin(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT  COUNT(u.id) AS count_of_id, u.jenis_kelamin FROM users AS u GROUP BY u.jenis_kelamin";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'count_of_id');
		$dataset_labels =  array_column($dataset1, 'jenis_kelamin');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

	/**
	* piechart_jenjangpendidikan Model Action
	* @return array
	*/
	function piechart_jenjangpendidikan(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT  COUNT(u.id) AS count_of_id, u.jenjang_pendidikan FROM users AS u GROUP BY u.jenjang_pendidikan";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'count_of_id');
		$dataset_labels =  array_column($dataset1, 'jenjang_pendidikan');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

	/**
	* piechart_status Model Action
	* @return array
	*/
	function piechart_status(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT  COUNT(u.id) AS count_of_id, u.status FROM users AS u GROUP BY u.status";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'count_of_id');
		$dataset_labels =  array_column($dataset1, 'status');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

	/**
	* barchart_institusi Model Action
	* @return array
	*/
	function barchart_institusi(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT  COUNT(u.id) AS count_of_id, u.institutisi FROM users AS u GROUP BY u.institutisi";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'count_of_id');
		$dataset_labels =  array_column($dataset1, 'institutisi');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

}
