<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== TRUE) die();
require_once "lib/EpilogHandler.php";

class AgentClassWeather
{
    public static function GetWeather()
    {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/its.weather/settings.json')) static::setCurrentCity();
        $curCity = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/local/its.weather/settings.json'), true)['city'];
        try{
            $info = file_get_contents("https://betify.pro/app/itsolutionru.weather/?url=$curCity");
            $weatherData = json_decode($info,true);
            static::cacheResult($weatherData);
        }
        catch (Exception $e)
        {

        }
        return "AgentClassWeather::GetWeather();";
    }

    public static function setCurrentCity()
    {
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/local/its.weather/settings.json',
            json_encode(["city" => "/prognoz/sankt_peterburg/"])
        );
        static::GetWeather();
    }

    public static function cacheResult($result)
    {
        $template = static::getTemplate($result);
        file_put_contents(__DIR__."/js/main.js",$template);
    }

    public static function getTemplate($result)
    {
        $dollar = '$';
        $template = "BX.ready(function(){".$dollar."('#left-menu-empty-item').after(\"";
        $template .= "<div id='weather-box' style='background: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url(".$result['background']."); background-size: cover;'><span class='city-name'>".$result['city']."</span><table cellpadding='0' cellspacing='0' width='100%' height='100%'><tr><td valign='top'><p class='flex'><img src='".static::getPortalUrl()."/local/its.weather/img/humidity.png' style='width:16px'> <span style='padding-left: 5px'>".$result['humidity']."%</span></p><p class='flex'><img src='".static::getPortalUrl()."/local/its.weather/img/pressure.png' style='width:16px'> <span style='padding-left: 5px'>".$result['pressure']." мм</span></p><p class='flex'><img src='".static::getPortalUrl()."/local/its.weather/img/wind.png' style='width:16px'> <span style='padding-left: 5px'>".$result['wind']." м/c</span></p></td><td><p class='temperature'>".$result['temperature']."°</p><p class='feels-like'>Ощущается как ".$result['feelsLike']."°</p></td></tr></table></div>";
        $template .= "\");});";
        return $template;
    }

    public static function GetByURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,6);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec ($ch);
        curl_close ($ch);
        return $result;
    }

    public static function getPortalUrl()
    {
        $url = "http://";
        if($_SERVER['HTTPS'] == "on") $url = "https://";
        return $url.$_SERVER['SERVER_NAME'];
    }

}