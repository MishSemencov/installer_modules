<?php
namespace ITHive\SJTickets\API\V1;

use ITHive\API\Response;
use ITHive\SJTickets\SJOptions;

class Token
{
    /**
     * Метод проверки токена
     *
     * @param $verifiableToken
     */
    public static function check($verifiableToken)
    {
        $appToken = SJOptions::getInstance()->getApiToken();
        if($appToken !== $verifiableToken){
            $res = new Response(['status' => 'error', 'description' => 'token checking error']);
            $res->respond();
        }
    }
}
