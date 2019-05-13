<?php
namespace ITHive\SJTickets\API\V1;

use ITHive\API\Request;
use Bitrix\Main\Loader;
use ITHive\SJTickets\SJOptions;
use Bitrix\Main\Diag\Debug;

/**
 * Класс для работы с комментариями тикетов стандратного модуля тех поддержки
 *
 * Class Comment
 * @package ITHive\SJTickets\API\V1
 */
class Comment extends Request implements Interfaces\Comment
{
    /**
     * Создаёт новый комментарий к тикету
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \ITHive\API\Error
     */
    public function add()
    {
        $this->checkRequired(['token', 'ticketComment']);
        Token::check($this->token);

        Loader::includeModule('support');
        Loader::includeModule('main');

        if(empty($this->ticketComment['ticketId']) || empty($this->ticketComment['text'])){
            $data = ['status' => 'error', 'description' => 'ticketComment: no required parameters'];
        }else{

            //ToDO check ticketId for existence ;)

            $SJOptions = SJOptions::getInstance();
            $arFields = [
                'MESSAGE_AUTHOR_USER_ID' => $SJOptions->getSupportSJUser(),
                'MESSAGE_SOURCE_ID' => $SJOptions->getTicketMessSourceId(),
                'MESSAGE' => $this->ticketComment['text'],
                'NOT_CHANGE_STATUS' => 'Y'
            ];

            //make files array
            if(!empty($this->ticketComment['files']) && count($this->ticketComment['files'])>0){
                foreach($this->ticketComment['files'] as $fileUrl){
                    $arFile = \CFile::MakeFileArray($fileUrl);
                    if(!empty($arFile))
                        $arFields['FILES'][] = $arFile;
                }
            }

//            $ticket = new \CTicket();
//            $messId = $ticket->AddMessage(intval($this->ticketComment['ticketId']), $arFields, $arr = [], 'N');
	    Debug::dumpToFile(date('d.m.Y'), 'date', 'lib.api.txt');
            Debug::dumpToFile($arFields, 'data', 'lib.api.txt');
            $messId = \CTicket::SetTicket($arFields, intval($this->ticketComment['ticketId']), "N", "N", "N");

            if($messId > 0)
                $data = ['status' => 'ok'];
            else
                $data = ['status' => 'error', 'description' => 'ticket comment add error'];
        }
        return $data;
    }
}
