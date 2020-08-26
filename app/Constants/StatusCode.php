<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class StatusCode extends AbstractConstants
{
    // 500 系统类错误
    /**
     * @Message("成功")
     */
    const SUCCESS = 200;
    /**
     * @Message("未知错误")
     */
    const ERROR = 550;
    /**
     * @Message("参数错误")
     */
    const PARAM_ERROR = 551;
    /**
     * @Message("系统错误")
     */
    const INTERNAL_ERROR = 552;
    /**
     * @Message("数据库更新错误")
     */
    const DB_UPDATE_ERROR = 553;
    /**
     * @Message("%s")
     */
    const CUSTOM_ERROR = 554;

    // 600 用户类错误
    /**
     * @Message("帐号已存在")
     */
    const ACCOUNT_EXISTS = 600;
    /**
     * @Message("帐号不存在")
     */
    const ACCOUNT_NOT_EXISTS = 601;
    /**
     * @Message("用户未登录")
     */
    const USER_NOT_LOGIN = 602;
    /**
     * @Message("帐号或密码错误")
     */
    const USER_LOGIN_FAILED = 603;

    // 700 数据类错误
    /**
     * @Message("数据已存在")
     */
    const DATA_EXISTS = 700;
    /**
     * @Message("数据不存在")
     */
    const DATA_NOT_EXISTS = 701;

}
