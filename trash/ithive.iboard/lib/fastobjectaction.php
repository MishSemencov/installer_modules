<?php
namespace ITHive\IBoard;

use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Localization\Loc;

/**
 * Class FastObjectAction
 * @package ITHive\IBoard
 *
 * Класс обрабатывает запрос на создание сущностей быстрых объектов. Имеет встроенную валидацию.
 * Пример вызова ithive.iboard/ajax/fastobject/action.php
 *
 */

class FastObjectAction
{
    public $paramErrors = [];
    protected $moduleId;
    public $status = false;
    public $resultData = [];

    public function __construct($action, $data)
    {
        $this->paramErrors = [];

        $this->moduleId = GetModuleID(__FILE__);

        switch($action){
            case 'mail':
                $this->createMail($data);
                break;
            case 'task':
                $this->createTask($data);
                break;
            case 'chat':
                $this->createChatMessage($data);
                break;
            case 'livefeed':
                $this->createLFMessage($data);
                break;
            case 'event':
                $this->createEvent($data);
                break;
            default:
                break;
        }
    }

    private function setValidateError($param)
    {
        $this->paramErrors[] = $param;
    }

    public function getValidateErrors()
    {
        if(count($this->paramErrors)>0)
            return $this->paramErrors;
        else
            return false;
    }

    private function validateParams($requiredParams, $params)
    {
        foreach($requiredParams as $paramCode){
            if(empty($params[$paramCode]))
                $this->setValidateError($paramCode);
        }
    }

    private function unsetValidateParam($field, $params)
    {
        if (($key = array_search($field, $params)) !== false) {
            unset($params[$key]);
        }
        return $params;
    }

    /**
     * Создаёт письмо
     *
     * @param $data
     */
    private function createMail($data)
    {
        $requiredParams = ['email_from', 'email_to', 'subject', 'message'];
        $this->validateParams($requiredParams, $data);
        if(!$this->getValidateErrors())
        {
            Loader::includeModule('main');
            $eventName = \COption::GetOptionString($this->moduleId, 'fo_mail_type_name');
            $messId = \COption::GetOptionInt($this->moduleId, 'fo_mail_mess_id');

            if(!empty($eventName) && !empty($messId)){

                global $USER;
                $arUser = \CUser::GetByID($USER->GetID())->Fetch();
                if(!empty($arUser['UF_MAIL_SIGN']))
                    $data['message'] .= '<br>--<br>'.$arUser['UF_MAIL_SIGN'];

                $textParser = new \CTextParser();
                $message = $textParser->convert4mail($data['message']);
                $res = Event::sendImmediate([
                    'EVENT_NAME' => $eventName,
                    'MESSAGE_ID' => $messId,
                    'LID' => SITE_ID,
                    'C_FIELDS' => [
                        'EMAIL_FROM' => $data['email_from'],
                        'EMAIL_TO' => $data['email_to'],
                        'SUBJECT' => $data['subject'],
                        'MESSAGE' => $message,
                    ]
                ]);

                if($res === 'Y') {
                    if ($data["hidden_idea_mode"] == "Y") {
//                        $shortName = Ideas::getShortName($data["hidden_idea_id"]);
                        $ideaId = $data["hidden_idea_id"];
                        $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
                        $historyData = Loc::Getmessage("IDEA_MAIL_SENDED", array("#IDEA_ID#" => $data["hidden_idea_id"], "#IDEA_NAME#" => $shortName, "#MAIL_TO#" => $data['email_to']));
                        History::add($historyData, $USER->GetId(), $data["hidden_idea_id"], false, "mail");
                    }
                    $this->status = true;
                }

            }else{
                $this->status = false;
            }
        }

    }

    /**
     * Создаёт задачу
     *
     * @param $data
     */
    private function createTask($data)
    {
        $requiredParams = ['title', 'description', 'responsible_id'];
        $this->validateParams($requiredParams, $data);
        if(!$this->getValidateErrors())
        {
            Loader::includeModule('tasks');
            Loader::includeModule('blog');
            global $USER;

            $parser = new \CTextParser();
            $data['description'] = $parser->convertText($data['description']);
            $data['description'] = str_replace('amp;', '', $data['description']);

            $arFields = [
                'CREATED_BY' => $USER->GetID(),
                'TITLE' => $data['title'],
                'DESCRIPTION' => $data['description'],
                'RESPONSIBLE_ID' => $data['responsible_id'],
            ];
            if(!empty($data['group_id']))
                $arFields['GROUP_ID'] = $data['group_id'];
            if(!empty($data['group_id']))
                $arFields['DEADLINE'] = $data['deadline'];
            if(!empty($data['priority']))
                $arFields['PRIORITY'] = $data['priority'];
            if(!empty($data['task_control']))
                $arFields['TASK_CONTROL'] = 'Y';
            $obTask = new \CTasks;
            $taskId = $obTask->Add($arFields);
            if($taskId>0){
                $taskUrl = '/company/personal/user/'.$USER->GetId().'/tasks/task/view/'.$taskId.'/';
                $this->status = true;
                $this->resultData = [
                    'type' => 'task',
                    'data' => ['url' => $taskUrl]
                ];
                if ($data["hidden_idea_mode"] == "Y") {
                    History::add($taskUrl, $USER->GetId(), $data["hidden_idea_id"], false, "task");
                }
            }

        }else{
            $this->status = false;
        }
    }

    /**
     * Создаёт сообщение в чат
     *
     * @param $data
     */
    private function createChatMessage($data)
    {
        $requiredParams = ['description', 'recipient_id', 'chat_id'];

        if(!empty($data['recipient_id'])){
            $requiredParams = $this->unsetValidateParam('chat_id', $requiredParams);
        }elseif(!empty($data['chat_id'])){
            $requiredParams = $this->unsetValidateParam('recipient_id', $requiredParams);
        }
        $this->validateParams($requiredParams, $data);


        if(!$this->getValidateErrors())
        {
            Loader::includeModule('im');
            global $USER;

            if(!empty($data['recipient_id']))
            {
                $messId = \CIMMessage::Add([
                    'FROM_USER_ID' => $USER->GetID(),
                    'TO_USER_ID' => intval($data['recipient_id']),
                    'MESSAGE' => $data['description'],
                ]);
            }

            if(!empty($data['chat_id']))
            {
                $messId = \CIMChat::AddMessage([
                    'FROM_USER_ID' => $USER->GetID(),
                    'TO_CHAT_ID' => intval($data['chat_id']),
                    'MESSAGE' => $data['description'],
                ]);
            }

            if($messId) {
                if ($data["hidden_idea_mode"] == "Y") {
                    $arUser = \CUser::GetById(intval($data['recipient_id']))->fetch();
//                    $shortName = Ideas::getShortName($data["hidden_idea_id"]);
                    $ideaId = $data["hidden_idea_id"];
                    $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
                    $historyData = Loc::Getmessage("IDEA_CHAT_MSG_SENDED", array("#IDEA_ID#" => $data["hidden_idea_id"], "#IDEA_NAME#" => $shortName, "#CHAT#" => $arUser["NAME"] . " " . $arUser["LAST_NAME"]));
                    History::add($historyData, $USER->GetId(), $data["hidden_idea_id"], false, "chat");
                }
                $this->status = true;
            }
        }
    }

    /**
     * Создаёт запись в живой ленте
     *
     * @param $data
     */
    private function createLFMessage($data)
    {
        $requiredParams = ['description', 'SPERM'];
        $this->validateParams($requiredParams, $data);
        if(!$this->getValidateErrors())
        {
            Loader::includeModule('blog');
            Loader::includeModule('socialnetwork');
            global $USER, $APPLICATION;

            $recipients = [];
            foreach($data['SPERM'] as $type => $arVal){
                foreach($arVal as $recipient){
                    $recipients[] = $recipient;
                }
            }

            $recipients = array_unique($recipients);

            $userId = $USER->GetID();
            $arBlog = \CBlog::GetByOwnerID($userId);

            if(empty($arBlog['ID'])){
                \CBlog::Add([
                    "NAME" => 'Блог пользователя '.$USER->GetFullName(),
                    "GROUP_ID" => '1',
                    "ENABLE_IMG_VERIF" => 'Y',
                    "EMAIL_NOTIFY" => 'Y',
                    "ENABLE_RSS" => "Y",
                    "USE_SOCNET" => "Y",
                    "URL" => "admin-blog",
                    "ACTIVE" => "Y",
                    "OWNER_ID" => $USER->GetID()
                ]);
                $arBlog = \CBlog::GetByOwnerID($userId);
            }

            $arFields = array(
                "TITLE" => ' ',
                "DETAIL_TEXT" => $data['description'],
                "DETAIL_TEXT_TYPE" => 'text',
                "DATE_PUBLISH" => (new \DateTime())->format('d.m.Y H:m:s'),
                "PUBLISH_STATUS" => 'P',
                "CATEGORY_ID" => '',
                "PATH" => '/company/personal/user/'.$userId.'/blog/#post_id#/',
                "URL" => 'u'.$userId.'-blog-s1',
                "PERMS_POST" => array(),
                "PERMS_COMMENT" => array(),
                "MICRO" => 'N',
                "SOCNET_RIGHTS" => $recipients,
                "=DATE_CREATE" => 'now()',
                "AUTHOR_ID" => $userId,
                "BLOG_ID" => $arBlog['ID'],

            );
            $CBlogPost = new \CBlogPost();
            $messId = $CBlogPost->Add($arFields);
            if($messId>0){
                $arFields["ID"] = $messId;
                $arParamsNotify = array(
                    "bSoNet"=>true,
                    'UserID'=>$userId,
                    'user_id'=>$userId,
                    //'SOCNET_GROUP_ID'=>$groupId,
                    'PATH_TO_POST'=>'/company/personal/user/#user_id#/blog/#post_id#/'
                );
                $notify = $CBlogPost->Notify($arFields, $arBlog, $arParamsNotify);


                if ($data["hidden_idea_mode"] == "Y") {
//                    $shortName = Ideas::getShortName($data["hidden_idea_id"]);
                    $ideaId = $data["hidden_idea_id"];
                    $shortName = Ideas::getList(array("id" => $ideaId), array("name"))[$ideaId]["NAME"];
                    $historyData = Loc::Getmessage("IDEA_LIVE_FEED_CREATED", array("#IDEA_ID#" => $data["hidden_idea_id"], "#IDEA_NAME#" => $shortName));
                    History::add($historyData, $USER->GetId(), $data["hidden_idea_id"], false, "lfeed");
                }

                $this->status = true;
            }else{
                if ($ex = $APPLICATION->GetException())
                    echo $ex->GetString();
                    die();
            }

        }
    }
    /**
     * Создаёт событие в календаре
     *
     * @param $data
     */
    private function createEvent($data)
    {
        $requiredParams = ['title', 'description', 'date_from', 'date_to'];
        $this->validateParams($requiredParams, $data);
        if(!$this->getValidateErrors())
        {
            Loader::includeModule('calendar');
            global $USER;

            $fromTs = \CCalendar::Timestamp($data['date_from']);
            $toTs = \CCalendar::Timestamp($data['date_to']);


            $arFields = [
                'OWNER_ID' => $USER->GetID(),
                'CREATED_BY' => $USER->GetID(),
                'CAL_TYPE' => 'user',
                'ACTIVE' => 'Y',
                'NAME' => $data['title'],
                'DESCRIPTION' => $data['description'],
                'DATE_FROM' => \CCalendar::Date($fromTs),
                'DATE_TO' => \CCalendar::Date($toTs),
            ];

            if(!empty($data['remind_val']) && !empty($data['remind_type']))
            $arFields["REMIND"][] = [
                "type" => intval($data['remind_type']),
                "count" => $data['remind_val']
            ];

            if (!$arFields['SKIP_TIME'])
            {
                $tzName = \CCalendar::GetUserTimezoneName($arFields["OWNER_ID"]);
                if(!$tzName)
                    $tzName = \CCalendar::GetGoodTimezoneForOffset(\CCalendar::GetCurrentOffsetUTC($arFields["OWNER_ID"]));
                $arFields["TZ_FROM"] = $arFields["TZ_TO"] = $tzName;
            }

            if(!empty($data['members'])){
                $arFields["IS_MEETING"] = true;
                $arFields["MEETING"]["TEXT"] = '';
                $arFields["MEETING"]["OPEN"] = true;
                $arFields["MEETING"]["NOTIFY"] = true;
                $arFields["MEETING"]["REINVITE"] = false;
                $arFields["MEETING_HOST"] = $USER->GetId();

                foreach($data['members'] as $userId => $arUser){
                    $arAccessCodes[] = 'U'.$userId;
                }
                if(!empty($arAccessCodes)){
                    $arAccessCodes = array_unique($arAccessCodes);
                    $arFields["ATTENDEES_CODES"] = $arAccessCodes;
                    $arFields["ATTENDEES"] = \CCalendar::GetDestinationUsers($arAccessCodes);
                }

            }

            $eventId = \CCalendar::SaveEvent(
                array(
                    'arFields' => $arFields,
                    'autoDetectSection' => true
                )
            );
            if($eventId>0){
                $this->status = true;
                $eventUrl = '/company/personal/user/'.$USER->GetId().'/calendar/?EVENT_ID='.$eventId;
                $this->resultData = [
                    'type' => 'event',
                    'data' => ['url' => $eventUrl]
                ];

                if ($data["hidden_idea_mode"] == "Y") {
                    History::add($eventUrl, $USER->GetId(), $data["hidden_idea_id"], false, "event");
                }
            }

        }
        else
        {
            $this->status = false;
        }
    }
}

?>