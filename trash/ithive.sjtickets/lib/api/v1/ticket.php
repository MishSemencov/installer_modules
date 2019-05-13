<?php
namespace ITHive\SJTickets\API\V1;

use Bitrix\Main\Loader;
use ITHive\API\Request;
use ITHive\API\Response;

/**
 * Класс для работы с тикетами стандартного модуля тех поддержки
 *
 * Class Ticket
 * @package ITHive\SJTickets\API\V1
 */
class Ticket extends Request implements Interfaces\Ticket
{
    /**
     * Возвращает список статусов тикетов
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getStatuses()
    {
        $this->checkRequired(['token']);

        Token::check($this->token);

        Loader::includeModule('support');

        $data = [];

        $ticketDict = new \CTicketDictionary();
        $resStatuses = $ticketDict->GetList($by = 's_c_sort', $order = 'asc', ['TYPE' => 'S']);
        while($arStatus = $resStatuses->GetNext())
        {
            $data[] = [
                'id' => intval($arStatus['ID']),
                'title' => $arStatus['NAME']
            ];
        }

        return ['statuses' => $data];
    }

    /**
     * Утсанавливает статус
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \ITHive\API\Error
     */
    public function setStatus()
    {

        $this->checkRequired(['token', 'ticketId', 'statusId']);
        Token::check($this->token);
        $this->checkStatusId($this->statusId);

        Loader::includeModule('support');
        $ticket = new \CTicket();
        $id = $ticket->Set(['STATUS_ID' => $this->statusId], $messId = null, 268, 'N');
        if($id)
            $data = ['status' => 'ok'];
        else
            $data = ['status' => 'error', 'description' => 'update status error'];

        return $data;

    }

    /**
     * Метод проверяет id статуса на наличие в базе
     *
     * @param $statusId
     * @throws \Bitrix\Main\LoaderException
     */
    protected function checkStatusId($statusId)
    {
        Loader::includeModule('support');
        $ticketDict = new \CTicketDictionary();
        $resStatuses = $ticketDict->GetList($by = 's_c_sort', $order = 'asc', ['TYPE' => 'S', '=ID' => $statusId], $is_filtered);

        if(!$resStatuses->Fetch())
            (new Response(['status' => 'error', 'description' => 'no status with this id']))->respond();
    }

}