<?php
/*
 * Copyright 2007-2011 Jean-Christophe Ghio (jissey)
 * This file is part of Pydio.
 *
 * Pydio is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pydio is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Pydio.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The latest code can be found at <http://pyd.io/>.
 */
defined('AJXP_EXEC') or die( 'Access not allowed');
/**
 * Abstract representation of an access to FEU athentification module(CMS Made Simple).
 * like auth.remote:
 *   In slave mode, the login dialog is not displayed in AJXP.
 *   If the user directly go to the main page, (s)he's redirected to the LOGIN_URL.
 *   The logout button link to LOGOUT_URL.
 *   The user will log in on FEU, and the remote script will call us, as
 *   ajxpPath/content.php?get_action=login_cmsms&uid=usernam&sessionid=sessionid in FEU loggedin table

 * You must modify conf.php like this:
 * 	"AUTH_DRIVER" => array(
        "NAME"		=> "cmsms",
        "OPTIONS"	=> array(
            "SQL_DRIVER" => Array(
                                    'driver' => 'mysql',
                                    'host' => 'localhost',
                                    'username' => 'root',
                                    'password' => '',
                                    'database' => 'your_cmsms_db'
                                    ),
            "PREFIX_TABLE"          => 'your_prefix',
            "LOGIN_URL"				=> 'http://url of FEU login form',
            "LOGOUT_URL"			=> 'http://url you want with the logout button ',
            "SECRET"				=> '1234' //the common secret code between the two application (store in cmsms database on the other side)
            )

    ),
 * @package AjaXplorer_Plugins
 * @subpackage Auth
 */
class cmsmsAuthDriver extends AbstractAuthDriver
{
    public $sqlDriver;
    public $driverName = "cmsms";
    public $slaveMode;
    public $secret;
    public $secret_cmsms;
    public $urls;
    public $prefix;
    public $groupid;


    public function init($options)
    {
        parent::init($options);
        $this->sqlDriver = $this->options["SQL_DRIVER"];
        try {
            dibi::connect($this->sqlDriver);
        } catch (DibiException $e) {
            echo get_class($e), ': ', $e->getMessage(), "\n";
            exit(1);
        }
        $this->secret = $this->options["SECRET"];
        $this->prefix = $this->options["PREFIX_TABLE"];
        $this->urls = array($this->options["LOGIN_URL"], $this->options["LOGOUT_URL"]);
        $this->slaveMode = true;
        $res = dibi::query("SELECT sitepref_value FROM [".$this->prefix."siteprefs] WHERE sitepref_name = 'FEUajaxplorer_mapi_pref_ajxp_auth_group'");
        $grp = $res->fetchSingle();
        $this->groupid = $grp;
        $res2 = dibi::query("SELECT sitepref_value FROM [".$this->prefix."siteprefs] WHERE sitepref_name = 'FEUajaxplorer_mapi_pref_ajxp_secret'");
        $sec = $res2->fetchSingle();
        $this->secret_cmsms = trim($sec);

    }

    public function listUsers()
    {
        $res = dibi::query("SELECT * FROM [".$this->prefix."module_feusers_users], [".$this->prefix."module_feusers_belongs] WHERE id = userid AND groupid=%s", $this->groupid);
        $pairs = $res->fetchPairs('username', 'password');
        return $pairs;
    }

    public function userExists($login)
    {
        $res = dibi::query("SELECT * FROM [".$this->prefix."module_feusers_users], [".$this->prefix."module_feusers_belongs] WHERE [username]=%s AND id = userid AND groupid=%s", $login, $this->groupid);
        return($res->getRowCount());
    }

    public function userIsConnected($username, $sessionid)
    {
        $userid = $this->getUserId($username);
        $res = dibi::query("SELECT * FROM [".$this->prefix."module_feusers_loggedin] WHERE [userid]=%s AND sessionid=%s", $userid, $sessionid);
        return($res->getRowCount());
    }

    // Never call if used with the initial cmsms ajxp module.
    // We don't check the password because we use sessionid and userid from FEU loggedin table.
    public function checkPassword($login, $pass, $seed)
    {
        $userStoredPass = $this->getUserPass($login);
        if(!$userStoredPass) return false;
        if (md5($pass) === $userStoredPass) {
        $loggedinData['sessionid']=session_id();
        $loggedinData['lastused']=time();
        $loggedinData['userid']=$this->getUserId($login);
        dibi::query('INSERT INTO ['.$this->prefix.'module_feusers_loggedin]', $loggedinData);
        }

            return ($userStoredPass === md5($pass));
    }

    public function usersEditable()
    {
        return true;
    }
    public function passwordsEditable()
    {
        return true;
    }

    public function createUser($login, $passwd)
    {
        $users = $this->listUsers();
        if(!is_array($users)) $users = array();
        if(array_key_exists($login, $users)) return "exists";
        $userData = array("username" => $login);
        $userData["password"] = md5($passwd);
        $userData["id"]=$this->getUserSeq()+1;
        $userData["createdate"]=date("Y-m-d H:i:s");
        dibi::query('INSERT INTO ['.$this->prefix.'module_feusers_users]', $userData);
        $this->setUserSeq($userData["id"]);
        $belongsData["userid"]=$userData["id"];
        $belongsData["groupid"]=$this->groupid;
        dibi::query('INSERT INTO ['.$this->prefix.'module_feusers_belongs]', $belongsData);
    }
    public function changePassword($login, $newPass)
    {
        $users = $this->listUsers();
        if(!is_array($users) || !array_key_exists($login, $users)) return ;
        $userData = array("username" => $login);
        $userData["password"] = md5($newPass);
        dibi::query("UPDATE [".$this->prefix."module_feusers_users] SET ", $userData, "WHERE [username]=%s", $login);
    }
    public function deleteUser($login)
    {
        $uid=$this->getUserId($login);
        //suppress all references in CMSMS FEU tables
        dibi::query("DELETE FROM [".$this->prefix."module_feusers_users] WHERE [username]=%s", $login);
        dibi::query("DELETE FROM [".$this->prefix."module_feusers_belongs] WHERE [userid]=%s", $uid);
        dibi::query("DELETE FROM [".$this->prefix."module_feusers_properties] WHERE [userid]=%s", $uid);
    }

    public function getUserPass($login)
    {
        $res = dibi::query("SELECT [password] FROM [".$this->prefix."module_feusers_users], [".$this->prefix."module_feusers_belongs] WHERE [username]=%s AND id = userid AND groupid= %s", $login, $this->groupid);
        $pass = $res->fetchSingle();
        return $pass;
    }

    public function getUserSeq()
    {
        $res = dibi::query("SELECT [id] FROM [".$this->prefix."module_feusers_users_seq]");
        $seq = $res->fetchSingle();
        return $seq;
    }

    public function setUserSeq($num)
    {
        $res = dibi::query("UPDATE [".$this->prefix."module_feusers_users_seq] SET [id]=%s", $num);
    }

    public function getUserId($login)
    {
        $res = dibi::query("SELECT id FROM [".$this->prefix."module_feusers_users], [".$this->prefix."module_feusers_belongs] WHERE [username]=%s AND id = userid AND groupid= %s", $login, $this->groupid);
        $uid = $res->fetchSingle();
        return $uid;
    }
    public function listUsersSerial()
    {
        return AJXP_Utils::loadSerialFile($this->usersSerFile);
    }
    public function getLoginRedirect()
    {
    if ($this->slaveMode) {
        if (isset($_SESSION["AJXP_USER"])) return false;
        return $this->urls[0];
    }
    return false;
}

    public function getLogoutRedirect()
    {
        if ($this->slaveMode) {
            return $this->urls[1];
        }
        return false;
        }
// call by CMS Made Simple with the followings arguments:
// username : username of the FEU user
// sessionid : num. of session in the loggedin table for matching the logging the user here.
    public function login_cmsms()
    {
    $username=strip_tags($_GET['username']);
    $sessionid=strip_tags($_GET['sessionid']);
    //verifying the CIA secret code...
    if($this->secret != $this->secret_cmsms) exit( "secret");//return header("Location: ".$this->getLoginRedirect());
    //verifying that the user exists (in the right group)
    if(!$this->userExists($username)) exit( "exists");//return header("Location: ".$this->getLoginRedirect());
    //verifying that the user is connected
    if(!$this->userIsConnected($username, $sessionid)) exit( "connect");//return header("Location: ".$this->getLoginRedirect());
    //all tests passed...we connect the user
    if($log=AuthService::logUser($username,"",true)==1) return header("Location: index.php");
    }
}
