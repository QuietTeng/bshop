<?php
/**
 * @copyright (c) 2015 www.jiguang.cn
 * @file jpush.php
 * @author songsang
 * @date 2016/9/20 
 * @version 3.3
 */

 /**
 * @class Hsms
 * @brief 短信发送接口
 */
class Jpush
{
	private static $jpushObj = null;
	private static $app_key = 'fec286c74024826099d0fa6c';
	private static $master_secret ='cf256710c1a13f2eac0d539f';

	public function __construct($app_key,$master_secret){
		if($app_key)  $this->app_key = $app_key;
		if($master_secret) $this->master_secret = $master_secret;
		$classFile = IWeb::$app->getBasePath().'plugins/src/JPush/Client.php';
		require($classFile);
		self::$jpushObj = new Client($this->app_key,$this->master_secret);
	}

	/**
	 * @brief 消息推送
	 * @param string $mobiles 多个手机号为用半角,分开，如13899999999,13688888888（最多200个）
	 * @param string $content 短信内容
	 * @param int $delay 延迟设置
	 * @return success or fail
	 */
	public static function send($mobile,$content,$time_to_live= 86400 ,$apns_production = false)
	{
		$api_call_back = false;
		if(!$mobile||!is_array($content)){//推送消息检验
			return $api_call_back;
		}
		$push = self::jpushObj->push();
		$platform = array('ios', 'android');
		$alias = $mobile;  //识别号,目前以手机号为唯一凭证
		$content = 'hello';
		$message = array(
		    'title' => 'hello',
		    'content_type' => 'text',
		    'extras' =>$content
		);
		$options = array(
		    'time_to_live' => $time_to_live,  //离线缓存时间,默认一天,最长10天
		    'apns_production' => $apns_production
		);
		$response = $push->setPlatform($platform)
		    ->addAlias($alias)
		    ->message($content, $message)
		    ->options($options);
		    ->send();
		//接口返回消息处理
		if($response) $api_call_back = true;
		return $api_call_back;
	}
}

 