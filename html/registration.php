<?php
class Registration {
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
	
	public function renderingFormRegistration($data=false, $err=false)
	{
		if($err)
			echo '<div class="mx-auto col-12 my-2 text-center alert alert-warning">
				'.$err.'
			</div>';

		if(!$data)
			$data = ['inputEmail' => '', 'inputLogin' => '', 'inputFio' => '', 'inputPassword1' => '', 'inputPassword2' => ''];

		printf('
			<div class="col-12 border my-2">
				<form action="./index.php?registration=1" method="POST">
					<div class="form-group">
						<label for="inputEmail">Email</label>
						<input type="email" class="form-control" name="inputEmail" value="%s">
					</div>
					<div class="form-group">
						<label for="inputLogin">Login</label>
						<input type="login" class="form-control" name="inputLogin" value="%s">
					</div>
					<div class="form-group">
						<label for="inputFio">ФИО</label>
						<input type="fio" class="form-control" name="inputFio" value="%s">
					</div>
					<div class="form-group">
						<label for="inputPassword1">Пароль</label>
						<input type="password" class="form-control" name="inputPassword1" value="%s">
					</div>
					<div class="form-group">
						<label for="inputPassword2">Подтвердите пароль</label>
						<input type="password" class="form-control" name="inputPassword2" value="%s">
					</div>
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		', $data['inputEmail'], $data['inputLogin'], $data['inputFio'], $data['inputPassword1'], $data['inputPassword2']);
	
	}

	public function prepareRegistration($data)
	{
		$inputEmail = trim($data['inputEmail']);
		if(empty($inputEmail))
		{
			$err = 'Поле Email пустое!';
			$this->renderingFormRegistration($data, $err);
			exit();
		}

		$inputLogin = trim($data['inputLogin']);
		if(empty($inputLogin))
		{
			$err = 'Поле Login пустое!';
			$this->renderingFormRegistration($data, $err);
			exit();
		}

		$inputFio = trim($data['inputFio']);
		if(empty($inputFio))
		{
			$err = 'Поле ФИО пустое!';
			$this->renderingFormRegistration($data, $err);
			exit();
		}

		$inputPassword1 = trim($data['inputPassword1']);
		if(empty($inputPassword1))
		{
			$err = 'Поле Пароль пустое!';
			$this->renderingFormRegistration($data, $err);
			exit();
		}

		$inputPassword2 = trim($data['inputPassword2']);
		if(empty($inputPassword2))
		{
			$err = 'Поле Пароль пустое!';
			$this->renderingFormRegistration($data, $err);
			exit();
		}

		$pass1 = md5($data['inputPassword1']);
		$pass2 = md5($data['inputPassword2']);

		if($pass1 !== $pass2)
		{
			$err = 'Поле Пароли не совпадают!';
			$this->renderingFormRegistration($data, $err);
			exit();
		}

		$prepareArr = [
			'email' => $inputEmail,
			'login' => $inputLogin,
			'fio' => $inputFio,
			'password' => $pass1
		]; 
		
		if($result = $this->insertRegistration($prepareArr))
		{
			if(!empty($result['check']))
			{
				$err = 'Пользователь с таким Login, уже зарегистрирован!';
				$this->renderingFormRegistration($data, $err);
				exit;
			}

			header('Location: ./');
			exit;
		}
		else
		{
			$err = 'Ошибка записи!';
			$this->renderingFormRegistration($data, $err);
		}
	}

	private function insertRegistration($data)
	{
		$mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
		if(mysqli_connect_errno()) return mysqli_connect_error();
	
		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			$query_select = "SELECT users.id FROM users WHERE users.login = ? LIMIT 0, 1";
			$stmt->prepare($query_select);
			$stmt->bind_param('s', $data['login']);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			$stmt->close();
		}

		if($id) return ['check' => true];

		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			$query_insert = "INSERT INTO users VALUES (NULL, ?, ?, ?, ?)";
			$stmt->prepare($query_insert);
			$stmt->bind_param('ssss', $data['fio'], $data['email'], $data['login'], $data['password']);
			$stmt->execute();
			$res = $stmt->affected_rows;
			$stmt->close();
		}
		$mysqli->close();

		return ($res) ? ['id' => $res, 'check' => false] : false;
	}
}


?>