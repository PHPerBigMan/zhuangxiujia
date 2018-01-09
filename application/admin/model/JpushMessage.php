<?php
/**
 * Created by PhpStorm.
 * User: baimifan-pc
 * Date: 2017/12/6
 * Time: 18:58
 */
namespace app\admin\model;
use JPush\Client;
use think\Model;

class JpushMessage extends Model{
    protected $table = "zjx_jpush_message";

    /**
     * @param $config
     * @param $mssage
     * @return int
     * author hongwenyang
     * method description : 发送极光推送集体消息
     */
    public static function sendAllMessage($config,$mssage){
        $client = new Client($config['AppKey'],$config['Secret']);
        try{
            $s = $client->push()
                ->setPlatform('all')
                ->addAllAudience()
                ->setNotificationAlert($mssage)
                ->send();
            if($s['http_code'] == 200){
                // 发送成功
                return 200;
            }else{
                // 发送失败
                return 404;
            }
        }catch (\Exception $exception){

        }
    }


    /**
     * @param $config
     * @param $alias
     * @param $message
     * author hongwenyang
     * method description : 极光推送个别消息
     */
    public static function sendAliasMessage($config,$alias,$message){
        $client = new Client($config['AppKey'],$config['Secret']);

        try{
            $client->push()
                ->setPlatform(array('android'))
                ->addAlias(array("$alias"))
                ->androidNotification($message,array(
                    'title'=>'装修之家'
                ))
                ->options(array(
                    'apns_production'=> false,
                ))->send();
        }catch (\Exception $exception){

        }
    }
}