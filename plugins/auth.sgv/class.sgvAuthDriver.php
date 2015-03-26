<?php

defined('AJXP_EXEC') or die('Access not allowed');

require('SoapGroepsadmin.php');

define('WSDL', 'https://groepsadmin-dev-tvl.scoutsengidsenvlaanderen.be/groepsadmin/webservice?wsdl');
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
        $this->ga = new SoapGroepsadmin(WSDL, 'sgv-org', true);
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

        if (sha1($pass) === $_SERVER["ADMIN_PW_SHA1"]) {
            return true;
        } else if ($login !== 'admin') {
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

        parent::updateUserObject($userObject);

        if ($userObject->id === 'admin') {
            $userObject->personalRole->setParameterValue("core.conf", "USER_DISPLAY_NAME", 'Admin');
            $userObject->personalRole->setParameterValue("core.conf", "email", 'informatica@scoutsengidsenvlaanderen.be');
            $userObject->save("superuser"); // save to the database (in ajxp_roles, not in ajxp_users)

            return;
        }

        $lidGegevens = $this->ga->lidGegevensV3($userObject->id, true, null, null, true);

        $name = $lidGegevens->voornaam . ' ' . $lidGegevens->naam;
        $email = $lidGegevens->emailadres;

        $userObject->personalRole->setParameterValue("core.conf", "USER_DISPLAY_NAME", $name);
        $userObject->personalRole->setParameterValue("core.conf", "email", $email);
        $userObject->personalRole->clearAcls();

        $gebruikersgroepen = $lidGegevens->gebruikersgroepen->gebruikersgroep;
        if ($gebruikersgroepen !== null) {
            foreach ($gebruikersgroepen as $gebruikersgroep) {
                if (preg_match('/^[A-Z][0-9]{4}[A-Z]/', $gebruikersgroep->id)) {
                    $naam = $gebruikersgroep->naam;
                    if ($naam === strtoupper($naam) || $naam === strtolower($naam)) {
                        $naam = ucwords(strtolower(str_replace('_', ' ', $naam)));
                    }
                    $this->createWorkspace($gebruikersgroep->id, $naam);

                    $recht = isset($gebruikersgroep->beheersrecht) ? 'r': 'rw';
                    $userObject->personalRole->setAcl($gebruikersgroep->id, $recht);
                }
            }
        }

        $userObject->save("superuser"); // save to the database (in ajxp_roles, not in ajxp_users)
        AuthService::updateDefaultRights($userObject); // reload the rights from the ACL
    }

    private function createWorkspace($workspace_id, $workspace_titel) {

        $repo = ConfService::getRepositoryById($workspace_id);

        if ($repo === null) {
            $this->logInfo(__FUNCTION__, 'New repo: ' . $workspace_id);

            $repo = new Repository($workspace_id, $workspace_titel, 'fs'); # fs -> filesystem
            $repo->uuid = $workspace_id;
            $repo->path = 'AJXP_DATA_PATH/sgv/' . $workspace_id;
            $repo->options["PATH"] = $repo->path; // required for backward compatibility

            ConfService::addRepository($repo);
        }

        $repo->create = false; // created and controlled by an external script
        $repo->display = $workspace_titel;
        $repo->isTemplate = false;
        $repo->enabled = is_dir(SystemTextEncoding::toStorageEncoding($repo->getOption("PATH")));
        if (!$repo->enabled) {
            $repo->display = 'DISABLED';
        }

        $repo->setInferOptionsFromParent(false);
        $repo->setSlug($workspace_id);

        $repo->options["CREATE"] = $repo->create; // backward compatibility
        $repo->options["META_SOURCES"]["meta.git"] = array(); // (re)activate the git plugin

        ConfService::replaceRepository($workspace_id, $repo);
    }
}
