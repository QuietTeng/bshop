<?php
/**
 * @brief 测试文件
 * @class test
 * @note  后台
 */
class test extends IController  
{
 	public  $layout = '';
 	private $seller_id = 1;
 	private $seller_app_safe_key = '59075b1015aa69f33a27099229295aae';  //通用私匙
 	private $app_post_params = array();


	 
	public  function test_jpush(){
		$classFile = IWeb::$app->getBasePath().'plugins/jpush2/src/JPush/ob.php';
		include_once($classFile);
		$client = new ob('fec286c74024826099d0fa6c','cf256710c1a13f2eac0d539f');

		$w =  $client->push()
	    ->setPlatform('all')
	    ->addAllAudience()
	    ->setNotificationAlert('人年识别图像推送')
	    ->message('有客啦啦', array(
		    'title' => '有个',
		    'content_type' => 'text',
		    'extras' =>array('people_sex'=>'男',
		    				'people_glass'=>'戴眼镜',
		    				'people_class'=>'青年',
		    				'recognition_rate'=>'20%',
		    				'member_card'=>'1234',
		    				'member_mobile'=>'15995850157',
		    				'member_phone'=>'15996756878',
		    				'record_url'=>'http://120.55.164.96:8080/upload/2016/11/07/20161107123447461.jpg',
		    )
		))
	    ->send();

	    print_r($w);
	}
 
}
