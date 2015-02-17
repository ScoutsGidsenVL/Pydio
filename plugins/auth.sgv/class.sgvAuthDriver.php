<?php

defined('AJXP_EXEC') or die( 'Access not allowed');

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

        $this->ga = new SoapGroepsadmin(WSDL, 'test-plain', true);
    }

    public function userExists($login) {

        return true;
    }

    public function checkPassword($login, $pass, $seed) {

        $id = $this->ga->login($login, $pass);

        return (gettype($id) === 'string') or (sha1($pass) === $_SERVER["ADMIN_PW_SHA1"]);
    }

    public function usersEditable() {

        return false;
    }

    public function passwordsEditable() {

        return false;
    }

    
    /* Using $this->logInfo(...) inside this method causes the logged in user to change! */
    public function updateUserObject(&$userObject){

        parent::updateUserObject($userObject);

        $groepen = $this->ga->lidGegevensV2($userObject->id, null, true, null)->groepen->groep;
        if ($groepen !== null) {

            // De gebruikersgroepen worden gegenereerd op basis van de groepen van de gebruiker.
            // Dit is goed genoeg voor demo's, maar na de demo's moet dit via de gebruikersgroepen.
            $gebruikersgroepen = array();
            foreach ($groepen as $groep) {
                $groepsnummer = $groep->groepsnummer;
                $groepsnaam = $groep->naam;
                if ($groepsnaam === strtoupper($groepsnaam)) {
                    $groepsnaam = str_replace('/', '-', ucwords(strtolower($groepsnaam)));
                }

                $district = preg_match('/^..000D$/', $groepsnummer);
                $gouw = preg_match('/^..000P$/', $groepsnummer);
                $xgroep = preg_match('/^X.....$/', $groepsnummer);

                if ($district or $gouw or $xgroep) {
                    $gebruikersgroepen[$groepsnummer] = $groepsnaam;
                }

                if ($gouw) {
                    $gebruikersgroepen[$groepsnummer . '-BU'] = str_replace('Gouw', 'Gouwbureau', $groepsnaam);
                    $gebruikersgroepen[$groepsnummer . '-RA'] = str_replace('Gouw', 'Gouwraad', $groepsnaam);
                }
            }

            $gebruikersgroepen['X4002G'] = 'Verbondsraad';

            foreach ($gebruikersgroepen as $workspace_id => $workspace_titel) {
                $this->createWorkspace($workspace_id, $workspace_titel);

                $role = $this->getRole($workspace_id);
                $userObject->addRole($role);
            }

            $userObject->save("superuser");
            AuthService::updateUser($userObject);
        }
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

    private function getRole($workspace_id) {

        $roleId = $workspace_id . ' role';
        $role = AuthService::getRole($roleId, true);

        $acl = $role->getAcl($workspace_id);
        if (empty($acl)) {
            $role->setAcl($workspace_id, 'rw');
            AuthService::updateRole($role); // inserts a new role or replaces an existing roles
        }

        return $role;
    }
}
