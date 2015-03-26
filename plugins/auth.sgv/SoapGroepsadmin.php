<?php

/**
 * Klasse die de webservices van de Groepsadministratie toegankelijk maakt naar de website.
 * Wrapper around PHP SoapClient.
 **/
class SoapGroepsadmin
{
    private $soap_client = null;    // de PHP SoapClient
    private $applicatie = null;     // de Groepsadmin webservice applicatie

    /**
     * Constructor
     *
     * Creates the wrapped SoapClient.
     */
    function __construct($wsdl, $applicatie, $debug = false)
    {
        $this->applicatie = $applicatie;
        $this->soap_client = new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'location' => 'https://groepsadmin-dev-tvl.scoutsengidsenvlaanderen.be/groepsadmin/webservice', 'trace' => $debug));
    }

    /**
     * Wrapper around SoapClient->__soapCall, mainly to avoid having to set
     * Applicatie header manually on every Groepsadmin webservice request.
     *
     * @param functienaam de naam van de remote functie
     * @param parameters de parameters voor de functie
     * @return hun return waarde
     * @throw SoapFault on Error
     **/
    private function soapCall($function_name, $arguments)
    {
        // de url hieronder is gewoon de soap header name space en is altijd
        // hetzelfde ongeacht of je nu https gebruikt of 8080 of ...
        $headers = array(new SoapHeader("http://groepsadmin.scoutsengidsenvlaanderen.be/webservice/", 'Applicatie', $this->applicatie));

        return $this->soap_client->__soapCall($function_name, array($arguments), null, $headers);
    }

    /**
     * Wrapper around Groepsadmin webservice Login call.
     * Kijkt na of de logingegevens voor een specifieke gebruiker correct zijn.
     *
     * @param gebruikersnaam    gaid, lidnummer of gebruikersnaam
     * @param wachtwoord        wachtwoord
     * @return mixed            gaid indien gebruikersnaam/wachtwoord combinatie ok, false indien niet ok
     * @throws SoapFault        wanneer geen gebruikersnaam en/of wachtwoord ingegeven, of bij connectie problemen
     */
    public function login($gebruikersnaam, $wachtwoord)
    {
        $arguments = array(
            'gebruikersnaam' => $gebruikersnaam,
            'wachtwoord'     => $wachtwoord,
        );

        // catch possible SoapFault exceptions, because webservice throws one
        // in case the gebruikersnaam/wachtwoord combination is wrong
        try{
            $result = $this->soapCall("Login", $arguments);
            return $result->id;
        }catch(SoapFault $e){
            if($e->getMessage() == 'verkeerd-wachtwoord-gebruiker'){
                // hide 'fake' exception and return false instead
                return false;
            }else{
                // in case of a 'real' exception: re-throw it
                throw $e;
            }
        }
    }

    /**
     * Wrapper around Groepsadmin webservice LidGegevens call.
     * Haalt de gegevens van een lid op aan de hand van zijn lidnummer of gebruikersnaam.
     *
     * @param gebruikersnaam    gaid, lidnummer of gebruikersnaam
     * @param basis             de basis gegevens van het lid ophalen   (true voor ophalen, null voor niet ophalen)
     * @param functies          de functies van het lid ophalen         (true voor enkel actieve functies, false voor alles, null voor niet ophalen)
     * @param groepen           de groepen van het lid ophalen          (true voor enkel actieve groepen, false voor alles, null voor niet ophalen)
     * @param adressen          de adressen van het lid ophalen         (true voor ophalen, null voor niet ophalen)
     * @return array            gegevens
     **/
    public function lidGegevens($gebruikersnaam, $basis = null, $functies = null, $groepen = null, $adressen = null)
    {
        $scope = array();
        if($basis === true)     $scope['basis']                 = '';
        if(!is_null($functies)) $scope['functies']['actief']    = (bool)$functies;
        if(!is_null($groepen))  $scope['groepen']['actief']     = (bool)$groepen;
        if($adressen === true)  $scope['adressen']              = '';

        $arguments = array(
            'gebruikersnaam' => $gebruikersnaam,
            'scope'          => $scope,
        );

        // catch possible SoapFault exceptions, because webservice throws one
        // in case the user does not exist
        try{
            return $this->soapCall("LidGegevens", $arguments);
        }catch(SoapFault $e){
            if($e->getMessage() == 'Het lid is niet gekend in de db'){
                // hide 'fake' exception and return false instead
                return false;
            }else{
                // in case of a 'real' exception: re-throw it
                throw $e;
            }
        }
    }

    /**
     * Wrapper around Groepsadmin webservice LidGegevens call.
     * Haalt de gegevens van een lid op aan de hand van zijn lidnummer of gebruikersnaam.
     *
     * @param gebruikersnaam    gaid, lidnummer of gebruikersnaam
     * @param basis             de basis gegevens van het lid ophalen   (true voor ophalen, null voor niet ophalen)
     * @param functies          de functies van het lid ophalen         (true voor enkel actieve functies, false voor alles, null voor niet ophalen)
     * @param adressen          de adressen van het lid ophalen         (true voor ophalen, null voor niet ophalen)
     * @return array            gegevens
     **/
    public function lidGegevensV2($gebruikersnaam, $basis = null, $functies = null, $adressen = null)
    {
        $scope = array();
        if($basis === true)     $scope['basis']                 = '';
        if(!is_null($functies)) $scope['functies']['actief']    = (bool)$functies;
        if($adressen === true)  $scope['adressen']              = '';

        $arguments = array(
            'gebruikersnaam' => $gebruikersnaam,
            'scope'          => $scope,
        );

        // catch possible SoapFault exceptions, because webservice throws one
        // in case the user does not exist
        try{
            return $this->soapCall("LidGegevensV2", $arguments);
        }catch(SoapFault $e){
            if($e->getMessage() == 'Het lid is niet gekend in de db'){
                // hide 'fake' exception and return false instead
                return false;
            }else{
                // in case of a 'real' exception: re-throw it
                throw $e;
            }
        }
    }

    /**
     * Wrapper around Groepsadmin webservice LidGegevens call.
     * Haalt de gegevens van een lid op aan de hand van zijn lidnummer of gebruikersnaam.
     *
     * @param gebruikersnaam    gaid, lidnummer of gebruikersnaam
     * @param basis             de basis gegevens van het lid ophalen   (true voor ophalen, null voor niet ophalen)
     * @param functies          de functies van het lid ophalen         (true voor enkel actieve functies, false voor alles, null voor niet ophalen)
     * @param adressen          de adressen van het lid ophalen         (true voor ophalen, null voor niet ophalen)
     * @param gebruikersgroepen de gebruikersgroepen van het lid ophalen (true voor ophalen, null voor niet ophalen)
     * @return array            gegevens
     **/
    public function lidGegevensV3($gebruikersnaam, $basis = null, $functies = null, $adressen = null, $gebruikersgroepen = null) {
        $scope = array();
        if ($basis === true)     $scope['basis']                 = '';
        if (!is_null($functies)) $scope['functies']['actief']    = (bool)$functies;
        if ($adressen === true)  $scope['adressen']              = '';
        if ($gebruikersgroepen === true)  $scope['gebruikersgroepen'] = '';

        $arguments = array(
            'gebruikersnaam' => $gebruikersnaam,
            'scope'          => $scope,
        );

        // catch possible SoapFault exceptions, because webservice throws one
        // in case the user does not exist
        try {
            return $this->soapCall("LidGegevensV3", $arguments);
        } catch (SoapFault $e) {
            if ($e->getMessage() == 'Het lid is niet gekend in de db') {
                // hide 'fake' exception and return false instead
                return false;
            } else {
                // in case of a 'real' exception: re-throw it
                throw $e;
            }
        }
    }

    /**
     * Wrapper around Groepsadmin webservice Groepsgegevens call.
     * Haalt de gegevens van een groep op aan de hand van het groepsnummer.
     *
     * @param groepsnummer      groepsnummer
     * @param basis             basis gegevens van de groep ophalen (true voor ophalen, null voor niet ophalen)
     * @param adres             adres ophalen                       (true voor ophalen, null voor niet ophalen)
     * @param personen          grls en vga ophalen                 (true voor ophalen, null voor niet ophalen)
     * @return array            gegevens
     **/
    public function groepsGegevens($groepsnummer, $basis = null, $adres = null, $personen = null)
    {
        $scope = array();
        if($basis === true)     $scope['basis']     = '';
        if($adres === true)     $scope['adres']     = '';
        if($personen === true)  $scope['personen']  = '';

        $arguments = array(
            'groepsnummer' => $groepsnummer,
            'scope'        => $scope,
        );
        return $this->soapCall("Groepsgegevens", $arguments);
    }

    /**
     * Wrappers around SoapClient debug retrieval methods.
     * Provides access to actually send/received headers and bodies.
     * These methods ONLY WORK WHEN object was created with DEBUG TRUE.
     */
    public function getLastRequestHeaders()
    {
        return $this->soap_client->__getLastRequestHeaders();
    }
    public function getLastRequest()
    {
        return $this->soap_client->__getLastRequest();
    }
    public function getLastResponseHeaders()
    {
        return $this->soap_client->__getLastResponseHeaders();
    }
    public function getLastResponse()
    {
        return $this->soap_client->__getLastResponse();
    }
}

/**
 * Function that tries to create a SoapGroepsadmin object and runs
 * a bunch of webservice calls. Returns debug info on fail.
 *
 * This also serves as a usage example.
 *
 * Run it from command line like this:
 *
 * php -r 'require_once("SoapGroepsadmin.php"); soap_groepsadmin_test("https://groepsadmin-develop.scoutsengidsenvlaanderen.net/groepsadmin/webservice?wsdl", "test-plain");'
 * php -r 'require_once("SoapGroepsadmin.php"); soap_groepsadmin_test("https://groepsadmin.scoutsengidsenvlaanderen.be/groepsadmin/webservice?wsdl", "dokuwiki", false);'
 *
 */
function soap_groepsadmin_test($wsdl, $applicatie, $debug = true)
{
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);

    try{
        $ga = new SoapGroepsadmin($wsdl, $applicatie, $debug);
    }catch(SoapFault $e){
        echo("Probleem bij aanmaken SoapGroepsadmin object:\n");
        print_r($e);
    }

    echo("\nOpvragen lidgegevens van gebruiker hoplakonijn:\n");
    $result = null;
    try{
        $result = $ga->lidGegevens('hoplakonijn', true, true);
    }catch(SoapFault $e){
        echo("Exception:\n");
        print_r($e);
    }
    echo("\nRequest Headers:\n");
    print_r($ga->getLastRequestHeaders());
    echo("\nRequest Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastRequest()));
    echo("\nResponse Headers:\n");
    print_r($ga->getLastResponseHeaders());
    echo("\nResponse Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastResponse()));
    echo("\nResulting Object:\n");
    var_dump($result);

    echo("\nOpvragen groepsgegevens van groep Personeel Secretariaat X1027G:\n");
    $result = null;
    try{
        $result = $ga->groepsGegevens('X1027G', true, true, true);
    }catch(SoapFault $e){
        echo("Exception:\n");
        print_r($e);
    }
    echo("\nRequest Headers:\n");
    print_r($ga->getLastRequestHeaders());
    echo("\nRequest Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastRequest()));
    echo("\nResponse Headers:\n");
    print_r($ga->getLastResponseHeaders());
    echo("\nResponse Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastResponse()));
    echo("\nResulting Object:\n");
    var_dump($result);

    echo("\nLogin poging van hoplakonijn met wachtwoord nietmijnechtwachtwoord:\n");
    $result = null;
    try{
        $result = $ga->login('hoplakonijn', 'nietmijnechtwachtwoord');
    }catch(SoapFault $e){
        echo("Exception:\n");
        print_r($e);
    }
    echo("\nRequest Headers:\n");
    print_r($ga->getLastRequestHeaders());
    echo("\nRequest Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastRequest()));
    echo("\nResponse Headers:\n");
    print_r($ga->getLastResponseHeaders());
    echo("\nResponse Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastResponse()));
    echo("\nResulting Object:\n");
    var_dump($result);

    try{
        $ga = new SoapGroepsadmin($wsdl, $applicatie, $debug);
    }catch(SoapFault $e){
        echo("Probleem bij aanmaken SoapGroepsadmin object:\n");
        print_r($e);
    }

    echo("\nOpvragen lidgegevens (v3) van gebruiker tvl:\n");
    $result = null;
    try{
        $result = $ga->lidGegevensV3('tvl', true, true, true, true);
    }catch(SoapFault $e){
        echo("Exception:\n");
        print_r($e);
    }
    echo("\nRequest Headers:\n");
    print_r($ga->getLastRequestHeaders());
    echo("\nRequest Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastRequest()));
    echo("\nResponse Headers:\n");
    print_r($ga->getLastResponseHeaders());
    echo("\nResponse Body:\n");
    print_r(soap_groepsadmin_format_xml($ga->getLastResponse()));
    echo("\nResulting Object:\n");
    var_dump($result);
}

function soap_groepsadmin_format_xml($string)
{
    if($string === '' || is_null($string))
        return '';

    $dom = new DOMDocument();
    $dom->loadXML($string);
    $dom->formatOutput = true;
    return($dom->saveXML());
}



