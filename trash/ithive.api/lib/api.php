<?php
/**
 * Api base class.
 *
 * @package ITHive\API
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */

namespace ITHive\API;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

/**
 * Api class which contain all info about how api work
 * @package ITHive\API
 */
class Api
{
    /** @var Logger */
    private $logger;

    /** @const file name to log requests. Set false to not log. */
    const LOG_TO_FILE = 'api_request.log';
    const OPT_INCLUDED_MODULES = 'include_modules';

    /** @var string Api namespace */
    public $namespace;

    /** @var string Application (takejet, takeheli... etc) */
    public $app;
    /** @var string Api version (v1, v2, v... etc) */
    public $version;
    /** @var string Entity (user, auth, session... etc) */
    public $entity;
    /** @var string Action (get, getList, calc, bomb... etc) */
    public $action;

    /** @var string Api default method data receive */
    private $method = 'post';
    /** @var \ITHive\API\Request Request object*/
    private $request;
    /** @var \ITHive\API\Response Response object*/
    private $response;

    /** @var string Data resend container (get, post, stream(json)... etc) */
    private $dataInput = 'stream';

    /** @var array Route map */
    private $map = array(
        'app' => 'app',
        'version' => 'version',
        'entity' => 'entity',
        'action' => 'action',
    );


    /**
     * Api constructor.
     */
    public function __construct($namespace)
    {
        self::log('=========== NEW REQUEST at '. (new \DateTime())->format('d-m-Y H:i:s') . ' ===============');
        self::log($_SERVER['REQUEST_URI'], 'request uri');

        $this->namespace = $namespace;

        try {
            $this->autoloadLinkedModules();
            $this->Init();
            $this->setDataInput();
            $this->setApiNamespace();
            $this->setRequest();

        }
        catch(Error $e){
            $e->showErrorXML();
        }
        catch(\Exception $e){
            (new Error($e->getMessage(), Error::API_ERR_UNKNOWN))->showErrorXML();
        }
    }

    /**
     * Set up routing props into object
     * @throws Error
     */
    private function Init(){
        // filter and fill path routing from $_GET
        foreach ($_GET as $name => $v) {
            $name = filter_var($name);
            if (strlen($name) > 0) {
                $v = filter_var($v);
                if (!empty($v)) {
                    $this->$name = $v;
                }
            }
        }

        foreach ($this->map as $name => $part) {
            if (!property_exists($this, $part)) {
                throw new Error('Not specified ' . $name, constant(__NAMESPACE__ . '\\Error::API_ERR_' . strtoupper($name)));
            }

        }
    }

    /**
     * Get data input type
     * @throws Error
     */
    private function setDataInput()
    {
        $this->dataInput = count($_POST) > 0 ? 'post' : 'stream';
    }

    /**
     * Set selected api Request object
     * @throws Error
     */
    private function setRequest()
    {
        $reqVerNamespace = $this->namespace;
        $reqEntityInterface = $reqVerNamespace . '\\Interfaces\\' . $this->entity;
        $reqEntityClass = $reqVerNamespace . '\\' . $this->entity;

        if (!interface_exists($reqEntityInterface)) {
            throw new Error('Request interface at "' . $reqEntityInterface . '" for entity "' . $this->entity . '" for api  "' . $this->app . '" version "' . $this->version . '"" not found.', Error::API_ERR_ENTITY);
        }
        if (!class_exists($reqEntityClass)) {
            throw new Error('Request class "' . $this->entity . '" for api  "' . $this->app . '" version "' . $this->version . '"" not found.', Error::API_ERR_ENTITY);
        }

        $this->request = new $reqEntityClass($this->action, $this->dataInput);
    }

    /**
     * Set selected api Respond object
     * @throws Error
     */
    private function setResponse()
    {
//        $responseClassName = __NAMESPACE__ . '\\' . $this->app . '\\' . $this->version . '\\Response';
//        if (!class_exists($responseClassName)) {
//            throw new Error('Response api "' . $this->app . '" version "' . $this->version . '"" not found.', Error::API_ERR_APP);
//        }

//        $this->response = new Response();
    }

    /**
     * Show api respond
     */
    public function request()
    {
        try{
            $this->response = new Response($this->request->call());
        } catch (Error $e) {
            $e->showErrorXML();
        }
    }

    /**
     * Call api respond
     */
    public function respond()
    {
        $this->response->respond();
//        (new Response($this->response))->respond();
    }

    /**
     * Log api requests
     * @param $var
     * @param $title
     * @param $path
     */
    public static function log($var, $title = '', $path = self::LOG_TO_FILE){
        if(self::LOG_TO_FILE !== false){
            Debug::writeToFile($var, $title, $path);
        }

    }

    private function setApiNamespace()
    {
        if(!empty($this->namespace))
            return true;

        if($this->app == 'example'){
            $this->namespace = __NAMESPACE__ . '\\Example\\' . $this->version;
        }
        elseif($namespace = Option::get('ithive.api', 'api_' . $this->app . '_' . $this->version, null)){
            $this->namespace = $namespace;
        }
        else {
            $this->namespace = __NAMESPACE__ . '\\' . $this->app . '\\' . $this->version;
        }
    }

    /**
     * @param string $app Api symbolic name
     * @param string $version Api version
     * @param $namespace
     * @param null|string $module Module id
     * @return bool
     */
    public static function add($app, $version, $namespace, $module = null){
        global $USER;
        if(!$USER->IsAdmin()) return false;

        $name = 'api_' . $app . '_' . $version;

        Option::set('ithive.api', $name, $namespace);

        if($module){
            self::addLinkedModuleAutoload($module);
        }
        return true;
    }

    /**
     * @param string $app Api symbolic name
     * @param string $version Api version
     * @param null|string $module Module id
     * @return bool
     */
    public static function delete($app, $version, $module = null)
    {
        global $USER;
        if (!$USER->IsAdmin()) return false;

        $name = 'api_' . $app . '_' . $version;

        Option::delete('ithive.api', ['name' => $name]);

        if ($module) {
            self::deleteLinkedModuleAutoload($module);
        }

        return true;
    }

    private static function addLinkedModuleAutoload($id)
    {
        $arIncludedModules = Option::get('ithive.api', self::OPT_INCLUDED_MODULES, '');
        $arModules = explode(',', $arIncludedModules);

        $arModules[] = $id;

        Option::set('ithive.api', 'includedModule', $arModules);
    }

    private static function deleteLinkedModuleAutoload($id)
    {
        $arIncludedModules = Option::get('ithive.api', self::OPT_INCLUDED_MODULES, '');
        $arModules = explode(',', $arIncludedModules);

        if($key = array_search($id, $arIncludedModules)){
            unset($arModules[$key]);
        }

        Option::set('ithive.api', 'includedModule', $arModules);
    }

    private function autoloadLinkedModules(){
        $sInclude = Option::get('ithive.api', self::OPT_INCLUDED_MODULES);
        $arInclude = explode(',', $sInclude);
        foreach ($arInclude as $moduleId){
            if(empty($moduleId))
                continue;
            if(!ModuleManager::isModuleInstalled($moduleId)){
                throw new Error('Internal error! Required module was not installed.', Error::API_ERR_APP);
            };
            Loader::includeModule($moduleId);
        }
    }
}