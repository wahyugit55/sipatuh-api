<?php

/**
 * Siswa Page Controller
 * @category  Controller
 */
class SiswaapiController extends SecureController
{
    function __construct()
    {
        parent::__construct();
        $this->tablename = "siswa";
    }
    /**
     * List page records
     * @param $fieldname (filter record by a field) 
     * @param $fieldvalue (filter field value)
     * @return BaseView
     */

    protected $servername = "localhost";
    protected $username = "root";
    protected $password = "";
    protected $dbname = "sipatuh";

    function index()
    {
        // Create connection
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT s.id, s.nis, s.nama AS nama, 
		s.jenis_kelamin, wm.nomor_hp AS nomor_wali
		FROM siswa s
		LEFT JOIN wali_murid wm ON s.nis = wm.siswa_nis
		LEFT JOIN kelas k ON s.kelas_id = k.id
		ORDER BY id DESC";

        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];

            $sql = "SELECT 
    	    s.id, s.nis, s.nama AS nama, 
    	    s.jenis_kelamin,
    	    k.nama AS kelas, 
    	    s.alamat, wm.nomor_hp AS nomor_wali
    		FROM siswa s
    		LEFT JOIN wali_murid wm ON s.nis = wm.siswa_nis
    		LEFT JOIN kelas k ON s.kelas_id = k.id
    		WHERE s.nis LIKE '%$keyword%' 
    		OR s.nama LIKE '%$keyword%'
    		OR k.nama LIKE '%$keyword%'
    		OR s.alamat LIKE '%$keyword%'
    		OR wm.nomor_hp LIKE '%$keyword%'
    		ORDER BY id DESC";
        }

        $result = $conn->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return render_json($rows);
    }
}
