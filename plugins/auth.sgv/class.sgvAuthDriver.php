<?php

defined('AJXP_EXEC') or die('Access not allowed');

require('SoapGroepsadmin.php');

/* Dit lijkt enkel te werken op domeinnaam, niet op IP-adres! */
define('WSDL', 'http://pc2013-215.vvksm.local:8080/groepsadmin/webservice?wsdl');
/* define('WSDL', 'https://groepsadmin.scoutsengidsenvlaanderen.be/groepsadmin/webservice?wsdl'); */

/**
 * @package AjaXplorer_Plugins
 * @subpackage Auth
 */
class sgvAuthDriver extends AbstractAuthDriver
{
    public $driverName = "sgv";
    private $ga;

    public function init($options) {

        parent::init($options);

        //error_log("WSDL: " . WSDL);
        $this->ga = new SoapGroepsadmin(WSDL, 'test-plain', true);
    }

    public function userExists($login) {

        // A message like 'This user does not exist.' should not be shown for security reasons.
        return true;
    }

    public function usersEditable() {

        return false;
    }

    public function passwordsEditable() {

        return false;
    }

    public function checkPassword($login, $pass, $seed) {

        $this->logInfo(__FUNCTION__, 'Login: ' . $login);

        if ($login === 'admin') {
            return sha1($pass) === $_SERVER["ADMIN_PW_SHA1"];
        } else {
            $id = $this->ga->login($login, $pass);
            return gettype($id) === 'string';
        }
    }

    public function getGroepsadminId($login) {

        $this->logInfo(__FUNCTION__, 'Login: ' . $login);

        if ($login === 'admin') {
            return $login;
        } else {
            return $this->ga->lidGegevensV3($login, null, null, null, null)->id;
        }
    }

    public function updateUserObject(&$userObject){

        $this->logInfo(__FUNCTION__, 'User id: ' . $userObject->id);

        //error_log('Roles before: ' . print_r($userObject->rights["ajxp.roles"], true));

        parent::updateUserObject($userObject);

        if ($userObject->id === 'admin') {
            $userObject->personalRole->setParameterValue("core.conf", "USER_DISPLAY_NAME", 'Admin');
            $userObject->personalRole->setParameterValue("core.conf", "email", 'informatica@scoutsengidenvlaanderen.be');
            AuthService::updateRole($userObject->personalRole);

            return;
        }

        $lidGegevens = $this->ga->lidGegevensV3($userObject->id, true, null, null, true);

        $name = $lidGegevens->voornaam . ' ' . $lidGegevens->naam;
        $email = $lidGegevens->emailadres;

        $userObject->personalRole->setParameterValue("core.conf", "USER_DISPLAY_NAME", $name);
        $userObject->personalRole->setParameterValue("core.conf", "email", $email);
        $userObject->personalRole->clearAcls();
        AuthService::updateRole($userObject->personalRole);

        foreach (array_keys($userObject->getRoles()) as $roleId) {
            if (preg_match('/^[A-Z][0-9]{4}[A-Z]/', $roleId)) {
                $userObject->removeRole($roleId);
            }
        }

        $gebruikersgroepen = $lidGegevens->gebruikersgroepen->gebruikersgroep;
        if ($gebruikersgroepen !== null) {
            foreach ($gebruikersgroepen as $gebruikersgroep) {
                if (preg_match('/^[A-Z][0-9]{4}[A-Z]/', $gebruikersgroep->id)) {
                    $naam = $gebruikersgroep->naam;
                    if ($naam === strtoupper($naam) || $naam === strtolower($naam)) {
                        $naam = ucwords(strtolower(str_replace('_', ' ', $naam)));
                    }
                    $this->createWorkspace($gebruikersgroep->id, $naam);

                    $role = $this->getRole($gebruikersgroep->id, isset($gebruikersgroep->beheersrecht) ? 'r': 'rw');
                    $userObject->addRole($role);
                }
            }
        }

        //error_log('Roles after: ' . print_r($userObject->rights["ajxp.roles"], true));
        //error_log('user after: ' . print_r($userObject, true));

        $userObject->save("superuser"); // update the database
    }

    private function createWorkspace($workspace_id, $workspace_titel) {

        $repo = ConfService::getRepositoryById($workspace_id);

        if ($repo === null) {
            $this->logInfo(__FUNCTION__, 'New repo: ' . $workspace_id);

            $repo = new Repository($workspace_id, $workspace_titel, 'fs'); # fs -> filesystem
            $repo->uuid = $workspace_id;
            $repo->path = 'AJXP_DATA_PATH/sgv/' . $workspace_id;
            $repo->options["PATH"] = $repo->path; // required for backward compatibility
            $repo->options["CREATE"] = $repo->create; // required for backward compatibility
            $repo->setSlug($workspace_id);
            $repo->isTemplate = false; // TODO check required
            $repo->setInferOptionsFromParent(false); // TODO check required
            $repo->setDescription($workspace_id);

            ConfService::addRepository($repo);
        } else if ($repo->display !== $workspace_titel or $repo->getDescription() !== $workspace_id) {
            $repo->display = $workspace_titel;
            $repo->setDescription($workspace_id);

            ConfService::replaceRepository($workspace_id, $repo);
        }
    }

    private function getRole($workspace_id, $beheersrecht) {

        $roleId = $workspace_id . ' ' . $beheersrecht . ' role';
        $role = AuthService::getRole($roleId, true);

        $acl = $role->getAcl($workspace_id);
        if (empty($acl)) {
            $role->setAcl($workspace_id, $beheersrecht);
            AuthService::updateRole($role); // inserts a new role or replaces an existing roles
        }

        return $role;
    }
}
