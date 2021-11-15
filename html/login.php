<?php
class Login {
	protected $HOST_DB;
	protected $USER_NAME_DB;
	protected $PASS_DB;
	protected $NAME_DB;

	function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB)
	{
		$this->HOST_DB = $HOST_DB; 
		$this->USER_NAME_DB = $USER_NAME_DB; 
		$this->PASS_DB = $PASS_DB;
		$this->NAME_DB = $NAME_DB;
	}
	
	public function renderingFormLogin($data=false, $err=false)
	{
		if($err)
			echo '<div class="mx-auto col-12 my-2 text-center alert alert-warning">
				'.$err.'
			</div>';

		if(!$data) $data = array('inputLogin' => '', 'inputPassword' => '');

		printf('
			<div class="col-12 border my-2">
				<form action="./index.php" method="POST">
					<div class="form-group">
						<label for="inputLogin">Login</label>
						<input type="login" class="form-control" name="inputLogin" value="%s">
					</div>
					<div class="form-group">
						<label for="inputPassword">Password</label>
						<input type="password" class="form-control" name="inputPassword" value="%s">
					</div>
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		', $data['inputLogin'], $data['inputPassword']);
	
	}

	public function prepareLogin($data)
	{
		$inputLogin = trim($data['inputLogin']);
		if(empty($inputLogin))
		{
			$err = 'Поле Login пустое!';
			$this->renderingFormLogin($data, $err);
			exit();
		}

		$inputPassword = trim($data['inputPassword']);
		if(empty($inputPassword))
		{
			$err = 'Поле Пароль пустое!';
			$this->renderingFormLogin($data, $err);
			exit();
		}
	
		$prepareArr = [
			'login' => $inputLogin,
			'password' => md5($inputPassword)
		]; 
		
		if($result = $this->checkLogin($prepareArr))
		{
			$_SESSION["hash"] = $result;
			header('Location: ./');
			exit;
		}
		else
		{
			$err = 'Ошибка авторизации!';
			$this->renderingFormLogin($data, $err);
		}

	}

	private function checkLogin($data)
	{
		$mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
		if(mysqli_connect_errno()) return mysqli_connect_error();
	
		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			$query_select = "SELECT users.id FROM users WHERE users.login = ? AND users.password = ? LIMIT 0, 1";
			$stmt->prepare($query_select);
			$stmt->bind_param('ss', $data['login'], $data['password']);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			$stmt->close();
		}

		if($id)
		{
			$date = date("Y-m-d");
			$hash = md5(time().$id);
			$user_id = $id;

			$stmt = $mysqli->stmt_init();
			if($stmt)
			{
				$query_insert = "INSERT INTO session_login VALUES (NULL, ?, ?, ?)";
				$stmt->prepare($query_insert);
				$stmt->bind_param('sss', $user_id, $hash, $date);
				$stmt->execute();
				$res = $stmt->affected_rows;
				$stmt->close();
			}
			$mysqli->close();
		}

		return !empty($res) ? $hash : false;
	}

	public function checkSession($hash)
	{
		if(!preg_match("/^[\d\w]+$/i", $hash)) return;

		$mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
		if(mysqli_connect_errno()) return mysqli_connect_error();

		$date = date("Y-m-d");

		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			$query_select = "SELECT session_login.id FROM session_login WHERE session_login.date = ? AND session_login.hash = ? LIMIT 0, 1";
			$stmt->prepare($query_select);
			$stmt->bind_param('ss', $date, $hash);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			$stmt->close();
		}

		return !empty($id) ? $id : $this->logout();;
	}

	public function logout()
	{
		unset($_SESSION["hash"]);
		return header('Location: ./');
	}
	
}


?>