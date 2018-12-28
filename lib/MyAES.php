<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace ank\extend;

class MyAES
{
    static $key = '';
    static $iv  = '';
    public static function encryptToken($data, $skey)
    {
        MyAES::$key = md5($skey);
        MyAES::$iv  = substr(MyAES::$key, 0, 16);
        $padding    = 16 - (strlen($data) % 16);
        $data .= str_repeat(chr($padding), $padding);
        if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, MyAES::$key, $data, MCRYPT_MODE_CBC, MyAES::$iv);
        } else {
            $data = openssl_encrypt($data, 'AES-256-CBC', MyAES::$key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, MyAES::$iv);
        }
        return base64_encode($data);
    }
    public static function decryptToken($data, $skey)
    {
        MyAES::$key = md5($skey);
        MyAES::$iv  = substr(MyAES::$key, 0, 16);
        $data       = base64_decode($data);
        if (version_compare(PHP_VERSION, '7.2.0', '<')) {
            $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, MyAES::$key, $data, MCRYPT_MODE_CBC, MyAES::$iv);
        } else {
            $data = openssl_decrypt($data, 'AES-256-CBC', MyAES::$key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, MyAES::$iv);
        }
        $padding = ord($data[strlen($data) - 1]);
        return substr($data, 0, -$padding);
    }
}

if (php_sapi_name() === 'cli') {
    $enstr = MyAES::encryptToken('www.zhaokeli.com111111', '735579768');
    echo ('PHP encrypt: ' . $enstr . "\n");
    echo ('PHP decrypt: ' . MyAES::decryptToken($enstr, '735579768')) . "\n";
    echo ('PHP decrypt: ' . MyAES::decryptToken('Tl5GYPirOOt5+lD2MHBvriBEk6LaZB0NM9vEN7vH17hLuXMhkxwfO7TZNw9d1ebyiSjrl5Ah7HdQrJSJIakyWU3HV/TkU//R0tfoyuhQqOc=', '3a208237ade')) . "\n";
}
