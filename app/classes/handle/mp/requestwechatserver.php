<?php
/**
 * Created by PhpStorm.
 * User: Ray
 * Date: 2016/12/30
 * Time: 上午12:20
 */

namespace handle\mp;


class RequestWechatServer
{
    private $api_domain = 'https://api.weixin.qq.com';

    /**
     * 获取微信服务器IP地址
     *
     * @param $access_token     微信服务器访问时需要的令牌
     * @return array
     */
    public function getWechatServerIP($access_token){
        $url = "{$this->api_domain}/cgi-bin/getcallbackip?access_token={$access_token}";
        return [];
    }

    /**
     * 拉取用户基本信息
     *
     * @param $access_token     微信服务器访问时需要的令牌
     * @param $openid           微信用户OPENID
     * @return array
     */
    public function getUserInfo($access_token, $openid){
        $url = "{$this->api_domain}/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        return [];
    }

    /**
     * 批量拉取用户基本信息,最多支持100个
     *
     * @param $access_token     微信服务器访问时需要的令牌
     * @param $openids          需要拉取的用户OPENID列表
     *
     * $openids取值样例：
     * {
     *   "user_list": [
     *   {
     *   "openid": "otvxTs4dckWG7imySrJd6jSi0CWE",
     *   "lang": "zh-CN"
     *   },
     *   {
     *   "openid": "otvxTs_JZ6SEiP0imdhpi50fuSZg",
     *   "lang": "zh-CN"
     *   }
     *   ]
     *   }
     */
    public function getUsersInfo($access_token, $openids){
        $url = "{$this->api_domain}/cgi-bin/user/info/batchget?access_token={$access_token}";
        return [];
    }

    /**
     * 拉取用户列表
     *
     * @param $access_token
     * @param $next_openid
     * @return array
     */
    public function getUsers($access_token, $next_openid){
        $url = "{$this->api_domain}/cgi-bin/user/get?access_token={$access_token}&next_openid={$next_openid}";
        return [];
    }


}