<?php $menuData=menu::init($this->admin['role_id']);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>后台管理</title>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="<?php echo $this->getWebSkinPath()."css/admin.css";?>" />
	<meta name="robots" content="noindex,nofollow">
	<link rel="shortcut icon" href="<?php echo IUrl::creatUrl("")."favicon.ico";?>" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/jquery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artdialog/artDialog.js"></script><script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artdialog/plugins/iframeTools.js"></script><link rel="stylesheet" type="text/css" href="/runtime/_systemjs/artdialog/skins/aero.css" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/form/form.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/autovalidate/validate.js"></script><link rel="stylesheet" type="text/css" href="/runtime/_systemjs/autovalidate/style.css" />
	<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artTemplate/artTemplate.js"></script><script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/artTemplate/artTemplate-plugin.js"></script>
	<script type='text/javascript' src="<?php echo $this->getWebViewPath()."javascript/common.js";?>"></script>
	<script type='text/javascript' src="<?php echo $this->getWebViewPath()."javascript/admin.js";?>"></script>
</head>
<body>
	<div class="container">
		<div id="header">
			<div class="logo">
				<a href="<?php echo IUrl::creatUrl("/system/default");?>"><img src="<?php echo $this->getWebSkinPath()."images/admin/logo.png";?>" width="303" height="43" /></a>
			</div>
			<div id="menu">
				<ul name="topMenu">
					<?php foreach(menu::getTopMenu($menuData) as $key => $item){?>
					<li>
						<a hidefocus="true" href="<?php echo IUrl::creatUrl("".$item."");?>"><?php echo isset($key)?$key:"";?></a>
					</li>
					<?php }?>
				</ul>
			</div>
			<p><a href="<?php echo IUrl::creatUrl("/systemadmin/logout");?>">退出管理</a> <a href="<?php echo IUrl::creatUrl("/system/admin_repwd");?>">修改密码</a> <a href="<?php echo IUrl::creatUrl("/system/default");?>">后台首页</a> <a href="<?php echo IUrl::creatUrl("");?>" target='_blank'>商城首页</a> <span>您好 <label class='bold'><?php echo isset($this->admin['admin_name'])?$this->admin['admin_name']:"";?></label>，当前身份 <label class='bold'><?php echo isset($this->admin['admin_role_name'])?$this->admin['admin_role_name']:"";?></label></span></p>
		</div>
		<div id="info_bar">
			<label class="navindex"><a href="<?php echo IUrl::creatUrl("/system/navigation");?>">快速导航管理</a></label>
			<span class="nav_sec">
			<?php $adminId = $this->admin['admin_id']?>
			<?php $query = new IQuery("quick_naviga");$query->where = "admin_id = $adminId and is_del = 0";$items = $query->find(); foreach($items as $key => $item){?>
			<a href="<?php echo isset($item['url'])?$item['url']:"";?>" class="selected"><?php echo isset($item['naviga_name'])?$item['naviga_name']:"";?></a>
			<?php }?>
			</span>
		</div>

		<div id="admin_left">
			<ul class="submenu">
				<?php $leftMenu=menu::get($menuData,IWeb::$app->getController()->getId().'/'.IWeb::$app->getController()->getAction()->getId())?>
				<?php foreach(current($leftMenu) as $key => $item){?>
				<li>
					<span><?php echo isset($key)?$key:"";?></span>
					<ul name="leftMenu">
						<?php foreach($item as $leftKey => $leftValue){?>
						<li><a href="<?php echo IUrl::creatUrl("".$leftKey."");?>"><?php echo isset($leftValue)?$leftValue:"";?></a></li>
						<?php }?>
					</ul>
				</li>
				<?php }?>
			</ul>
			<div id="copyright"></div>
		</div>

		<div id="admin_right">
			<script type="text/javascript" charset="UTF-8" src="/runtime/_systemjs/my97date/wdatepicker.js"></script>
<div class="headbar">
	<div class="position"><span>工具</span><span>></span><span>广告管理</span><span>></span><span>更新广告</span></div>
</div>
<div class="content_box">
	<div class="content form_content">
		<form action='<?php echo IUrl::creatUrl("/tools/ad_edit_act");?>' method='post' name='ad' enctype='multipart/form-data'>
			<input type='hidden' name='id' value='' />
			<table class="form_table">
				<colgroup>
					<col width="150px" />
					<col />
				</colgroup>
				<tr>
					<th>说明：</th>
					<td>(1)先添加 <广告位> 数据；(2)再添加 <广告> 并且绑定之前添加的 <广告位>，广告数据才可以正常显示 </td>
				</tr>
				<tr>
					<th>名称：</th>
					<td>
						<input type='text' class='normal' name='name' pattern='required' alt="填写名称" />
						<label>*广告名称（必填）</label>
					</td>
				</tr>
				<tr>
					<th>广告展示类型：</th>
					<td>
						<label class='attr'><input type='radio' name='type' value='1' checked=checked />图片</label>
						<label class='attr'><input type='radio' name='type' value='2' />flash</label>
						<label class='attr'><input type='radio' name='type' value='3' />文字</label>
						<label class='attr'><input type='radio' name='type' value='4' />代码</label>

						<div id='ad_box'></div>
					</td>
				</tr>
				<tr>
					<th>广告位：</th>
					<td>
						<select name='position_id' class='normal' pattern='required' alt='广告位不能为空'>
							<option value=''>请选择</option>
							<?php $query = new IQuery("ad_position");$items = $query->find(); foreach($items as $key => $item){?>
							<option value='<?php echo isset($item['id'])?$item['id']:"";?>'><?php echo isset($item['name'])?$item['name']:"";?></option>
							<?php }?>
						</select>
						<label>*在选择的广告位置内进行展示（必选）</label>
					</td>
				</tr>
				<tr>
					<th>链接地址：</th>
					<td>
						<input type='text' class='normal' name='link' empty pattern='url' alt='请填写正确URL地址' />
						<label>点击广告后页面链接的URL地址，为空则不跳转</label>
					</td>
				</tr>
				<tr>
					<th>排序：</th>
					<td>
						<input type='text' class='small' name='order' pattern='int' alt='填写正确的数字' />
						<label>数字越小，排列越靠前</label>
					</td>
				</tr>

				<tr>
					<th>开始和结束时间：</th>
					<td>
						<input type='text' class='Wdate' onfocus='WdatePicker()' name='start_time' pattern='date' /> ～
						<input type='text' class='Wdate' onfocus='WdatePicker()' name='end_time' pattern='date' />
						<label>*广告展示的开始时间和结束时间（必选）</label>
					</td>
				</tr>
				<tr>
					<th>描述：</th>
					<td><textarea class='textarea' name='description' alt='请填写文章内容'></textarea></td>
				</tr>
				<tr>
					<th>绑定商品分类：</th>
					<td>
						<!--分类数据显示-->
						<span id="__categoryBox" style="margin-bottom:8px"></span>
						<button class="btn" type="button" name="_goodsCategoryButton"><span class="add">设置分类</span></button>
						<?php plugin::trigger('goodsCategoryWidget',array("name" => "goods_cat_id","value" => isset($this->adRow['goods_cat_id']) ? $this->adRow['goods_cat_id'] : ""))?>
						<label> 可选项，与商品分类做关联，与商品分类绑定在一起，动态的展示 </label>
					</td>
				</tr>

				<?php if($this->adRow && $this->adRow['position_id']){?>
				<?php $query = new IQuery("ad_position");$query->where = "id = {$this->adRow['position_id']}";$items = $query->find(); foreach($items as $key => $positionRow){?><?php }?>
				<tr>
					<th>代码：</th>
					<td style="font-weight:bold;color:#000;font-size:14px;">
						将以下代码Copy到你想要放置广告的任何模板中。 <a href="http://www.aircheng.com/movie" target="_blank">如何添加广告？</a><br />
						<code style="font-weight:normal;font-family:'Courier New';font-size:16px;display:block;background:#333;color:#fff;padding:5px;margin-right:30px;">
							<?php if($this->adRow['goods_cat_id']){?>
							<?php echo chr(123);?>echo:Ad::show("<?php echo isset($positionRow['name'])?$positionRow['name']:"";?>",绑定的商品分类ID)<?php echo chr(125);?>
							<?php }else{?>
							<?php echo chr(123);?>echo:Ad::show("<?php echo isset($positionRow['name'])?$positionRow['name']:"";?>")<?php echo chr(125);?>
							<?php }?>
						</code>
					</td>
				</tr>
				<?php }?>

				<tr>
					<th></th><td><button class='submit' type='submit'><span>确 定</span></button></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<!--广告内容模板-->
<script id="adTemplate" type="text/html">
<%if(newType == "1"){%>
	<input type="file" name="img" class="file" />
	<%if(newType == defaultType){%>
		<p><img src="<?php echo IUrl::creatUrl("")."";?><%=content%>" width="150px" /></p>
		<input type="hidden" name="content" value="<%=content%>" />
	<%}%>
<%}else if(newType == "2"){%>
	<input type="file" name="flash" class="file" />
	<%if(newType == defaultType){%>
		<embed src="<?php echo IUrl::creatUrl("")."";?><%=content%>" width="150px" type="application/x-shockwave-flash"></embed>
		<input type="hidden" name="content" value="<%=content%>" />
	<%}%>
<%}else if(newType == "3"){%>
	<input type="text" class="normal" name="content" value="<%=content%>" />
<%}else{%>
	<textarea class='textarea' name='content'><%=content%></textarea>
<%}%>
</script>

<script type='text/javascript'>
//广告数据
defaultAdRow = <?php echo JSON::encode($this->adRow);?>;

//切换广告类型 1:图片; 2:flash; 3:文字; 4:代码;
function changeType(typeVal)
{
	var content = (defaultAdRow && typeVal == defaultAdRow['type']) ? defaultAdRow['content'] : "";
	var defaultType = (defaultAdRow && defaultAdRow['type']) ? defaultAdRow['type'] : "";
	var adHtml = template.render('adTemplate',{'newType':typeVal,'defaultType':defaultType,'content':content});
	$('#ad_box').html(adHtml);
}

//表单回显
var FromObj = new Form('ad');
FromObj.init(defaultAdRow);

//单选按钮点击绑定
$('input:radio[name="type"]').each(
	function(i){
		$(this).bind("click",function()
		{
			changeType(i+1);
		});
	}
);

$("[name='type']:checked").trigger("click");
</script>
		</div>
	</div>

	<script type='text/javascript'>
	//隔行换色
	$(".list_table tr:nth-child(even)").addClass('even');
	$(".list_table tr").hover(
		function () {
			$(this).addClass("sel");
		},
		function () {
			$(this).removeClass("sel");
		}
	);

	//按钮高亮
	var topItem  = "<?php echo key($leftMenu);?>";
	$("ul[name='topMenu']>li:contains('"+topItem+"')").addClass("selected");

	var leftItem = "<?php echo IUrl::getUri();?>";
	$("ul[name='leftMenu']>li a[href^='"+leftItem+"']").parent().addClass("selected");
	</script>
</body>
</html>
