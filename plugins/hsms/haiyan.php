<?php
/**
 * @copyright (c) 2015 aircheng.com
 * @file haiyan.php
 * @brief **短信发送接口
 * @author nswe
 * @date 2015/5/30 15:29:38
 * @version 3.3
 */

 /**
 * @class haiyan
 * @brief 短信发送接口 http://sms.ihuyi.com/
 */
class haiyan extends hsmsBase
{
	private $username = 'cf_boe';
	private $userpwd = '21218cca77804d2ba1922c33e0151105';
	private $submitUrl = "http://106.ihuyi.com/webservice/sms.php?method=Submit";

	/**
	 * @brief 获取config用户配置
	 * @return array
	 */
	private function getConfig()
	{
		//如果后台没有设置的话，这里手动配置也可以
		$siteConfigObj = new Config("site_config");
		return array(
			'userid'   => $siteConfigObj->sms_userid,
			'username' => $siteConfigObj->sms_username,
			'userpwd'  => $siteConfigObj->sms_pwd,
		);
	}

	/**
	 * @brief 发送短信
	 * @param string $mobile
	 * @param string $content
	 * @return
	 */
	public function send($mobile,$content)
	{
		$config = self::getConfig();

		$post_data = array(
			'account'   => $config['username'],
			'password'  => $config['userpwd'],
			'mobile' => $mobile,
			'content'  => $content,
			 
		);
		$url    = $this->submitUrl;
		$string = '';
		foreach ($post_data as $k => $v)
		{
		   $string .="$k=".urlencode($v).'&';
		}

		$post_string = substr($string,0,-1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
		$result = curl_exec($ch);
		return $this->response($result);
	}

	/**
	 * @brief 解析结果
	 * @param $result 发送结果
	 * @return string success or fail
	 */
	public function response($result)
	{	
		$result = $this->xml_to_array($result);
		if($result['SubmitResult']['code']==2)
		{
			return 'success';
		}
		else
		{
			return $this->getMessage($result['SubmitResult']['code']);
		}
	}

	//返回消息提示
	public function getMessage($code)
	{
		$messageArray = array(
			400  =>"非法ip访问",
			401  =>"帐号不能为空",
			402  =>"密码不能为空",
			403  =>"手机号码不能为空",
			4030 =>"手机号码已被列入黑名单",
			404  =>"短信内容不能为空",
			405  =>"用户名或密码不正确",
			4050 =>"账号被冻结",
			4051 =>"剩余条数不足",
			4052 =>"访问ip与备案ip不符",
			406  =>"手机格式不正确",
			407  =>"短信内容含有敏感字符",
			4070 =>"签名格式不正确",
			4071 =>"没有提交备案模板",
			4072 =>"短信内容与模板不匹配",
			4073 =>"短信内容超出长度限制",
			408  =>"您的帐户疑被恶意利用，已被自动冻结，如有疑问请与客服联系。",
		);
		return isset($messageArray[$code]) ? $messageArray[$code] : "未知错误";
	}

	private function xml_to_array($xml){
		$arr = array();
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array($subxml);
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
	//短信配置参数
	public function getParam(){
		return array(
			"username" => "用户名",
			"userpwd"  => "密码",
			"usersign" => "短信签名",
		);
	}
}