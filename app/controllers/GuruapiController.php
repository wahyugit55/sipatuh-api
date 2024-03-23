<?php

/**
 * Guru Page Controller
 * @category  Controller
 */
class GuruapiController extends SecureController
{
    function __construct()
    {
        parent::__construct();
        $this->tablename = "guru";
    }
    /**
     * List page recordsd
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

        $sql = "SELECT g.id, g.nip, g.nama, g.jabatan, k.nama as wali_kelas, g.nomor_hp 
		FROM guru g LEFT JOIN kelas k ON g.kelas_id = k.id
		ORDER BY g.id DESC";

        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];

            $sql = "SELECT g.id, g.nip, g.nama, g.jabatan, k.nama as wali_kelas, g.nomor_hp 
    		FROM guru g 
    		LEFT JOIN kelas k ON g.kelas_id = k.id
    		WHERE g.nip LIKE '%$keyword%'
    		OR g.nama LIKE '%$keyword%' OR g.jabatan LIKE '%$keyword%' OR
    		k.nama LIKE '%$keyword%' OR g.nomor_hp LIKE '%$keyword%'
    		ORDER BY k.id DESC";
        }

        $result = $conn->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return render_json($rows);
    }
}
