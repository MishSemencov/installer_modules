<?php
namespace ITHive\SJTickets;

use Bitrix\Main\Diag\Debug;

class Handler
{
	/**
     * Слушатель одноименного события, проверяет поставили ли ответсвенным пользователя SJ, и в положительном случае отправляет тикет в SJ
     *
     * @param $arFields
     * @return mixed
     */
    public function OnAfterTicketAdd($arFields)
    {
        $CTicket = new \CTicket();
        $sjUserId = SJOptions::getInstance()->getSupportSJUser();

        $arFields = $CTicket->GetList($by = 'id', $order = 'asc', array('ID' => $arFields['ID']), $is_filtered, $CHECK_RIGHTS = "N")->Fetch();

        if(!empty($arFields['RESPONSIBLE_USER_ID']) && intval($arFields['RESPONSIBLE_USER_ID']) == $sjUserId){
            $arMessages = [];
            $resMess = $CTicket->GetMessageList($by = 'id', $order = 'asc', ['TICKET_ID' => $arFields['ID'], 'IS_MESSAGE' => 'Y'], $is_filtered, 'N');
            while($arrMess = $resMess->Fetch()){
                $arMessages[] = self::makeMessageArray($arFields['ID'], $arrMess['ID'], $arrMess);
            }
            $data = [
                'id' => $arFields['ID'],
                'status' => $arFields['STATUS_ID'],
                'title' => $arFields['TITLE'],
                'messages' => $arMessages,
            ];
            SJRequest::getInstance()->postTicket($data);
        }
    }
    /**
     * Слушатель одноименного события, проверяет поставили ли ответсвенным пользователя SJ, и в положительном случае отправляет тикет в SJ
     *
     * @param $arFields
     * @return mixed
     */
    public function OnBeforeTicketUpdate($arFields)
    {
        Debug::dumpToFile(date('d.m.Y'), 'date', 'data.txt');
        Debug::dumpToFile($arFields, 'arFields2', 'data.txt');
        if($arFields['ID'])
        {
            $CTicket = new \CTicket();
            $sjUserId = SJOptions::getInstance()->getSupportSJUser();
            $SJRequest = SJRequest::getInstance();
            global $USER, $APPLICATION;
            $thisUserId = $USER->GetID();
            //проверяем, был ли ответственный поменян с обычного пользователя тех поддержки на пользователя SJ
            $arCurrTicket = $CTicket->GetList($by = 'id', $order = 'asc', array('ID' => $arFields['ID']), $is_filtered, $CHECK_RIGHTS = "N")->Fetch();


            if($arFields['CLOSE'] == 'Y' && (!empty($arFields['RESPONSIBLE_USER_ID']) && intval($arFields['RESPONSIBLE_USER_ID']) == $sjUserId || intval($arCurrTicket['RESPONSIBLE_USER_ID']) == $sjUserId)){
                $data = [
                    'ticketId' => $arFields['ID']
                ];
                $SJRequest->closeTicket($data);
            }
            else if(!empty($arFields['RESPONSIBLE_USER_ID']) && intval($arFields['RESPONSIBLE_USER_ID']) == $sjUserId && $arCurrTicket['RESPONSIBLE_USER_ID'] != $sjUserId)
            {
                //проверка: кто меняет тикет, тех поддержка или юзер
                if($CTicket->IsSupportTeam($thisUserId) || $CTicket->IsAdmin($thisUserId))
                {
                    $arMessages = [];
                    $resMess = $CTicket->GetMessageList($by = 'id', $order = 'asc', ['TICKET_ID' => $arFields['ID'], 'IS_MESSAGE' => 'Y'], $is_filtered, 'N');
                    while($arrMess = $resMess->Fetch()){
                        $arMessages[] = self::makeMessageArray($arFields['ID'], $arrMess['ID'], $arrMess);
                    }
                    $data = [
                        'id' => $arFields['ID'],
                        'status' => $arFields['STATUS_ID'],
                        'title' => $arFields['TITLE'],
                        'messages' => $arMessages,
                    ];
		    Debug::dumpToFile(date('d.m.Y'), 'date', 'data.txt');
	            Debug::dumpToFile($data, 'data2', 'data.txt');
                    $SJRequest->postTicket($data);
                }
            } else {
                    $arMessages = [];
                    $resMess = $CTicket->GetMessageList($by = 'id', $order = 'asc', ['TICKET_ID' => $arFields['ID'], 'IS_MESSAGE' => 'Y'], $is_filtered, 'N');
                    while($arrMess = $resMess->Fetch()){
                        $arMessages[] = self::makeMessageArray($arFields['ID'], $arrMess['ID'], $arrMess);
                    }
                    $data = [
                        'id' => $arFields['ID'],
                        'status' => $arFields['STATUS_ID'],
                        'title' => $arFields['TITLE'],
                        'messages' => $arMessages,
                    ];
		    Debug::dumpToFile(date('d.m.Y'), 'date', 'data.txt');
	            Debug::dumpToFile($data, 'dataNEW', 'data.txt');
	    }
        }
        return $arFields;
    }

    /**
     * Слушатель одноименного события, проверяет было ли добавлено сообщение в тикет, и если да, то отправляет сообщение в SJ
     * @param $arFields
     */
    public function OnAfterTicketUpdate($arFields)
    {
	Debug::dumpToFile(date('d.m.Y'), 'date', 'data.txt');
        Debug::dumpToFile($arFields, 'arFields3', 'data.txt');

        $SJRequest = SJRequest::getInstance();
        $sjUserId = SJOptions::getInstance()->getSupportSJUser();

        //Определение ответственного
        if(empty($arFields['RESPONSIBLE_USER_ID'])){
            $CTicket = new \CTicket();
            $arTicket = $CTicket->GetByID($arFields['ID'], 'ru', 'N')->Fetch();
            $responsibleUserId = intval($arTicket['RESPONSIBLE_USER_ID']);
        }else{
            $responsibleUserId = intval($arFields['RESPONSIBLE_USER_ID']);
        }
	
        //если было отправлено сообщение в тикет
        if($arFields['MID'] && $arFields['MESSAGE'] && ($arFields['HIDDEN'] != 'Y') && ($responsibleUserId == $sjUserId)) {
            $data = self::makeMessageArray($arFields['ID'], $arFields['MID'], $arFields);
	    if (strlen(trim($data["author"])) > 0) {
	        $SJRequest->postComment($data);
	    } else {
		Debug::dumpToFile(date('d.m.Y'), 'date', 'data.txt');
		Debug::dumpToFile($data, 'data3', 'data.txt');
	    }
        }
    }

    /**
     * Метод готовит данные типа TicketComment
     *
     * @param $id - id сообщения
     * @param $arFields - массив полей сообщения
     * @return array
     */
    private static function makeMessageArray($ticketId, $id, $arFields)
    {
        $CTicket = new \CTicket();
        global $USER;

        $thisUserId = $USER->GetID();
        $author = !empty($arFields['CREATED_NAME'])?$arFields['CREATED_NAME']:$USER->GetFullName();

        $files = [];
        $resFiles = $CTicket->GetFileList($by = 'id', $order = 'asc', ['MESSAGE_ID' => $id], 'N');
        while($arrFile = $resFiles->Fetch()){
            $files[] = 'https://'.\COption::GetOptionString('main', 'server_name').\CFile::GetPath($arrFile['ID']);
        }

        $date = $arFields['DATE_CREATE']?$arFields['DATE_CREATE']:date('d.m.Y H:i:s');

        $data = [
            'id' => $id,
            'date' => MakeTimeStamp($date, "DD.MM.YYYY HH:MI:SS"),
            'ticketId' => $ticketId,
            'text' => str_replace(array("\r\n", "\r", "\n"), '<br>',  $arFields['MESSAGE']),
            'files' => $files,
            'author' => $author,
            'support' => ($CTicket->IsSupportTeam($thisUserId) || $CTicket->IsAdmin($thisUserId))?true:false
        ];
	Debug::dumpToFile(date('d.m.Y'), 'date', 'data.txt');
	Debug::dumpToFile($data, 'dataM', 'data.txt');
        return $data;
    }
}