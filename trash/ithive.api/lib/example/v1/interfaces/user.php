<?php
/**
 * Api interface User.
 *
 * @package ITHive\API\TakeHeli\V1\Interfaces
 * @author Dmitriy Gertsen <web.mackacmexa@gmail.com>
 * @copyright 2003-2016 IT-Hive
 */
namespace ITHive\API\Example\V1\Interfaces;

/**
 * Interface User
 * @package ITHive\API\TakeHeli\V1\Interfaces
 */
interface User
{
    /**
     * API Метод позволяет получить персональные данные пользователя.
     *
     * @api {POST} /user/profile/{get} 2.1 Получение профиля
     * @apiSampleRequest /api/takeheli/v1/user/profile/
     * @apiVersion 1.0.1
     * @apiGroup User
     * @apiName getProfile
     * @apiDescription API Метод позволяет получить персональные данные пользователя.
     * @apiParam {String} token Токен доступа
     * @apiSuccess {User} user Информация о пользователе
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *   "user" : {
     *     "id": 1,
     *     "firstName": null,
     *     "lastName": null,
     *     "email": null,
     *     "phone": "+7000448877"
     *   }
     * }
     */
    public function getProfile();

    /**
     * API Метод позволяет изменить персональные данные пользователя.
     *
     * @api {POST} /user/profile/set/ 2.2 Обновление профиля
     * @apiSampleRequest /api/takeheli/v1/user/profile/set/
     * @apiVersion 1.0.1
     * @apiGroup User
     * @apiName setProfile
     * @apiDescription API Метод позволяет изменить персональные данные пользователя.
     * @apiParam {String} token Токен доступа
     * @apiParam {User} userInfo Информация о пользователе
     */
    public function setProfile();
}