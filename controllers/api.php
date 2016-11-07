<?php
/**
 * @zhenglk Api    edit by songsang
 * @class Api
 * @note
 * test url http://120.55.164.96:81/api/seller_app/seller_id/100/go/user_login_check/safe_key/安全密钥
 */
class Api extends IController {

	public  $layout = '';
	private $app_base_params = array();//基本参数
	private $seller_app_safe_key = '59075b1015aa69f33a27099229295aae';  //通用私匙
	private $app_post_params = array();

	function init() {

	}

	/**
	 * 对传递的POST参数进行解析
	 * @return type
	 */
	private function parse_app_param() {
		if (isset($_POST['data'])) {
			$tmp_data = $_POST['data'];
			$_POST = NULL;
			if ($tmp_data) {
				if (is_string($tmp_data)) {//传递的可能是个Json字符串
					$json_tmp_data = @json_decode($tmp_data, true);
					if ($json_tmp_data !== NULL) {
						$this->app_post_params = $json_tmp_data;
					}
					$_POST = $json_tmp_data;
				} else {
					$_POST['data'] = $tmp_data;
				}
			}
			return;
		}
	}

	/*
	 *  接口入口
	 *  和商家APP对接有关的方法开始了
	 *  http://网站域名或是IP/api/seller_app/seller_id/100/go/user_login_check/safe_key/安全密钥
	 */

	public function seller_app() {//商家APP接口调用的入口
		$this->app_base_params['seller_id'] = IFilter::act(IReq::get('seller_id'), 'int'); //商家ID
		$this->app_base_params['safe_key'] = IFilter::act(IReq::get('safe_key'), 'string'); //安全密钥
		$this->app_base_params['call_method'] = IFilter::act(IReq::get('go'), 'string'); //调用方法
		$this->app_base_params['ip'] = IClient::getIp(); //调用者IP
		if (!$this->app_base_params['ip']) {//未指定IP时
			return $this->seller_app_echo(-1000);
		}
		if ($this->seller_app_ip_check()) {//当前在黑名单的时候
			return $this->seller_app_echo(-999);
		}
		if (!$this->seller_app_get_seller()) {//根据商家ID获取不到商家信息时
			return $this->seller_app_echo(-998);
		}
		if (!$this->seller_app_check_safe_key()) {//根据商家加密的密钥是否正确
			return $this->seller_app_echo(-997);
		}
		if (!$this->app_base_params['call_method']) {//未指定执行方法
			return $this->seller_app_echo(-996);
		}
		if (!method_exists($this, $this->app_base_params['call_method'])) {//指定的方法名称不存在
			return $this->seller_app_echo(-995);
		}
		$method_name = $this->app_base_params['call_method'];
		$this->parse_app_param(); //对传递的POST参数进行解析
		//$this->record_log();//日志记录
		return $this->$method_name(); //结果返回
	}



	/**
	 * 1.商家APP接口中 用户登录判断
	 */
	private function user_login_check(){
		$username =  IFilter::act($_POST['username'], 'string'); //用户名
		$password =  IFilter::act($_POST['password'], 'string'); //密码
		$result = array(
			'sult' => 'fail',
			'message' => '',
			'error_num' => 0,
			'user_info'=>null
		);
		if(!$username){
			$result['message'] = '未指定用户名';
			$result['error_num'] = -100;
			$this->seller_app_echo($result);

		}
		if(!$password){
			$result['message'] = '未指定密码';
			$result['error_num'] = -99;
			$this->seller_app_echo($result);

		}

		//登录方式
		if(preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $username)){
			$where = "email='".$username."'";
		}elseif(preg_match('/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/', $username)){
			$where = "mobile='".$username."'";
		}elseif(is_string($username)){	
			$where = "username='".$username."'";
		}

		//会员信息
		$userInfo = new IQuery('user as u');
		$userInfo->join   = "left join member as m on m.user_id = u.id";
		$userInfo->where  = $where;
		$userInfo->fields = 'u.username,m.true_name,u.password,m.user_type,m.group_id as user_group,m.expires_time,m.member_card,m.mobile,m.email';
		//$userInfo->debug = 1;
		$userInfoData = $userInfo->find();
		//用户
		if(!$userInfoData){
			$result['message'] = '不存在的用户';
			$result['error_num'] = -98;
			$this->seller_app_echo($result);
		}
		//密码判断
		if($password!==$userInfoData[0]['password']){
			$result['message'] = '密码错误';
			$result['error_num'] = -97;
			$this->seller_app_echo($result);

		}
		//登录验证
		// if(ISession::get($userInfoData[0]['username'])){
		if($userInfoData){
			$result['sult'] = 'success';
			$result['user_info'] = $userInfoData;
		}else{
			$result['message'] = '未登陆';
			$result['error_num'] = -96;
		}
		$this->seller_app_echo($result);
	}


	/**
	 * 2.商家APP接口中 发送短信
	 */
	private function  send_mms(){
		// $mobile = '18306258175';
		// $content = '您的验证码是：1234。请不要把验证码泄露给其他人。';
		$mobile = IFilter::act($_POST['mobile'], 'string');
		$content = IFilter::act($_POST['content'], 'string');
		$result = array(
			'sult' => 'fail',
			'message' => '',
			'error_num' => 0,
		);
		if(!$mobile){
			$result['message'] = '未指定手机号码';
			$result['error_num'] = -100;
			return $this->seller_app_echo($result);
		}
		if(!$content){
			$result['message'] = '未指定短信内容';
			$result['error_num'] = -99;
			return $this->seller_app_echo($result);
		}
		$send_result = Hsms::send($mobile,$content);
		if('success'==$send_result){
			$result['sult'] = 'success';
		}else{
			$result['message'] = $send_result;
			$result['error_num'] = '-1';
		}
		//日志记录
		$this->seller_app_echo($result);
	}

	
	/**
	 * 3.商家APP接口中 根据电话号码查询用户信息
	 */
	private function get_user_info_from_mobile($isMobile=1,$isUid=0){

		$result = array(
			'sult' => 'fail',
			'message' => '',
			'error_num' => 0,
		);

		if($isMobile){
			$mobile = IFilter::act($_POST['mobile'], 'string');
			if(!$mobile){
				$result['message'] = '未指定手机号码';
				$result['error_num'] = -100;
				return $this->seller_app_echo($result);
			}
			$field_where  = "m.mobile='".$mobile."'"; 
		}else{
			$uid = IFilter::act($_POST['uid'], 'string');
			if(!$uid){
				$result['message'] = '未指定uid';
				$result['error_num'] = -99;
				return $this->seller_app_echo($result);
			}
			$field_where  = "u.id='".$uid."'"; 
		}	 
		$result = null;

		//会员信息
		$userInfo = new IQuery('user as u');
		$userInfo->join   = "left join member as m on m.user_id = u.id";
		$userInfo->where  = $field_where;
		$userInfo->fields = 'u.id as uid,u.username,m.true_name,u.password,m.expires_time,m.member_card,m.mobile,m.email,m.area,m.contact_addr,m.qq,m.sex,m.user_type,m.group_id  as user_group,m.birthday,m.point,m.zip,m.status,m.last_login';
		//$userInfo->debug = 1;
		$userInfoData = $userInfo->find();
		//用户
		if(!$userInfoData){
			$result['message'] = '没有用户信息';
			$result['error_num'] = -98;
			$this->seller_app_echo($result);
		}

		//地区数据处理
		if($userInfoData[0]['area']){
			$areaDB = new IQuery('areas');
			$areaDB->where = "area_id in(".trim($userInfoData[0]['area'],',').")";
			$areaDB->fields = 'area_name';
			$areaData =   $areaDB->find();
			$userInfoData[0]['area'] = '';
			foreach ($areaData as  $value) {
				$userInfoData[0]['area'] .= $value['area_name']." ";
			}
			$userInfoData[0]['area'] = trim($userInfoData[0]['area']);
		}

		$this->seller_app_echo($userInfoData[0]);
	}
		
	/**
	 * 4.商家APP接口中 根据用户ID获取用户的详细信息
	 */	
	private function  get_user_info_from_uid(){
		$this->get_user_info_from_mobile(0,1);
	}

	/**
	 * 5.商家APP接口中 根据用户UID修改密码
	 */	
	private function modify_user_password(){
		$uid = IFilter::act($_POST['uid'], 'string');
		$old_password = IFilter::act($_POST['old_password'], 'string');
		$new_password = IFilter::act($_POST['new_password'], 'string');
		$result = array(
			'sult' => 'fail',
			'message' => '',
			'error_num' => 0,
		);
		//用户
		if(!$uid){
			$result['message'] = '未指定uid';
			$result['error_num'] = -100;
			$this->seller_app_echo($result);
		}
		if(!$new_password){
			$result['message'] = '未指定旧密码';
			$result['error_num'] = -99;
			$this->seller_app_echo($result);
		}
		if(!$new_password){
			$result['message'] = '未指定新密码';
			$result['error_num'] = -98;
			$this->seller_app_echo($result);
		}
		if($new_password==$old_password){
			$result['message'] = '新旧密码相同';
			$result['error_num'] = -97;
			$this->seller_app_echo($result);
		}
		//密码限制
		//code......
		//用户信息
		$userDB = new IQuery('user');//查询
		$userDB->where ='id='.$uid;
		$userInfo = $userDB->find();
		if(!$userInfo){
			$result['message'] = '不存在的用户';
			$result['error_num'] = -96;
			$this->seller_app_echo($result);
		}
		
		if($old_password!==$userInfo[0]['password']){
			$result['message'] = '密码错误';
			$result['error_num'] = -95;
			$this->seller_app_echo($result);
		}

		//修改密码
		$userDB = new IModel('user'); //修改
		$userDB->setData(array('password' => $new_password));
		$ros = $userDB->update('id='.$uid);
		if(1===$ros){
			$result['sult'] = 'success';
		}else{
			$result['message'] = '修改密码失败,请重试';
			$result['error_num'] = '-1';
		}
		$this->seller_app_echo($result);
	}

	/**
	 * 6.商家APP接口中 添加用户
	 */
	private function   add_user_info(){
		$username = IFilter::act($_POST['username'], 'string');
		$password = IFilter::act($_POST['password'], 'string');
		$user_type = IFilter::act($_POST['user_type'], 'string');
		$sex = IFilter::act($_POST['sex'], 'string');
		$status = IFilter::act($_POST['status'], 'string');
		$result = array(
			'sult' => 'fail',
			'message' => '',
			'error_num' => 0,
		);
		if(!$username){
			$result['message'] = '未指定账号名称';
			$result['error_num'] = -100;
			$this->seller_app_echo($result);
		}
		if(!$password){
			$result['message'] = '未指定密码';
			$result['error_num'] = -99;
			$this->seller_app_echo($result);
		}
		if(!$user_type){
			$result['message'] = '未指定会员类型';
			$result['error_num'] = -98;
			$this->seller_app_echo($result);
		}
		if(!$sex){
			$result['message'] = '未指定性别';
			$result['error_num'] = -97;
			$this->seller_app_echo($result);
		}
		if(!$status){
			$result['message'] = '未指定用户状态';
			$result['error_num'] = -96;
			$this->seller_app_echo($result);
		}

		//会员信息监测
		//创建会员操作类
		$userDB   = new IModel("user");
		$memberDB = new IModel("member");

		if($userDB->getObj("username='".$username."'"))
		{
			$result['message'] = '用户名重复';
			$result['error_num'] = -95;
			$this->seller_app_echo($result);
		}
		$email =  IFilter::act($_POST['email'], 'string');
		if($email && $memberDB->getObj("email='".$email."'"))
		{
			$result['message'] = '邮箱重复';
			$result['error_num'] = -94;
			$this->seller_app_echo($result);
		}
		$mobile = IFilter::act($_POST['mobile'], 'string');
		if($mobile && $memberDB->getObj("mobile='".$mobile."'"))
		{
			$result['message'] = '手机号码重复';
			$result['error_num'] = -93;
			$this->seller_app_echo($result);
		}
		
		//账号添加
		$user = array(
			'username' => $username,
			'password' => $password,
		);
		$userDB->setData($user);
		$user_id = $userDB->add();
		if($user_id){
			//会员信息添加
			$this->app_post_params['user_id'] = $user_id;  
			unset($this->app_post_params['username'],$this->app_post_params['password']);
			$memberDB->setData($this->app_post_params);
			$wws = $memberDB->add();
			//echo $memberDB->getsql();
			$result['sult'] = 'success';
		}else{
			$result['message'] = '添加用户失败';
			$result['error_num'] = '-1';
		}
		$this->seller_app_echo($result);
		
	}

	/**
	 * 7.商家APP接口中 获取用户分组
	 */
	private function  get_user_group(){
		$userGroupDB = new IQuery('user_group');
		$userGroupDB->fields = 'id,group_name,discount';
		$result = null;
		$result = $userGroupDB->find();
		$this->seller_app_echo($result);
	}

	/**
	 * 8.商家APP接口中  获取用户信息查询其的消费记录
	 */
	private function  get_user_order_list(){
		$uid = IFilter::act($_POST['uid'], 'string');
		$username = IFilter::act($_POST['username'], 'string');
		$mobile = IFilter::act($_POST['mobile'], 'string');
		$page = IFilter::act($_POST['current_page'], 'string');;
		$pagesize = IFilter::act($_POST['limit_num'], 'string');;
		if($uid){
			$fields_where = "u.id='".$uid."'";
		}elseif($username){
			$fields_where = "u.username='".$username."'";
		}elseif($mobile){
			$fields_where = "m.mobile='".$mobile."'";
		}else{
			$result['message'] = '未指定用户标识参数';
			$result['error_num'] = -100;
			$this->seller_app_echo($result);
		}
		if($fields_where){
			$userDB = new IQuery('user as u');
			$userDB->join   = "left join member as m on m.user_id = u.id";
			$userDB->where = $fields_where;
			//$userDB->debug=1;
			$userInfo = $userDB->find();
			if(!$userInfo){
				$result['message'] = '不存在的用户';
				$result['error_num'] = -99;
				$this->seller_app_echo($result);
			}
		}
		//结果数据
		$result = array(
			'total_count'=>0,
			'page_count'=>0,
			'list'=>null
		);

		$page = $page?$page:1;
		$pagesize = $pagesize?$pagesize:10;

		//订单数据查询
		$orderDB = new IQuery('order');
		$fields_where ='';
		if($this->app_post_params['status']&&$this->app_post_params['uid']<>'-1'){
			$fields_where =  "status=".$this->app_post_params['status'];
		}
		if($this->app_post_params['pay_status']&&$this->app_post_params['pay_status']<>'-1'){
			$fields_where .= " and pay_status=".$this->app_post_params['pay_status'];
		}
		if($this->app_post_params['distribution_status']&&$this->app_post_params['distribution_status']<>'-1'){
			$fields_where .= " and distribution_status=".$this->app_post_params['distribution_status'];
		}
		$orderDB->where = $fields_where;

		//商品总数
		$orderDB->fields="count(*) as total";
		$total = $orderDB->find();
		if($total){
			$result['total_count'] = $total[0]['total'];
			$result['page_count'] =  ceil($total[0]['total']/$pagesize);
		} 

		//分页
		if(-1==$pagesize){//全部数据
			$orderDB->limit = 0;

		}else{
			$orderDB->page = $page;
			$orderDB->pagesize = $pagesize;
		}
		//$orderDB->debug=1;
		//排序
		$order_mode =   $this->app_post_params['order_mode'];
		$where_order = '';
		if($order_mode==3){
				$where_order = 'payable_amount ';
		}elseif($order_mode==2){
				$where_order = 'payable_amount desc';
		}elseif($order_mode==1){
				$where_order = 'create_time';
		}else{
				$where_order = 'create_time desc';
		}
		$orderDB->order = $where_order;
		$orderDB->fields="id,order_no,pay_type,distribution,status,pay_status,distribution_status,accept_name,postcode,telphone,address,mobile,pay_time,send_time,create_time,completion_time,accept_time,postscript,note,payable_amount,real_amount,payable_freight,real_freight,pay_fee,taxes,promotions,discount,order_amount,trade_no";
		$orderInfo = $orderDB->find();
		if($orderInfo){
			$result['list'] = $orderInfo;
		}
		$this->seller_app_echo($result); 
	}


	/**
	 * 9.商家APP接口中 根据条件查询商品
	 */
	private function get_goods_list(){
		$brand_id = IFilter::act($_POST['brand_id'], 'string'); 
		$category_id = IFilter::act($_POST['category_id'], 'string'); 
		$keyword = IFilter::act($_POST['keyword'], 'string'); 
		$pagesize = IFilter::act($_POST['limit_num'], 'string'); 
		$page = IFilter::act($_POST['current_page'], 'string'); 
		$order_mode = IFilter::act($_POST['order_mode'], 'string'); 
		//结果数据
		$result = array(
			'total_count'=>0,
			'page_count'=>0,
			'list'=>null
		);
		$page = $page?$page:1;
		$pagesize = $pagesize?$pagesize:10;
		
		//商品
		$goodsDB =  new IQuery('goods');
		$fields_where ='';
		$f = 0;
		if($brand_id&&$brand_id<>'-1'){
			$fields_where =  "brand_id=".$brand_id;
			$f++;
		}
		if($category_id&&$category_id<>'-1'){
			$st =  $f?" and ":'';
			$fields_where .= $st."category_id=".$category_id;
			$f++;
		}
	    if($keyword){
	    	$st =  $f?" and ":'';
	    	$fields_where .= $st."keywords like '%".$keyword."%'";
	    }
		$goodsDB->where = $fields_where;
		//商品总数
		$goodsDB->fields="count(*) as total";
		$total = $goodsDB->find();
		if($total){
			$result['total_count'] = $total[0]['total'];
			$result['page_count'] =  ceil($total[0]['total']/$pagesize);
		} 

		//分页
		if(-1==$pagesize){//全部数据
			$goodsDB->limit = 0;

		}else{
			$goodsDB->page = $page;
			$goodsDB->pagesize = $pagesize;
		}

		//排序
		// $where_order = '';
		// if($order_mode==3){
		// 		$where_order = 'payable_amount ';
		// }elseif($order_mode==2){
		// 		$where_order = 'payable_amount desc';
		// }elseif($order_mode==1){
		// 		$where_order = 'create_time';
		// }else{
		// 		$where_order = 'create_time desc';
		// }
		// $goodsDB->order = $where_order;
		$goodsDB->fields = "*";
		$goods_data = $goodsDB->find();
		if($goods_data){
			$result['list'] = $goods_data;
		}
		$this->seller_app_echo($result);

	}


	/**
	 * 10.商家APP接口中 将摄像头识别出来的数据提交到电商系统的方法
	 */
	private function reply_camera_post() {
		//$group_id = IFilter::act($_POST['group_id'], 'int'); //集团号码
		$camera_no = IFilter::act($_POST['camera_no'], 'string'); //摄像头编号
		$people_info = is_array($_POST['people_info'])?$_POST['people_info']:@json_decode($_POST['people_info'],true);   //人脸信息 
		$sult = array(
			'sult' => 'fail',
			'message' => '',
			'error_num' => 0,
		);

		if (!$camera_no) {
			$sult['message'] = "未指定摄像机编号";
			$sult['error_num'] = -100;
			$this->seller_app_echo($sult);
		}

		if (!$sult['error_num'] && !$people_info) {
			$sult['message'] = "未指定人脸信息";
			$sult['error_num'] = -99;
			$this->seller_app_echo($sult);
		}
		if (!$sult['error_num'] && !is_array($people_info)) {
			$sult['message'] = "未指定人脸信息";
			$sult['error_num'] = -98;
			$this->seller_app_echo($sult);
		}
		//$_FILES[0]=array('name'=>'图片1.jpg','type'=>'image/jpeg','tmp_name'=>'C:\Users\09001153\AppData\Local\Temp\php5D0.tmp','error'=>0,'size'=>7940);
		//$_FILES[1]=array('name'=>'图片1.jpg','type'=>'image/jpeg','tmp_name'=>'C:\Users\09001153\AppData\Local\Temp\php5D0.tmp','error'=>0,'size'=>7940);
		if (!$sult['error_num'] && !($_FILES)) {
			$sult['message'] = "未传递文件信息";
			$sult['error_num'] = -97;
			$this->seller_app_echo($sult);
		}


		// $data  = $people_info;
		// $people_info = array();
  //       $people_info[0] = $people_info;
		//$this->seller_app_echo(array('1',$_POST,$_FILES,count($_FILES),count($people_info)));
		

		if (!$sult['error_num'] && count($_FILES) !== count($people_info)) {
			$sult['message'] = "指定的文件与人脸信息数量不匹配";
			$sult['error_num'] = -96;
			$this->seller_app_echo($sult);			
		}
		if (!$sult['error_num']) {//对人脸信息和文件信息的下载进行对比，只要有一个错误，就出错
			foreach ($people_info as $key => $a_info) {
				if (!isset($_FILES['file'.$key])) {
					$sult['message'] = "上传的文件信息与人脸信息的下标不匹配";
					$sult['error_num'] = -95;
					$this->seller_app_echo($sult);
					break;
				}
			}
		}
		if (!$sult['error_num'] && !$this->seller_app_get_camera_info($camera_no)) {
			$sult['message'] = "指定的摄像机编号找不到对应的摄像机信息";
			$sult['error_num'] = -94;
			$this->seller_app_echo($sult);
		}
		
		$db_info = $stranger = $user = array();
		foreach ($people_info as $key => $a_info) { 
			if (!isset($a_info['people_sex']) || !isset($a_info['people_glass']) || !isset($a_info['people_class']) || !isset($a_info['recognition_rate'])) {//如果指定的人脸信息不重要参数未指定的时候
				continue;
			} else {//参数大致正确的时候
				$member_card = empty($a_info['member_card']) ? '' : $a_info['member_card'];
				$member_mobile = empty($a_info['member_mobile']) ? '' : $a_info['member_mobile'];
				$member_phone = empty($a_info['member_phone']) ? '' : $a_info['member_phone'];
				//上传图片信息
				$upload_info = $this->seller_app_upload_file($key);
				$is_stranger = false;
				$tmp_user_info = NULL;
				$db_info[$key] = array(
					'people_sex' => is_numeric($a_info['people_sex']) ? $a_info['people_sex'] : 1,
					'people_glass' => is_numeric($a_info['people_glass']) ? $a_info['people_glass'] : 1,
					'people_class' => is_numeric($a_info['people_class']) ? $a_info['people_class'] : 10,
					'recognition_rate' => $a_info['recognition_rate'],
					'member_card' => $member_card,
					'member_mobile' => $member_mobile,
					'member_phone' => $member_phone,
					'record_url' => empty($upload_info['url']) ? '' : $upload_info['url'],
				);

//-----------------整理出陌生人还理会员，为下一步的消息推送做准备S--------------------------------------------------

				if (!$member_card && !$member_mobile && !$member_phone) {//没有传递会员信息时，理解成陌生人S
					$is_stranger = true;
				} else {//有传递会员信息时，根据读取出对应的会员信息是否正确，判断是以会员信息推送还是陌生人消息推送S
					$tmp_user_info = $this->seller_app_get_user_info($member_card, $member_mobile, $member_phone);
					if (!$tmp_user_info) {//会员信息不存在时S
						$is_stranger = true; //以陌生人处理
					}//会员信息不存在时E
				}//有传递会员信息时，根据读取出对应的会员信息是否正确，判断是以会员信息推送还是陌生人消息推送E
				if ($is_stranger) {
					$stranger[$key] = $db_info[$key];
				} else {
					$user[$key] = $db_info[$key] + $tmp_user_info;
				}
//-----------------整理出陌生人还理会员，为下一步的消息推送做准备E--------------------------------------------------
				$db_info[$key]+=$this->app_base_params;
			}//参数大致正确的时候E
		}//For循环
		if ($db_info) {//保存相关到数据库
			$this->seller_app_save_post_info($db_info);
		} else {
			$sult['message'] = "传递的参数中，有关人脸信息的结构不正确";
			$sult['error_num'] = -93;
			$this->seller_app_echo($sult);
		}
		if (!$sult['error_num'] && ($stranger || $user)) {//消息推送
			$sult = $this->seller_app_push_camera_message($stranger, $user);
		} else {
			$sult['message'] = "数据保存成功，但是传递用户信息中没有任何会员信息和陌生人信息";
			$sult['error_num'] = -92;
			$this->seller_app_echo($sult);			
		}
		if($sult['sult'] <>'fail'){
			$data = $_POST;
			$data['url'] = $upload_info['url'];
			$this->seller_app_echo($data);
		}
		else
			$this->seller_app_echo($sult);
	}

//----------------------------------------------------------1大堆基本的商家APP有关的API方法开始-------------------------------------------------------------------------
	//商家APP接口输出结果
	private function seller_app_echo($value) {
		
		  //日志记录处理，
		  $log_info = array(//要记录的数据
			  'Server' => $_SERVER,
			  'Post' => $_POST,
			  'Get' => $_GET,
			  'Files' => $_FILES,
			  'Sult'=>$value
		  );
		  $logObj = new log('db');
		  $logObj->write('operation', array("商家接口日志" . date('Y-m-d H:i:s') . 'Ip:' . $this->seller_app_params['ip'], var_export($log_info, true)));
		if (!is_scalar($value)) {
			exit(json_encode($value));
		}
		exit(strval($value));
	}

	/*
	  商家APP调用时检测是否在黑名单中
	 * @output Boolean
	 */

	private function seller_app_ip_check() {
		$black_list = array(//黑名单的IP列表
		);
		if (in_array($this->app_base_params['ip'], $black_list)) {
			return true;
		}
		return false;
	}

	/*
	  商家APP根据传递的密钥 进行安全密钥的校验
	 * @output Boolean
	 */

	private function seller_app_check_safe_key() {
		$create_safe_key = md5($this->seller_app_safe_key . $this->app_base_params['ip'] . date("Y-m-d") . $this->app_base_params['seller_id']);
		return $this->app_base_params['safe_key'] == $create_safe_key;
	}

	/*
	 * 商家APP根据this->seller_app_params['seller_id']读取出商家信息
	 * @output array()
	 */

	private function seller_app_get_seller() {
		//echo $this->app_base_params['seller_id'];
		$tb_sellers = new IModel('seller');
		$seller_info = 	$tb_sellers->getObj('id = '.$this->app_base_params['seller_id']);
		if(false == $seller_info) return false;
		//后期这里需要从数据库中读取出来
		$this->app_base_params['seller_info'] = array(
			'id' => $this->app_base_params['seller_id'], //商家ID
			'seller_name' => &$seller_info['seller_name'],
			'true_name' =>  &$seller_info['true_name'],
			'is_lock' =>  &$seller_info['is_lock'], //是否被锁定
			'is_del' =>  &$seller_info['is_del'], //是否被删除
			'is_vip' =>  &$seller_info['is_vip'], //是否是特级商家 
			'email' =>  &$seller_info['email'],
			'mobile' =>  &$seller_info['mobile'],
			'phone' =>  &$seller_info['phone'],
		);
		return $this->app_base_params['seller_info'];
	}

//----------------------------------------------------------1大堆基本的商家APP有关的API方法结束-------------------------------------------------------------------------
	/*
	   商家APP根据$camera_no读取出对应的摄像头信息
	 * @output array()
	 */

	private function seller_app_get_camera_info($camera_no) {
		//后期这里需要从数据库中读取出来
		$cameraDB = New IQuery('camera');
		$cameraDB->where = "camera_no='".$camera_no."'";
	    //$cameraDB->debug = 1;
		$camera_info = $cameraDB->find();
		if(!$camera_info){
			$this->app_base_params['camera_info']= false;
		}else{
			$this->app_base_params['camera_info'] = array(
				'camera_no' => $camera_no, //摄像机
				'seller_id' => $camera_info[0]['seller_id'],
				'id' => $camera_info[0]['id'],
			);
		}
		
		return $this->app_base_params['camera_info'];
	}

	/**
	 * seller_app_upload_file根据$_FILES对应的下标完成单个文件的上传
	 * @param type $file_key
	 * @output array(
	  'error_num'=>0,//错误代码
	  'message'=>'',//错误文本
	  'url'=>'',//文件的访问URL
	  'file_size'=>0,//文件的字节
	  'pic_width'=>'',//图片文件的宽度
	  'pic_height'=>'',//图片文件的高度
	  'relative_path'=>'',//文件的相对路径
	  )
	 */
	private function seller_app_upload_file($file_key) {
	//	$tmp_info = '/upload/' . date('YmdHi') . '/' . $file_key . 'jpg';
		$upObj = new IUpload(10000,array(),'file0');
		$dir  = IWeb::$app->config['upload'].'/'.date('Y/m/d');
		$upObj->setDir($dir);
		$upState = $upObj->execute2();
		$photoInfo = $upState['file0'][0];
		return array(
			'error_num' => 0, //错误代码
			'message' => '', //错误文本
			'url' => 'http://'.$_SERVER['HTTP_HOST'] .'/'. $photoInfo['fileSrc'], //文件的访问URL
			'file_size' => 0, //文件的字节
			'pic_width' => '', //图片文件的宽度
			'pic_height' => '', //图片文件的高度
			'relative_path' => $photoInfo['fileSrc'], //文件的相对路径
		);
	}

	/**
	 * seller_app_get_user_info 根据会员卡号，手机号，电话号码，获取对应的会员信息,优先读取顺序  $member_card->$member_mobile->$member_phone
	 * @param type $member_card
	 * @param type $member_mobile
	 * @param type $member_phone
	 * @return type 读取成功返回array(),获取不到返回NULL
	 */
	private function seller_app_get_user_info($member_card = '', $member_mobile = '', $member_phone = '') {
		if($member_card){
			$field_where  = "m.member_card='".$member_card."'"; 
		}elseif($member_mobile){
			$field_where  = "m.mobile='".$member_mobile."'"; 
		}elseif($member_phone){
			$field_where  = "m.telphone='".$member_phone."'"; 
		}else{
			return NULL;
		}
		//会员信息
		$userInfo = new IQuery('user as u');
		$userInfo->join   = "left join member as m on m.user_id = u.id";
		$userInfo->where  = $field_where;
		$userInfo->fields = 'u.id as uid,u.username,m.true_name,u.password,m.expires_time,m.member_card,m.mobile,m.email,m.area,m.contact_addr,m.qq,m.sex,m.user_type,m.group_id  as user_group,m.birthday,m.point,m.zip,m.status,m.last_login';
		//$userInfo->debug = 1;
		$userInfoData = $userInfo->find();
		return $userInfoData;

	}

	/**
	 * 将摄像头提交上来的会员或是陌生人信息，在APP中推送提醒消息
	 * @param type $stranger //陌生人信息 array()
	 * @param type $user   //会员信息 array()
	 * @return type array()
	 */
	private function seller_app_push_camera_message($stranger = array(), $user = array()) {
		$sult = array(
			"sult" => "success",
			"message" => "",
			"error_num" => 0
		);
		if (!$stranger && !$user) {
			$sult = array(
				"sult" => "fail",
				"message" => "没有指定陌生人信息并且也没有指定会员信息",
				"error_num" => -100
			);
		}

		if (empty($this->app_base_params['camera_info']['seller_id']) || empty($this->app_base_params['camera_info']['camera_no']) || empty($this->app_base_params['camera_info']['id'])) {
			$sult = array(
				"sult" => "fail",
				"message" => "推送消息前没有指定商家信息",
				"error_num" => -99
			);
		}


		/**
		 * 有关与APP消息推送的代码集成，在这里实现，但由于APP开发还没有确定。这一块就不能完成。
		 * edit by songsang  
		 * 极光推送 1对1 定点推送
		 */
		// $content = empty($stranger)?$user:$stranger;
		// $classFile = IWeb::$app->getBasePath().'plugins/jpush2/src/JPush/ob.php';
		// include_once($classFile);
		// $client = new ob('fec286c74024826099d0fa6c','cf256710c1a13f2eac0d539f');
		// $jiguang_callback = $client->push()
	 //    ->setPlatform('all')
	 //    ->addAllAudience()
	 //    ->setNotificationAlert('有客啦')
	 //    ->message('有客啦啦', array(
		//     'title' => '有个',
		//     'content_type' => 'text',
		//     'extras' =>$content,
		// ))
	 //    ->send();
		// if(!$jiguang_callback){
		// 	$sult = array(
		// 		"sult" => "fail",
		// 		"message" => "推送消息失败",
		// 		"error_num" => -99
		// 	);
		// }
		//保持推送消息
		//return $sult;
	}

	/**
	 * 将摄像头提交上来的会员或是陌生人信息,推送相关信息 保存到数据库
	 * @param type  $info  //会员信息 array()
	 * @return type array()
	 */
	private function seller_app_save_post_info($info){
		$query_DB = new IQuery('camera_user');
		$model_DB = new IModel('camera_user');
		foreach ($info as $key => $value) {
			$data = array(
				'people_sex'=>$value['people_sex'],
				'people_glass'=>$value['people_glass'],
				'people_class'=>$value['people_class'],
				'recognition_rate'=>$value['recognition_rate'],
				'member_card'=>$value['member_card'],
				'member_mobile'=>$value['member_mobile'],
				'member_phone'=>$value['member_phone'],
				'record_url'=>$value['record_url'],
				'ctime'=>ITime::getDateTime()
			);
			$model_DB->setData($data);
			$is_member = false;
			//会员检测
			if($data['member_mobile']){
				$query_DB->where = 'member_mobile ='.$data['member_mobile'];
				$row = $query_DB->find();
				if($row) $is_member = true;
			}
			if($is_member){
				$model_DB->update('id='.$row['id']);
			}else{
				$model_DB->add();
			}
		}
	}

	public function img_base64_decode($file){
		$file = $file?$file:IWeb::$app->getBasePath().'upload/2011/06/07/20110607105300463.png';
		$fp = fopen($file,"rb", 0);
		if($fp){
			return '';
		}
		$type = getimagesize($file);
		switch($type[2]){//判读图片类型  
			case 1:$img_type="gif";break;  
			case 2:$img_type="jpg";break;  
			case 3:$img_type="png";break;  
		}  
		$file_content = chunk_split(base64_encode(fread($fp,filesize($file))));
		$data ='data:image/'.$img_type.';base64,'.$file_content;
        fclose($fp);
        return $data;
	}

}
