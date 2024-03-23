<?php

/**
 * Wali_murid Page Controller
 * @category  Controller
 */
class JurusanapiController extends SecureController
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

        $sql = "SELECT * FROM jurusan";

        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];

            $sql = "SELECT * FROM jurusan
    	    WHERE nama LIKE '%$keyword%'
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
