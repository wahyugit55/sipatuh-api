<?php 

/**
 * SharedController Controller
 * @category  Controller / Model
 */
class SharedController extends BaseController{
	
	/**
     * user_nama_value_exist Model Action
     * @return array
     */
	function user_nama_value_exist($val){
		$db = $this->GetModel();
		$db->where("nama", $val);
		$exist = $db->has("user");
		return $exist;
	}

	/**
     * user_email_value_exist Model Action
     * @return array
     */
	function user_email_value_exist($val){
		$db = $this->GetModel();
		$db->where("email", $val);
		$exist = $db->has("user");
		return $exist;
	}

	/**
     * getcount_siswa Model Action
     * @return Value
     */
	function getcount_siswa(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS total FROM siswa";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_pelanggaran Model Action
     * @return Value
     */
	function getcount_pelanggaran(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS total FROM pelanggaran";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_hariini Model Action
     * @return Value
     */
	function getcount_hariini(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS total FROM pelanggaran WHERE DATE(tanggal) = DATE(CURRENT_DATE())";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
	* doughnutchart_kategoripelanggaran Model Action
	* @return array
	*/
	function doughnutchart_kategoripelanggaran(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT jp.nama AS jenis, COUNT(*) AS total
        FROM pelanggaran p
        LEFT JOIN jenis_pelanggaran jp ON p.jenis_id = jp.id
        GROUP BY jp.nama ORDER BY total DESC";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'total');
		$dataset_labels =  array_column($dataset1, 'jenis');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

	/**
	* piechart_hariini Model Action
	* @return array
	*/
	function piechart_hariini(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT k.nama AS kelas, COUNT(p.id) AS total 
        FROM pelanggaran p
        LEFT JOIN siswa s ON p.nis = s.nis 
        LEFT JOIN kelas k ON s.kelas_id = k.id 
        WHERE DATE(tanggal) = DATE(CURRENT_DATE()) GROUP BY kelas";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'total');
		$dataset_labels =  array_column($dataset1, 'kelas');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

	/**
	* barchart_pelanggaranperkelas Model Action
	* @return array
	*/
	function barchart_pelanggaranperkelas(){
		
		$db = $this->GetModel();
		$chart_data = array(
			"labels"=> array(),
			"datasets"=> array(),
		);
		
		//set query result for dataset 1
		$sqltext = "SELECT k.nama, COUNT(*) as total
        FROM pelanggaran p
        LEFT JOIN siswa s ON p.nis = s.nis
        LEFT JOIN kelas k ON s.kelas_id = k.id GROUP BY(k.nama) ORDER BY total DESC";
		$queryparams = null;
		$dataset1 = $db->rawQuery($sqltext, $queryparams);
		$dataset_data =  array_column($dataset1, 'total');
		$dataset_labels =  array_column($dataset1, 'nama');
		$chart_data["labels"] = array_unique(array_merge($chart_data["labels"], $dataset_labels));
		$chart_data["datasets"][] = $dataset_data;

		return $chart_data;
	}

}
