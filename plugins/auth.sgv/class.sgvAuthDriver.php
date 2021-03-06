<?php

defined('AJXP_EXEC') or die('Access not allowed');

require('SoapGroepsadmin.php');

define('WSDL', 'https://groepsadmin.scoutsengidsenvlaanderen.be/groepsadmin/webservice?wsdl');

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
            try {
                $id = $this->ga->login($login, $pass);
            } catch (Exception $e) {
                throw new Exception("Probleem met de koppeling met de groepsadministratie.\n\n".$e->getMessage());
            }
            return gettype($id) === 'string';
        }
    }

    public function getGroepsadminId($login) {

        if ($login === 'admin' || preg_match('/^[0-9a-f]{32}$/', $login)) {
            return $login;
        } else {
            $this->logInfo(__FUNCTION__, 'Looking up id: '.$login);

            try {
                return $this->ga->lidGegevensV3($login, null, null, null, null)->id;
            } catch (Exception $e) {
                throw new Exception("Probleem met de koppeling met de groepsadministratie.\n\n".$e->getMessage());
            }
        }
    }

    public function updateUserObject(&$userObject){

        parent::updateUserObject($userObject);

        if ($userObject->id === 'admin') {
            $userObject->personalRole->setParameterValue("core.conf", "USER_DISPLAY_NAME", 'Admin');
            $userObject->personalRole->setParameterValue("core.conf", "email", 'info@scoutsengidsenvlaanderen.be');
            $userObject->save("superuser"); // save to the database (in ajxp_roles, not in ajxp_users)

            return;
        }

        $last_update = $userObject->personalRole->filterParameterValue("core.conf", "last_update", 'AJXP_REPO_SCOPE_ALL', 0);
        $outdated = $last_update + 300 < time();

        if ($outdated) {
            $this->logInfo(__FUNCTION__, 'User id: ' . $userObject->id);

            try {
                $lidGegevens = $this->ga->lidGegevensV3($userObject->id, true, null, null, true);
            } catch (Exception $e) {
                throw new Exception("Probleem met de koppeling met de groepsadministratie.\n\n".$e->getMessage());
            }

            $name = $lidGegevens->voornaam . ' ' . $lidGegevens->naam;
            $email = $lidGegevens->emailadres;

            $userObject->personalRole->setParameterValue("core.conf", "USER_DISPLAY_NAME", $name);
            $userObject->personalRole->setParameterValue("core.conf", "email", $email);
            $userObject->personalRole->setParameterValue("core.conf", "last_update", time());
            $userObject->personalRole->clearAcls();

            $gebruikersgroepen = $lidGegevens->gebruikersgroepen->gebruikersgroep;
            if ($gebruikersgroepen !== null) {
                foreach ($gebruikersgroepen as $gebruikersgroep) {
                    if (preg_match('/^[A-Z][0-9]{4}[A-Z]/', $gebruikersgroep->id)) {
                        $naam = str_replace('_', ' ', $gebruikersgroep->naam);
                        if ($naam === strtoupper($naam) || $naam === strtolower($naam)) {
                            $naam = ucwords(strtolower($naam));
                        }
                        $enabled = $this->updateWorkspace($gebruikersgroep->id, $naam);
                        if ($enabled) {
                            $recht = isset($gebruikersgroep->beheersrecht) ? 'rw': 'r';
                            $userObject->personalRole->setAcl($gebruikersgroep->id, $recht);
                        }
                    }
                }
            }
        }

        $userObject->save("superuser"); // save to the database (in ajxp_roles, not in ajxp_users)
        AuthService::updateDefaultRights($userObject); // reload the rights from the ACL
    }

    private function updateWorkspace($workspace_id, $workspace_titel) {

        $repo = ConfService::getRepositoryById($workspace_id);

        if ($repo === null) {
            $this->logInfo(__FUNCTION__, 'New repo: ' . $workspace_id);

            $repo = new Repository($workspace_id, $workspace_titel, 'fs'); # fs -> filesystem
            $repo->uuid = $workspace_id;

            ConfService::addRepository($repo);
        }

        $repo->path = 'AJXP_DATA_PATH/sgv/' . $workspace_id;
        $repo->options["PATH"] = $repo->path; // required for backward compatibility

        $repo->create = false; // created and controlled by an external script
        $repo->display = $workspace_titel;
        $repo->isTemplate = false;
        $repo->enabled = is_dir(SystemTextEncoding::toStorageEncoding($repo->getOption("PATH")));
        if (!$repo->enabled) {
            $repo->display = '[path not found] ' . $repo->display;
        }

        $repo->setInferOptionsFromParent(false);
        $repo->setSlug($workspace_id);

        $repo->options["CREATE"] = $repo->create; // backward compatibility

        $repo->options["META_SOURCES"] = array(); // clear old meta settings
        $repo->options["META_SOURCES"]["meta.git"] = array(); // (re)activate the git plugin
        $repo->options["META_SOURCES"]["index.lucene"] = array("index_content" => true, "index_meta_fields" => "", "repository_specific_keywords" => "");

        ConfService::replaceRepository($workspace_id, $repo);

        return $repo->enabled;
    }
}
