<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();
require_once "lib/EpilogHandler.php";

class AgentClassCurrency
{
    public static function getDateFormatted($dateInput)
    {
        $date = date("j",strtotime($dateInput))." ";
        switch (date("m",strtotime($dateInput)))
        {
            case 1:
                $date .= "января";
                break;
            case 2:
                $date .= "февраля";
                break;
            case 3:
                $date .= "марта";
                break;
            case 4:
                $date .= "апреля";
                break;
            case 5:
                $date .= "мая";
                break;
            case 6:
                $date .= "июня";
                break;
            case 7:
                $date .= "июля";
                break;
            case 8:
                $date .= "августа";
                break;
            case 9:
                $date .= "сентября";
                break;
            case 10:
                $date .= "октября";
                break;
            case 11:
                $date .= "ноября";
                break;
            case 12:
                $date .= "декабря";
                break;
        }
        return $date;
    }

    public static function GetCurrency()
    {
        $info = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp");
        if($info == false) return "AgentClassCurrency::GetCurrency();";
        $xml = simplexml_load_string($info);
        $date = $xml -> attributes() -> Date;
        $todayCurrencies = static::parsePage($xml);

        $info = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp?date_req=".date("d/m/Y",strtotime($date) - 60*60*24));
        $xml = simplexml_load_string($info);
        $yesterdayCurrencies = static::parsePage($xml);
        $changes = [
            $todayCurrencies[0] - $yesterdayCurrencies[0],
            $todayCurrencies[1] - $yesterdayCurrencies[1]
        ];
        static::cacheResult([
            "date" => static::getDateFormatted($date),
            "dollar" => $todayCurrencies[0],
            "euro" => $todayCurrencies[1],
            "dollarChanged" => [$changes[0] >= 0 ? "up" : "down",round($changes[0],2)],
            "euroChanged" => [$changes[1] >= 0 ? "up" : "down",round($changes[1],2)],
        ]);
        return "AgentClassCurrency::GetCurrency();";
    }

    public static function cacheResult($result)
    {
        $template = static::getTemplate($result);
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/local/its.currency/main.js",$template);
    }

    public static function getTemplate($result)
    {
        $dollar = '$';
        $template = "BX.ready(function(){".$dollar."('#left-menu-empty-item').after(\"";
        $template .= "<div id='currency-box' style='background: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url(".self::getPortalUrl()."/local/its.currency/currency.jpg); background-size: cover;'><span class='city-name'>Котировки на ".$result['date']."</span><table cellpadding='0' cellspacing='0' width='100%' height='100%'><tr><td valign='top'><p class='flex'><span class='currency-icon'>$</span><span style='padding-left: 5px'>".$result['dollar']." руб.</span><span class='currency-".$result['dollarChanged'][0]."'>".$result['dollarChanged'][1]." руб.</span></p><p class='flex'><span class='currency-icon'>€</span><span style='padding-left: 5px'>".$result['euro']." руб.</span><span class='currency-".$result['euroChanged'][0]."'>".$result['euroChanged'][1]." руб.</span></p></td></tr></table></div>";
        $template .= "\");});";
        return $template;
    }

    public static function getPortalUrl()
    {
        $url = "http://";
        if($_SERVER['HTTPS'] == "on") $url = "https://";
        return $url.$_SERVER['SERVER_NAME'];
    }

    public static function parsePage($xml)
    {
        $valutes = [0,0];
        foreach ($xml -> Valute as $valute)
        {
            switch($valute -> CharCode)
            {
                case "USD":
                    $valutes[0] = round(floatval(str_replace(",",".",$valute -> Value)),2);
                    break;
                case "EUR":
                    $valutes[1] = round(floatval(str_replace(",",".",$valute -> Value)),2);
                    break;
            }
        }
        return $valutes;
    }

}