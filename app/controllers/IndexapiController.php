<?php

/**
 * Index Page Controller
 * @category  Controller
 */
class IndexapiController extends BaseController
{
	function __construct()
	{
		parent::__construct();
		$this->tablename = "user";
	}
	/**
	 * Index Action 
	 * @return null
	 */

	protected $servername = "localhost";
	protected $username = "root";
	protected $password = "";
	protected $dbname = "sipatuh";

	function index()
	{
		if (user_login_status() == true) {
			$this->redirect(HOME_PAGE);
		} else {
			$this->render_view("index/index.php");
		}
	}
	private function login_user($username, $password_text, $rememberme = false)
	{
		$db = $this->GetModel();
		$username = filter_var($username, FILTER_SANITIZE_STRING);
		$db->where("nama", $username)->orWhere("email", $username);
		$tablename = $this->tablename;
		$user = $db->getOne($tablename);
		if (!empty($user)) {
			//Verify User Password Text With DB Password Hash Value.
			//Uses PHP password_verify() function with default options
			$password_hash = $user['password'];
			$this->modeldata['password'] = $password_hash; //update the modeldata with the password hash
			if (password_verify($password_text, $password_hash)) {
				unset($user['password']); //Remove user password. No need to store it in the session
				set_session("user_data", $user); // Set active user data in a sessions
				//if Remeber Me, Set Cookie
				if ($rememberme == true) {
					$sessionkey = time() . random_str(20); // Generate a session key for the user
					//Update user session info in database with the session key
					$db->where("id", $user['id']);
					$res = $db->update($tablename, array("login_session_key" => hash_value($sessionkey)));
					if (!empty($res)) {
						set_cookie("login_session_key", $sessionkey); // save user login_session_key in a Cookie
					}
				} else {
					clear_cookie("login_session_key"); // Clear any previous set cookie
				}
				$redirect_url = get_session("login_redirect_url"); // Redirect to user active page
				if (!empty($redirect_url)) {
					clear_session("login_redirect_url");
					return $this->redirect($redirect_url);
				} else {
					return $this->redirect(HOME_PAGE);
				}
			} else {
				//password is not correct
				return $this->login_fail("Username or password not correct");
			}
		} else {
			//user is not registered
			return $this->login_fail("Username or password not correct");
		}
	}
	/**
	 * Display login page with custom message when login fails
	 * @return BaseView
	 */
	private function login_fail($page_error = null)
	{
		$this->set_page_error($page_error);
		$this->render_view("index/login.php");
	}
	/**
	 * Login Action
	 * If Not $_POST Request, Display Login Form View
	 * @return View
	 */
	function login($formdata = null)
	{
		if ($formdata) {
			$modeldata = $this->modeldata = $formdata;
			$username = trim($modeldata['username']);
			$password = $modeldata['password'];
			$rememberme = (!empty($modeldata['rememberme']) ? $modeldata['rememberme'] : false);
			$this->login_user($username, $password, $rememberme);
		} else {
			$this->set_page_error("Invalid request");
			$this->render_view("index/login.php");
		}
	}
	/**
	 * Insert new record into the user table
	 * @param $formdata array from $_POST
	 * @return BaseView
	 */
	function register($formdata = null)
	{
		if ($formdata) {
			$request = $this->request;
			$db = $this->GetModel();
			$tablename = $this->tablename;
			$fields = $this->fields = array("nama", "email", "password", "role"); //registration fields
			$postdata = $this->format_request_data($formdata);
			$cpassword = $postdata['confirm_password'];
			$password = $postdata['password'];
			if ($cpassword != $password) {
				$this->view->page_error[] = "Your password confirmation is not consistent";
			}
			$this->rules_array = array(
				'nama' => 'required',
				'email' => 'required|valid_email',
				'password' => 'required',
				'role' => 'required|numeric',
			);
			$this->sanitize_array = array(
				'nama' => 'sanitize_string',
				'email' => 'sanitize_string',
				'role' => 'sanitize_string',
			);
			$this->filter_vals = true; //set whether to remove empty fields
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$password_text = $modeldata['password'];
			//update modeldata with the password hash
			$modeldata['password'] = $this->modeldata['password'] = password_hash($password_text, PASSWORD_DEFAULT);
			//Check if Duplicate Record Already Exit In The Database
			$db->where("nama", $modeldata['nama']);
			if ($db->has($tablename)) {
				$this->view->page_error[] = $modeldata['nama'] . " Already exist!";
			}
			//Check if Duplicate Record Already Exit In The Database
			$db->where("email", $modeldata['email']);
			if ($db->has($tablename)) {
				$this->view->page_error[] = $modeldata['email'] . " Already exist!";
			}
			if ($this->validated()) {
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if ($rec_id) {
					redirect_to_page('index');
					return;
				} else {
					$this->set_page_error();
				}
			}
		}
		$page_title = $this->view->page_title = "Add New User";
		return $this->render_view("index/register.php");
	}
	/**
	 * Logout Action
	 * Destroy All Sessions And Cookies
	 * @return View
	 */
	function logout($arg = null)
	{
		Csrf::cross_check();
		session_destroy();
		clear_cookie("login_session_key");
		$this->redirect("");
	}

	function dashboard()
	{
		// Create connection
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$total_siswa = "SELECT COUNT(*) AS total FROM siswa";
		$result = $conn->query($total_siswa);
		$total_siswa = [];
		while ($row = $result->fetch_assoc()) {
			$total_siswa[] = $row;
		}

		$total_siswa = $total_siswa[0]['total'];

		$total_pelanggaran = "SELECT COUNT(*) AS total FROM pelanggaran";
		$result = $conn->query($total_pelanggaran);
		$total_pelanggaran = [];
		while ($row = $result->fetch_assoc()) {
			$total_pelanggaran[] = $row;
		}

		$total_pelanggaran = $total_pelanggaran[0]['total'];

		$hari_ini = "SELECT COUNT(*) AS total FROM pelanggaran WHERE DATE(tanggal) = DATE(CURRENT_DATE())";
		$result = $conn->query($hari_ini);
		$hari_ini = [];
		while ($row = $result->fetch_assoc()) {
			$hari_ini[] = $row;
		}

		$hari_ini = $hari_ini[0]['total'];

		return render_json(["total_siswa" => $total_siswa, "total_pelanggaran" => $total_pelanggaran, "hari_ini" => $hari_ini]);
	}

	function kategoripelanggaran()
	{
		// Create connection
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT jp.nama AS jenis, COUNT(*) AS total
		FROM pelanggaran p
		LEFT JOIN jenis_pelanggaran jp ON p.jenis_id = jp.id
		GROUP BY jp.nama ORDER BY total DESC";

		$result = $conn->query($sql);
		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$row['total'] = (int) $row['total'];
			$rows[] = $row;
		}

		return render_json($rows);
	}

	function pelanggaranbyday()
	{
		// Create connection
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT k.nama AS kelas, COUNT(p.id) AS total 
        FROM pelanggaran p
        LEFT JOIN siswa s ON p.nis = s.nis 
        LEFT JOIN kelas k ON s.kelas_id = k.id 
        WHERE DATE(tanggal) = DATE(CURRENT_DATE()) GROUP BY kelas";
		$result = $conn->query($sql);


		$results = [];
		while ($row = $result->fetch_assoc()) {
			$row['total'] = (int) $row['total'];
			$results[] = $row;
		}

		// 		$sql = "SELECT COUNT(p.id) AS total
		//         FROM pelanggaran p
		//         WHERE DATE(tanggal) = DATE(CURRENT_DATE())";
		// 		$result2 = $conn->query($sql);

		// 		$results2 = $result2->fetch_assoc();

		return render_json($results);
	}

	function pelanggaranperkelas()
	{
		// Create connection
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT k.nama, COUNT(*) as total
		FROM pelanggaran p
		LEFT JOIN siswa s ON p.nis = s.nis
		LEFT JOIN kelas k ON s.kelas_id = k.id GROUP BY(k.nama) ORDER BY total DESC";
		$result = $conn->query($sql);

		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$row['total'] = (int) $row['total'];
			$rows[] = $row;
		}

		return render_json($rows);
	}
}
