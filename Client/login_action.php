<?php
error_reporting(E_ALL^E_NOTICE);
    header("Content-Type: text/html; charset=utf8");
# 获取当前完整URL

if (isset($_GET['encas_st'])){
	# 如果登录成功 得到了ENCAS_ST
	
	$st_from_encas = $_GET['encas_st'];
	include('./conn.php');//链接数据库
	@mysql_connect("localhost","encommunity","your_password_here")or die;    //链接数据库
	@mysql_select_db("encommunity")or die;    //选择数据库
	$rs = mysql_query("select encas_server_token from en_user_auth where encas_server_token='$st_from_encas'");
	while($row = mysql_fetch_array($rs))
       //已登录前提下的ENCAS可用性辨别开始
       if($row['encas_server_token'] == $st_from_encas){
		   # 与数据库中ST比对完成，签发Token
		   echo "Login success, redirecting. / 登陆成功，正在重定向";
		   //$web_id = $_GET["enc_web_id"]; //获取完整的来路URL
		   header("Location: https://account.ensonyan.com/home/");
		   exit;
	   } else if (!isset($row['encas_server_token']) == '$st_from_encas') {
		   echo "登陆失败，测试环境暂时无法使用";
		   exit;
	   }
	   
} else {
	//$current_url_original = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$src=$_GET['src'];
	$src_encoded=urlencode($src);
	$encas_callback='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$encas_callback_encoded=urlencode($encas_callback);
	header("Location: https://passport.ensonyan.com/encom_main/encas/login/index.php?src=$src_encoded&encas_callback=$encas_callback_encoded");
    exit;
}
?>