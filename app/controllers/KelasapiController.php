<?php

/**
 * Kelas Page Controller
 * @category  Controller
 */
class KelasapiController extends SecureController
{
    function __construct()
    {
        parent::__construct();
        $this->tablename = "kelas";
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

        $sql = "SELECT k.id, k.nama AS kelas, j.nama as jurusan 
		FROM kelas k
		LEFT JOIN jurusan j ON k.jurusan_id = j.id
		ORDER BY k.id DESC";

        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];

            $sql = "SELECT k.id, k.nama AS kelas, j.nama as jurusan 
    		FROM kelas k
    		LEFT JOIN jurusan j ON k.jurusan_id = j.id
    		WHERE k.nama LIKE '%$keyword%' OR j.nama LIKE '%$keyword%'
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
