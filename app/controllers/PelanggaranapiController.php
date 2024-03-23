<?php

/**
 * Pelanggaran Page Controller
 * @category  Controller
 */
class PelanggaranapiController extends SecureController
{
    function __construct()
    {
        parent::__construct();
        $this->tablename = "pelanggaran";
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

        $sql = 'SELECT p.id, s.nis, s.nama, k.nama as kelas, DATE_FORMAT(p.tanggal, "%Y-%m-%d %H:%i") AS tanggal, 
		jp.nama AS jenis, p.detail, wm.nomor_hp
		FROM pelanggaran p
		LEFT JOIN siswa s ON p.nis = s.nis
		LEFT JOIN kelas k ON s.kelas_id = k.id
		LEFT JOIN jenis_pelanggaran jp ON p.jenis_id = jp.id
		LEFT JOIN wali_murid wm ON p.nis = wm.siswa_nis
		ORDER BY p.id DESC';

        if (isset($_GET['search'])) {
            $keyword = $_GET['search'];

            $sql = "SELECT p.id, s.nis, s.nama, k.nama as kelas, DATE_FORMAT(p.tanggal, '%Y-%m-%d %H:%i') AS tanggal, 
		    jp.nama AS jenis, p.detail, wm.nomor_hp
    		FROM pelanggaran p
    		LEFT JOIN siswa s ON p.nis = s.nis
    		LEFT JOIN kelas k ON s.kelas_id = k.id
    		LEFT JOIN jenis_pelanggaran jp ON p.jenis_id = jp.id
    		LEFT JOIN wali_murid wm ON p.nis = wm.siswa_nis
    		WHERE s.nama LIKE '%$keyword%' 
    		OR s.nis LIKE '%$keyword%' 
    		OR k.nama LIKE '%$keyword%' 
    		OR p.tanggal LIKE '%$keyword%' 
    		OR jp.nama LIKE '%$keyword%'
    		OR p.detail LIKE '%$keyword%' 
    		OR wm.nomor_hp LIKE '%$keyword%'
    		ORDER BY p.id DESC";
        }

        $result = $conn->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return render_json($rows);
    }

    function add($formdata = null)
    {
        // Create connection
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $nis = $formdata["nis"];
        $nama = $formdata["nama"];
        $kelas = $formdata["kelas"];
        $tanggal = $formdata["tanggal"];
        $jenis_id = $formdata["jenis_id"];
        $detail = $formdata["detail"];

        if ($formdata == null || empty($formdata)) {
            return render_json([
                "message" => "Isi data terlebih dahulu!"
            ]);
        }

        $message = ["message" => []];

        if (empty($nis) || empty($nama) || empty($kelas) || empty($tanggal) || empty($jenis_id)) {

            if (empty($nis)) {
                $message["message"]['nis'] = 'nis harus diisi';
            }

            if (empty($nama)) {
                $message["message"]['nama'] = 'nama harus diisi';
            }

            if (empty($kelas)) {
                $message["message"]['kelas'] = 'kelas harus diisi';
            }

            if (empty($tanggal)) {
                $message["message"]['tanggal'] = 'tanggal harus diisi';
            }

            if (empty($jenis_id)) {
                $message["message"]['jenis_id'] = 'jenis_id harus diisi';
            }


            return render_json($message);
        }

        $sql = "SELECT * FROM pelanggaran WHERE nis = '$nis' && tanggal = '$tanggal' && jenis_id = '$jenis_id'";
        $pelanggaran_check = $conn->query($sql);
        $pelanggaran_check = $pelanggaran_check->fetch_assoc();

        if ($pelanggaran_check) {

            return render_json(
                [
                    "message" => "pelanggaran sudah tercatat"
                ]
            );
        }

        // 		$sql = "INSERT INTO pelanggaran(siswa_nis, tanggal, jenis_id, detail, nomor_wali) VALUES ('$siswa_nis', '$tanggal', '$jenis_id', '$detail', '$nomor_wali')";
        $sql = "INSERT INTO pelanggaran(nis, nama, kelas, tanggal, jenis_id, detail) VALUES ('$nis', '$nama', '$kelas', '$tanggal', '$jenis_id', '$detail')";
        $result = $conn->execute_query($sql);

        return render_json(["message" => "Data berhasil ditambahkan", $formdata]);
    }
}
