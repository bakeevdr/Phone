<?php
require_once("./config.php");
include './library/Auth.class.php';
include './library/AjaxRequest.class.php';



if (!empty($_COOKIE['sid'])) {
    // check session id in cookies
    session_id($_COOKIE['sid']);
}
session_start();

class AuthorizationAjaxRequest extends AjaxRequest
{
    public $actions = array(
        "login" => "login",
        "logout" => "logout",
    );

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            // Method Not Allowed
            http_response_code(405);
            header("Allow: POST");
            $this->setFieldError("main", "Method Not Allowed");
            return;
        }
        setcookie("sid", "");

        $username = $this->getRequestParam("username");
        $password = $this->getRequestParam("password");
        $remember = !!$this->getRequestParam("remember-me");

        if (empty($username)) {
            $this->setFieldError("username", "Введите имя пользователя");
            return;
        }
		
        if (empty($password)) {
            $this->setFieldError("password", "Введите пароль");
            return;
        }

		$UserCurentIP = explode('.',$_SERVER["REMOTE_ADDR"])[1];
		$host ='';
		global $P_LDAP;
		foreach ($P_LDAP as $P_LDAP_v) {
			if (is_array($P_LDAP_v['Server'])) {
				if ((explode('.',$P_LDAP_v['Server'][0])[1])===($UserCurentIP)) {
					$host	= $P_LDAP_v['Server'][0];
					$DN		= $P_LDAP_v['DN'];
					break;
				}
			}
			else{
				if ((explode('.',$P_LDAP_v['Server'])[1])===($UserCurentIP)) {
					$host	= $P_LDAP_v['Server'];
					$DN		= $P_LDAP_v['DN'];
					break;
				}
			}/**/
		}
		$user = new Auth\User();
		$auth_result = $user->authorize($host, $DN,  $username, $password, $remember);
		if (!$auth_result) {
			$this->setFieldError("password", "Неверный логин или пароль ");
			return;
		}/**/

		$this->status = "ok";
		$this->setResponse("redirect", ".");
		$this->message = sprintf("Hello, %s! Доступ разрешен.", $username);
	}

    public function logout()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            header("Allow: POST");
            $this->setFieldError("main", "Method Not Allowed");
            return;
        }

        setcookie("sid", "");

        $user = new Auth\User();
        $user->logout();/**/

        $this->setResponse("redirect", ".");
        $this->status = "ok";
    }

}

$ajaxRequest = new AuthorizationAjaxRequest($_REQUEST);
$ajaxRequest->showResponse();
