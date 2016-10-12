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


	public function init()
	{

	}
	public function test_api(){



		//根据条件查询商品信息
	 //    $url = 'http://10.17.118.53//api/seller_app/seller_id/'.$this->seller_id.'/go/get_goods_list/safe_key/'.$this->seller_app_check_safe_key();
		// $data = array(
		// 	'brand_id'=>'1',
		// 	'category_id'=>'2',
		// 	'keyword'=>'苹果',
		// 	'limit_num'=>'1',
		// 	'current_page'=>'1',
		// 	'order_mode'=>'1',
		// 	);
		// echo $post_data = 'data='.json_encode($data);

	 //    //echo  file_get_contents($url);
		// //$this->curl_post($url,$post_data);
		// exit('www');

		// $data = array('username'=>'173058129@qq.com','password'=>md5('888888'));
		// $data = array('mobile'=>'15995850157','content'=>'您的验证码是：1234。请不要把验证码泄露给其他人。');
		//$data = array('uid'=>1,'old_password'=>'888888','new_password'=>'888888');
		// $data =  array(
		// 	'username'=>'u_zhou',
		// 	'password'=>md5('888888'),
		// 	'mobile'=>'15995850136',
		// 	'true_name'=>'孙勇',
		//     'expires_time'=>'2016-12-11 12:12:12',
		// 	'member_card'=>'3844434',
		// 	'user_type'=>'1',
		// 	'group_id'=>'1',
		// 	'email'=>'17376827@qq.com',
		// 	'area'=>'00838',
		// 	'contact_addr'=>'南京路',
		// 	'qq'=>'172772772',
		// 	'sex'=>'1',
		// 	'birthday'=>'2016-12-12',
		// 	'zip'=>'226555',
		// 	'status'=>1
		// 	);
			// );
		// $this->app_post_params['uid']='';
		// $this->app_post_params['username']='';
		// $this->app_post_params['mobile']='15995850157';
		// $this->app_post_params['status']='';
		// $this->app_post_params['pay_status']='';
		// $this->app_post_params['distribution_status']='';
		// $this->app_post_params['limit_num']='20';
		// $this->app_post_params['current_page']='1';
		// $this->app_post_params['order_mode']='1';
		// $data = array('brand_id');
		// // echo  json_encode($this->app_post_params);
		$array[]  = array('people_sex'=>1,'people_glass'=>1,'people_class'=>1,'recognition_rate'=>1,'member_card'=>'','member_mobile'=>'15995850157','member_phone'=>1);
		$array[]  = array('people_sex'=>1,'people_glass'=>1,'people_class'=>1,'recognition_rate'=>1,'member_card'=>1,'member_mobile'=>1,'member_phone'=>1); 
		$data['camera_no'] =1;
		$data['group_id'] = 1;
		$data['people_info'] =  ($array);

		// $data = array('username'=>'U_sun','password'=>md5('888888'));
		 
		echo  json_encode($data);
		
	}







	/**
	 * @brief curl请求
	 */
	public function curl_post(){

 
		//ini_set('default_socket_timeout', 1);
		$array[]  = array('people_sex'=>1,'people_glass'=>1,'people_class'=>1,'recognition_rate'=>1,'member_card'=>'','member_mobile'=>'15995850157','member_phone'=>1);
		$array[]  = array('people_sex'=>1,'people_glass'=>1,'people_class'=>1,'recognition_rate'=>1,'member_card'=>1,'member_mobile'=>1,'member_phone'=>1); 
		$data['camera_no'] =1;
		$data['group_id'] = 1;
		$data['people_info'] = json_encode($array);
		//$post_data = "data=".json_encode($data);


        
        
        $post_data['data'] = json_encode($data);       
        $post_data[] =  '@D:\www\iwebshop\controllers\20110607105608864.png';                                                                      
        $post_data[] =  '@D:\www\iwebshop\controllers\boe.jpg';

        //$cfile = curl_file_create('resource/test.png','image/png','testpic'); // try adding 

        //print_r($cfile);
        //exit('ww');

        //$post_data = array('data' => , 'file' => array('@D:\www\iwebshop\controllers\boe.jpg','@D:\www\iwebshop\controllers\boe.jpg'));
		//$post_data[]='ss';
		//$post_data[] ='@D:\www\iwebshop\controllers\boe.jpg';
		//$post_data[]='@D:\www\iwebshop\controllers\boe.jpg';
       
        print_r($post_data);


		$url = 'http://120.55.164.96:81/api/seller_app/seller_id/1/go/reply_camera_post/safe_key/1490decd407528a949940c649c3826dd';
 
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL , $url);
		curl_setopt($ch,CURLOPT_SAFE_UPLOAD, false);    // 5.6 给改成 true了, 弄回去 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT , 360);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($ch);
    	var_dump(curl_error($ch));  
      	print_r($result);
	}

	/*
	  商家APP根据传递的密钥 进行安全密钥的校验
	 * @output Boolean
	 */
	private function seller_app_check_safe_key() {
		$create_safe_key = md5($this->seller_app_safe_key . IClient::getIp() . date("Y-m-d") . $this->seller_id);
		return  $create_safe_key;
	} 

	public  function upload(){
		print_r($_FILES);
		 $this->redirect('upload');
	}
	public  function upload2(){
	  $w  = file_get_contents("php://input"); 

	  print_r($_FILES);
	  print_r($w);

	  echo "string";
	}
 
}
