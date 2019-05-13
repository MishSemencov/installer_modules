<?php
/**
 * Api interface Session.
 *
 * @package RusVipAvia\TakeJet\API\V1\Interfaces
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API\Example\V1\Interfaces;

/**
 * Interface Session
 * @package ITHive\API\Example\V1\Interfaces
 */
interface Session
{
    /**
     * API метод позволяет по коду подтверждения получить Токен доступа
     *
     * @api {POST} /session/login/ 1 Получение токена доступа
     * @apiSampleRequest /api/takejet/v1/session/login/
     * @apiVersion 1.0.0
     * @apiGroup Session
     * @apiName login
     * @apiDescription API метод позволяет данным авторизации получить Токен доступа
     *
     * @apiParam {String} login Логин
     * @apiSuccess {String} password Пароль
     * @apiSuccessExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *   accessToken: "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
     * }
     */
    public function login();

    /**
     * API метод позволяет по коду подтверждения получить Токен доступа
     *
     * @api {POST} /session/login/ 1 Получение токена доступа
     * @apiSampleRequest /api/takejet/v1/session/login/
     * @apiVersion 1.0.0
     * @apiGroup Session
     * @apiName login
     * @apiDescription API метод позволяет по коду подтверждения получить Токен доступа
     *
     * @apiParam {String} code Код подтверждения
     * @apiSuccess {String} accessToken Токен доступа
     * @apiSuccessExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *   accessToken: "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
     * }
     */
    public function authorize();

    /**
     * API метод позволяет разлогиниться и произвести необходимые действия
     * При логауте сервер должен удалять device_token, полученный в методе self::pushSubscribe, для текущей сессии.
     *
     * @api {POST} /session/logout/ 3 Деактивация(удаление) токена доступа
     * @apiSampleRequest /api/takejet/v1/session/logout/
     * @apiVersion 1.0.0
     * @apiGroup Session
     * @apiName logout
     * @apiDescription API метод позволяет разлогиниться и произвести необходимые действия
     *
     * @apiParam {String} token Токен доступа
     */
    public function logout();
}