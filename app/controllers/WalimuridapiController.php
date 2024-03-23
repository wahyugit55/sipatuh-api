<?php

/**
 * Wali_murid Page Controller
 * @category  Controller
 */
class WalimuridapiController extends SecureController
{
    function __construct()
    {
        parent::__construct();
        $this->tablename = "wali_murid";
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

        $sql = "SELECT wm.id, wm.nama, s.nama as siswa, k.nama AS kelas, wm.status, wm.nomor_hp
		FROM wali_murid wm
		LEFT JOIN siswa s ON wm.siswa_nis = s.nis
		LEFT JOIN kelas k ON wm.siswa_kelas = k.id
		ORDER BY wm.id DESC";

        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];

            $sql = "SELECT wm.id, wm.nama, s.nama AS siswa, k.nama AS kelas, wm.status, wm.nomor_hp
    		FROM wali_murid wm
    		LEFT JOIN siswa s ON wm.siswa_nis = s.nis
    		LEFT JOIN kelas k ON wm.siswa_kelas = k.id
    		WHERE wm.nama LIKE '%$keyword%' 
    		OR s.nama LIKE '%$keyword%' 
    		OR k.nama LIKE '%$keyword%' 
    		OR wm.status LIKE '%$keyword%' 
    		OR wm.nomor_hp LIKE '%$keyword%' 
    		ORDER BY wm.id DESC";
        }

        $result = $conn->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return render_json($rows);
    }
}
