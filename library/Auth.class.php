<?php
namespace Auth;

class User
{
    private $id;
    private $username;
    private $user_id;
    private $user_job;
    private $is_authorized = false;

    public function __construct($username = null, $password = null)
    {
        $this->username = $username;
    }

    public function __destruct()
    {
    }

    public static function isAuthorized()
    {
        if (!empty($_SESSION["user_id"])) {
            return (bool) $_SESSION["user_id"];
        }
        return false;
    }
	
    public function authorize($host, $DN,  $username, $password, $remember=false)
    {
        if ($this->LC=ldap_connect($host,$Port='389')) {
			ldap_set_option($this->LC, LDAP_OPT_PROTOCOL_VERSION, 3); 
			ldap_set_option($this->LC, LDAP_OPT_REFERRALS, 0); 
			ldap_set_option($this->LC, LDAP_OPT_SIZELIMIT, 0); 
			ldap_set_option($this->LC, LDAP_OPT_TIMELIMIT, 0); 
			if ($LB=ldap_bind($this->LC, $username.'@'.$DN, $password)) {
				$this->is_authorized = true;
				$LS	=	ldap_search(
						$this->LC
						,'DC='.implode(', DC=', explode ('.',$DN))
						,"samaccountname=".$username
				);
				$LE	=	ldap_get_entries($this->LC, $LS);
				
				$Temp_guid = bin2hex($LE[0]['objectguid'][0]);
				$Temp_guid_ ='';
				for ($i = 0; $i <= strlen($Temp_guid)-2; $i = $i+2)
					$Temp_guid_ .=  '\\'.substr($Temp_guid, $i, 2);
				$this->user_id =$Temp_guid_;
				
				$Temp_job_ = $this->mb_ucfirst(Trim($LE[0]['title'][0]));
				$Temp_job_ = str_replace('i'		,'I'			,$Temp_job_);
				$Temp_job_ = str_replace('Зам.'	,'Заместитель'		,$Temp_job_);
				$Temp_job_ = str_replace('зам.'	,'заместителя'		,$Temp_job_);
				$Temp_job_ = str_replace('№'		,'№ '			,$Temp_job_);
				$Temp_job_ = str_replace('кат.'	,'категории'		,$Temp_job_);
				$Temp_job_ = str_replace('  '		,' '			,$Temp_job_);
				$Temp_job_ = str_replace('1'	,'I'				,$Temp_job_);
				$Temp_job_ = str_replace('2'	,'II'				,$Temp_job_);
				$Temp_job_ = str_replace('I-й','I'					,$Temp_job_);
				$Temp_job_ = str_replace('I-ой','I'					,$Temp_job_);
				$Temp_job_ = str_replace('-',' - '					,$Temp_job_);
				$Temp_job_ = str_replace('–',' - '					,$Temp_job_);
				$Temp_job_ = str_replace(' нач.',' начальника '		,$Temp_job_);
				$this->user_job =$Temp_job_;
				
				$this->saveSession($remember);
			}
			Else
				$this->is_authorized = false;
		}
		Else 
			$this->is_authorized = false;

        return $this->is_authorized;
    }

	public function mb_ucfirst($str, $encoding='UTF-8'){
			$str = mb_ereg_replace('^[\ ]+', '', $str);
			$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).mb_substr($str, 1, mb_strlen($str), $encoding);
			return $str;
	}
	
    public function logout()
    {
        if (!empty($_SESSION["user_id"])) {
            unset($_SESSION["user_id"]);
        }
    }

    public function saveSession($remember = false, $http_only = true, $days = 7)
    {
        $_SESSION["user_id"] = $this->user_id;
		$_SESSION["user_job"] = $this->user_job;

        if ($remember) {
            // Save session id in cookies
            $sid = session_id();

            $expire = time() + $days * 24 * 3600;
            $domain = ""; // default domain
            $secure = false;
            $path = "/";

            $cookie = setcookie("sid", $sid, $expire, $path, $domain, $secure, $http_only);
        }
    }
}
