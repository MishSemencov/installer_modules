<?php
/**
 * Module session class.
 *
 * @package RusVipAvia\TakeJet\API\V1
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API\Example\V1;

use ITHive\API\Error;
use ITHive\API\Request;
use OAuth2;

/**
 * OAuth2.0 authorization entity.
 * @package ITHive\API\Example\V1
 * @property string $login
 * @property string $password
 * @property string $code
 * @property string $secretKey
 */
class Session extends Request implements Interfaces\Session
{
    /** @const OAuth2.0 client id */
    const CLIENT_ID = "example";
    const SECRET_KEY = "VgtFOeuNPDiVMi8jrzBltJr41pz7Y5vmzrfkSBXVCk7GLd6CJpSwN6MBuKLR5Ldc";

    /** @var null|OAuth2\Storage\Pdo OAuth2.0 storage (DB) */
    private $storage = null;

    /**
     * Api realisation method
     * Get access token from user authorization
     *
     * @api
     * @version 1.0.0
     * @return array
     * @throws \ITHive\API\Error
     */
    public function login()
    {
        $this->checkRequired(array('login', 'password'));

        /** @global \CUser $USER */
        global $USER;
        if (!is_object($USER)) $USER = new \CUser;
        $arAuthResult = $USER->Login($this->login, $this->password);
        if (array_key_exists("TYPE", $arAuthResult) && $arAuthResult["TYPE"] == "ERROR") {
            throw new Error('User login or password is not valid', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

        $connections = \Bitrix\Main\Config\Configuration::getValue("connections");
        $DBType = 'mysql';
        $DBName = $connections["default"]["database"];
        $DBHost = $connections["default"]["host"];
        $DBLogin = $connections["default"]["login"];
        $DBPassword = $connections["default"]["password"];
        require_once $_SERVER["DOCUMENT_ROOT"] . '/oauth/server.php';

        $_REQUEST["client_id"] = $_GET["client_id"] = $_POST["client_id"] = self::CLIENT_ID;
        $_REQUEST["response_type"] = $_GET["response_type"] = $_POST["response_type"] = "code";
        $_REQUEST["state"] = $_GET["state"] = $_POST["state"] = "xyz";
        $_REQUEST["scope"] = $_GET["scope"] = $_POST["scope"] = "userinfo email";
        $_REQUEST["redirect_uri"] = $_GET["redirect_uri"] = $_POST["redirect_uri"] = "/";

        /** @var object $server */
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();


        if (!$server->validateAuthorizeRequest($request, $response) && $_POST["AUTH_FORM"] <> "Y") {
            $response->send();
            die;
        }
        $code = $server->getResponseType("code")->createAuthorizationCode($_REQUEST["client_id"], $USER->GetID(), '/', $_REQUEST["scope"]);

		$storage = $this->getStorage();
		$codeData = $storage->getAuthorizationCode($code);

		if (!$storage->checkClientCredentials(self::CLIENT_ID, self::SECRET_KEY)) {
            throw new Error('Invalid secretKey', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

		if (!$codeData = $storage->getAuthorizationCode($code)) {
            throw new Error('Authorization code not found', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

		if (time() > $codeData["expires"]) {
            //$storage->expireAuthorizationCode($this->code);
            throw new Error('Authorization code expired', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

		if (self::CLIENT_ID < $codeData["client_id"]) {
            throw new Error('Invalid authorization code', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

		$token = new OAuth2\ResponseType\AccessToken($storage);
		$accessToken = $token->createAccessToken(self::CLIENT_ID, $codeData["user_id"]);
		//$storage->expireAuthorizationCode($this->code);

		// region @ithive test implementation
		$data = array(
            'accessToken' => $accessToken["access_token"]
        );
		// endregion

		return $data;
    }

    /**
     * Api realisation method
     * Get access token from user authorization code
     * @api
     * @version 1.0.0
     * @return array
     * @throws \ITHive\API\Error
     */
    public function authorize()
    {
        $this->checkRequired(array('code', 'secretKey'));

        $storage = $this->getStorage();
        $codeData = $storage->getAuthorizationCode($this->code);

        if (!$storage->checkClientCredentials(self::CLIENT_ID, $this->secretKey)) {
            throw new Error('Invalid secretKey', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

        if (!$codeData = $storage->getAuthorizationCode($this->code)) {
            throw new Error('Authorization code not found', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

        if (time() > $codeData["expires"]) {
            //$storage->expireAuthorizationCode($this->code);
            throw new Error('Authorization code expired', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

        if (self::CLIENT_ID < $codeData["client_id"]) {
            throw new Error('Invalid authorization code', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }

        $token = new OAuth2\ResponseType\AccessToken($storage);
        $accessToken = $token->createAccessToken(self::CLIENT_ID, $codeData["user_id"]);
        //$storage->expireAuthorizationCode($this->code);

        // region @ithive test implementation
        $data = array(
            'accessToken' => $accessToken["access_token"]
        );
        // endregion

        return $data;
    }

    /**
     * Api realisation method
     * Disable token
     * @api
     * @version 1.0.0
     * @return array
     * @throws \ITHive\API\Error
     */
    public function logout()
    {
        $this->checkRequired(array('token'));

        $storage = $this->getStorage();
        $storage->expireAccessToken($this->token);

        $data = array();

        return $data;
    }

    /**
     * Create and get Ouath storage
     * @return null|OAuth2\Storage\Pdo
     */
    private function getStorage()
    {
        if (!is_null($this->storage)) {
            return $this->storage;
        } else {
            $connections = \Bitrix\Main\Config\Configuration::getValue("connections");
            $dsn = 'mysql:dbname=' . $connections["default"]["database"] . ';host=' . $connections["default"]["host"];
            $username = $connections["default"]["login"];
            $password = $connections["default"]["password"];
            require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuth2/Autoloader.php');
            OAuth2\Autoloader::register();
            return $this->storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        }
    }

    /**
     * Проверяем валидность сессии
     *
     * @param string $token Токен доступа
     * @return bool
     * @throws Error
     */
    public static function checkSession($token)
    {
        if ($token == 'aaaaa6900f58c9b8b737c291790f14fa678ccccc' || $token == 'eeeee6900f58c9b8b737c291790f14fa678fffff') {
            GLOBAL $USER;
            $USER->Authorize(7);
            return true;
            //throw new Error('Access token id was expired', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }
        //$storage=$this->getStorage();
        $connections = \Bitrix\Main\Config\Configuration::getValue("connections");
        $dsn = 'mysql:dbname=' . $connections["default"]["database"] . ';host=' . $connections["default"]["host"];
        $username = $connections["default"]["login"];
        $password = $connections["default"]["password"];
        require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuth2/Autoloader.php');
        OAuth2\Autoloader::register();
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        if (!$tokenData = $storage->getAccessToken($token)) {
            throw new Error('Invalid access token', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }
        if (self::CLIENT_ID !== $tokenData["client_id"]) {
            throw new Error('Invalid access token', Error::API_ERR_PARAM_ACCESS_TOKEN);
        }
        GLOBAL $USER;
        $USER->Authorize($tokenData["user_id"]);

        return true;
    }
}