<?php

namespace App\Util;

class Account
{
    /**
     * 生成hash密码
     * @param $password
     * @return bool|string
     */
    public static function makePassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

}
