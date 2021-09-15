<?php
	if (!empty($_COOKIE['sid'])) {
		session_id($_COOKIE['sid']);
	}
	session_start();
	require_once './library/Auth.class.php';
?>
<?php if (Auth\User::isAuthorized()): ?>
	<h3>Вы авторизованны</h3>
	<form class="ajax" method="post" action="./login-ajax.php">
		<input type="hidden" name="act" value="logout">
		<br>
		<div class="form-actions">
			<button class="btn btn-large btn-primary" type="submit"> Выйти </button>
		</div>
	</form>
<?php else: ?>
	<form class="form-signin ajax" method="post" action="./login-ajax.php">
		<div class="alert alert-info">После авторизации для руководства отображаются мобильные телефоны сотрудников</div>
		<div class="main-error alert alert-danger hide"></div>
		<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
			<input name="username" type="text" class="form-control input-block-level" placeholder="Имя пользователя" autofocus />
		</div>
		<div class="input-group">
			<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
			<input name="password" type="password" class="form-control input-block-level" placeholder="Ваш пароль" />
		</div>
		<div class="checkbox">
			<label><input name="remember-me"  type="checkbox" value="remember-me">Запомнить меня</label>
		</div>
		<br>
		<input type="hidden" name="act" value="login">
		<button class="btn btn-large btn-primary" type="submit"> Войти </button>
	</form>
<?php endif; ?>
<script src="./js/ajax-form.js"></script>