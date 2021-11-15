<?php
class Edit extends Registration {
	function __construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB)
	{
		parent::__construct($HOST_DB, $USER_NAME_DB, $PASS_DB, $NAME_DB);
	}
	
	public function renderingEditPage($hash)
	{
		$user = $this->getUser($hash);
		$data['idUser'] = $user['id'];
		$data['inputFio'] = $user['fio'];
		$data['inputPasswordOld'] = '';
		$data['inputPassword1'] = '';
		$data['inputPassword2'] = '';
		$this->renderingFormEdit($data);
		
	}

	private function renderingFormEdit($data=false, $err=false)
	{
		echo '<div class="mx-auto col-12 my-2 text-center alert alert-primary">
				Редактирование данных о пользователе
			</div>';

		if($err)
			echo '<div class="mx-auto col-12 my-2 text-center alert alert-warning">
				'.$err.'
			</div>';

		if(!$data) 
			$data = array('inputFio' => '', 'inputPasswordOld' => '', 'inputPassword1' => '', 'inputPassword2' => '');

		printf('
			<div class="col-12 border my-2">
				<form action="./index.php" method="POST">
					<div class="form-group">
						<label for="inputFio">ФИО</label>
						<input type="fio" class="form-control" name="inputFio" value="%s">
					</div>
					<div class="form-group">
						<label for="inputPasswordOld">Старый пароль</label>
						<input type="password" class="form-control" name="inputPasswordOld" value="%s">
					</div>
					<div class="form-group">
						<label for="inputPassword1">Новый пароль</label>
						<input type="password" class="form-control" name="inputPassword1" value="%s">
					</div>
					<div class="form-group">
						<label for="inputPassword2">Подтвердите новый пароль</label>
						<input type="password" class="form-control" name="inputPassword2" value="%s">
					</div>
					<input type="hidden" name="idUser" value="%s">
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		',$data['inputFio'], $data['inputPasswordOld'], $data['inputPassword1'], $data['inputPassword2'], $data['idUser']);
	
	}

	public function prepareEdit($data)
	{
		$inputFio = trim($data['inputFio']);
		if(empty($inputFio))
		{
			$err = 'Поле ФИО пустое!';
			$this->renderingFormEdit($data, $err);
			exit();
		}

		if(!empty($data['inputPasswordOld']) && !empty($data['idUser']))
		{
			if(!$this->checkPassword($data['inputPasswordOld'], $data['idUser']))
			{
				$err = 'Старый пароль введен непарвильно!';
				$this->renderingFormEdit($data, $err);
				exit();
			}

			$inputPassword1 = trim($data['inputPassword1']);
			if(empty($inputPassword1))
			{
				$err = 'Поле Новый пароль пустое!';
				$this->renderingFormEdit($data, $err);
				exit();
			}

			$inputPassword2 = trim($data['inputPassword2']);
			if(empty($inputPassword2))
			{
				$err = 'Поле Подтвердите новый пароль пустое!';
				$this->renderingFormEdit($data, $err);
				exit();
			}

			$pass1 = md5($data['inputPassword1']);
			$pass2 = md5($data['inputPassword2']);

			if($pass1 !== $pass2)
			{
				$err = 'Поля Новый пароль не совпадают!';
				$this->renderingFormEdit($data, $err);
				exit();
			}
		}

		$prepareArr = [
			'id' => $data['idUser'],
			'fio' => $inputFio,
			'password' => !empty($data['inputPasswordOld']) ? $pass1 : false
		]; 
		
		if($this->updateUser($prepareArr))
		{
			$data['inputPasswordOld'] = '';
			$data['inputPassword1'] = '';
			$data['inputPassword2'] = '';

			$err = 'Обновлено успешно!';
			$this->renderingFormEdit($data, $err);
			exit;
		}
		else
		{
			$this->renderingFormEdit($data);
		}
	}

	private function checkPassword($password, $idUser)
	{
		$pass = md5($password);

		$mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
		if(mysqli_connect_errno()) return mysqli_connect_error();
	
		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			$query_select = "SELECT users.id FROM users WHERE users.password = ? OR users.id = ? LIMIT 0, 1";
			$stmt->prepare($query_select);
			$stmt->bind_param('si', $pass, $id);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			$stmt->close();
		}

		return $id ? true : false;
	}

	private function getUser($hash)
	{
		$mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
		if(mysqli_connect_errno()) return mysqli_connect_error();
	
		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			$query_select = "SELECT users.id, users.fio
				FROM session_login
				JOIN users ON users.id = session_login.user_id
				WHERE session_login.hash = ?
				LIMIT 0, 1";
			$stmt->prepare($query_select);
			$stmt->bind_param('s', $hash);
			$stmt->execute();
			$stmt->bind_result($id, $fio);
			$stmt->fetch();
			$stmt->close();
		}

		if($id) return ['id'=>$id, 'fio'=>$fio];
	}

	private function updateUser($data)
	{
		$mysqli = new mysqli($this->HOST_DB, $this->USER_NAME_DB, $this->PASS_DB, $this->NAME_DB);
		if(mysqli_connect_errno()) return mysqli_connect_error();
	
		$stmt = $mysqli->stmt_init();
		if($stmt)
		{
			if(!empty($data['password']))
			{
				$query = "UPDATE users SET users.fio = ?, users.password = ? WHERE users.id = ?";
				$stmt->prepare($query);
				$stmt->bind_param('ssi', $data['fio'], $data['password'], $data['id']);
			}
			else
			{
				$query = "UPDATE users SET users.fio = ? WHERE users.id = ?";
				$stmt->prepare($query);
				$stmt->bind_param('si', $data['fio'], $data['id']);
			}
			$stmt->execute();
			$res = $stmt->affected_rows;
			$stmt->close();
		}
		$mysqli->close();

		return ($res) ? $res : false;
	}
}


?>