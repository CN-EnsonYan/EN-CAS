<head>
    <title>鉴权中 - Authenticating</title>
    <meta name="theme-color" content="#5B9BD5">
</head>
<?php
$src_in_url_1step=$_GET['src'];
$src_in_url=urldecode($src_in_url_1step);

$encas_callback_in_url_1step=$_GET['encas_callback'];
$encas_callback_in_url=urldecode($encas_callback_in_url_1step);
//$src='http://www.ensonyan.com/index.html';
$check_enc_web=parse_url($src_in_url);
$enc_web_check_result=$check_enc_web['host'];
//echo $enc_web_check_result;
//输出结果类似 www.ensonyan.com

//function getRootDomain($url='$src_in_url'){
//                //$url=$url?$url:$_SERVER['HTTP_HOST']; (不提供URL则查询当前主机)
//                if(preg_match('%^[\d\.]$%',$url)) return;
//                if(preg_match('%[^:\.\/]+(?:(?<ext>\.(?:com|net|org|edu|gov|biz|tv|me|pro|name|cc|co|info|cm))|(?<ctr>\.(?:cn|us|hk|tw|uk|it|fr|br|in|de))|\k<ext>\k<ctr>)+$%i',$url,$match)){
//                        return $match[0];
//                }
//                return;
//        }
//        $filtered_result=var_dump(getRootDomain());

$enc_web_list = array("www.ensonyan.com","ensonyan.com","passport.ensonyan.com","volunteer.ensonyan.com","verify.volunteer.ensonyan.com","docs.ensonyan.com","search.ensonyan.com","cloud.home.ensonyan.com","community.ensonyan.com","www.yan.net.cn","yan.net.cn","www.hi-res.com.cn","hi-res.com.cn","www.ecdn.ltd","ecdn.ltd","search.ecdn.ltd","ssl.ensonyan.com");
if(in_array("$enc_web_list",$enc_web_check_result)){
// 属于 ENC 官方站点
    $is_it_enc="yes";
}else{
    $is_it_enc="no";
}
?>
<?php
ini_set('display_errors', 0);
header("Content-Type: text/html; charset=utf8");
//reCaptcha
if(isset($_POST['g-recaptcha-response'])){
        $captcha=$_POST['g-recaptcha-response'];
    }
    else
        $captcha = false;

    if(!$captcha){
        echo '<script type="text/javascript">alert("Anti-bot Detecting Failed ! Please wait for the page to load completely and click login. / 人机核验失败！请等待页面完全加载后点击登录。Error Type: tp-google-recap-t1-nip");history.go(-1);</script>';
        exit;
		die;
    }
    else{
        $secret = 'secret';
        $response=file_get_contents("https://www.recaptcha.net/recaptcha/api/siteverify?secret=$secret&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
        if($response.success==false)
        {
            //Do something with error
            echo '<script type="text/javascript">alert("Anti-bot Detecting Failed ! Please wait for the page to load completely and click login. / 人机核验失败！请等待页面完全加载后点击登录。Error Type: tp-google-recap-t1-int");history.go(-1);</script>';
        exit;
		die;
        }
    }
// The Captcha is valid you can continue with the rest of your code. reCaptcha验证流程结束，以下为ENCAS流程

    if(!isset($_POST["submit_det"])){//submit_det 来自上一个页面的Form
        exit("Unauthorized Access! / 非法访问！");
		header("Refresh:2;url=https://passport.ensonyan.com/encom_main/encas/login/index.php?type=sc&stat=uaa");
    }//检测是否有submit操作 
 
    function safeStrings($str) {
      return str_replace("'","\\'",str_replace("\\","\\\\",(string)$str));
    }//安全字符串检测函数

include('../configs/conn.php');//链接数据库

if(isset($_SESSION['logged'])){
	@mysql_connect("localhost","your_database","your_password_here")or die;    //链接数据库
	@mysql_select_db("your_database")or die;    //选择数据库
	$rs = mysql_query("select status from en_website_config where alias='encas_main'");
	while($row = mysql_fetch_array($rs))
               
               //已登录前提下的ENCAS可用性辨别开始
               if($row['status'] = 'running'){
                echo "<script>alert('你已经登录，请勿重复登录！');</script>";
				echo "
					<script>
						setTimeout(function(){window.location.href='https://www.ensonyan.com/index.php?type=al&ref=encas_main_lgin&token=FoqXwKt0T59jDXrP';},100);
					</script>";
				mysql_close();//关闭数据库
				exit;
              } else if ($row['status'] = 'maintenance') {
                session_unset();//free all session variable
                session_destroy();//销毁一个会话中的全部数据
                setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
                echo "<script>alert('System under maintenance, try again later! / 系统维护中，请待维护完成后登录，您暂时被强制下线！');</script>";
                echo "
                    <script>
                       history.go(-1);
                    </script>";//返回上一页
				mysql_close();//关闭数据库
				exit;
             } else if ($row['status'] = 'stopped') {
				session_unset();//free all session variable
                session_destroy();//销毁一个会话中的全部数据
                setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
                echo "<script>alert('ENCAS stopped, try again later! / 系统暂停中，请关注ENC官方发布的最新动态，您暂时被强制下线！');</script>";
                echo "
                    <script>
                       history.go(-1);
                    </script>";//返回上一页
				mysql_close();//关闭数据库
				exit;
			 }
			 
			 //已登录前提下的ENCAS可用性辨别结束
    } else {

		@mysql_connect("localhost","your_database","your_password_here")or die;    //链接数据库
		@mysql_select_db("your_database")or die;    //选择数据库
		$rs = mysql_query("select status from en_website_config where alias='encas_main'");
		while($row = mysql_fetch_array($rs))
               
            //未登录前提下的ENCAS可用性辨别开始
               if($row['status'] === 'running'){
					session_start();
					setcookie("session_id",session_id(),time()+3600*24*365*10,"/",".ensonyan.com");
              } else if ($row['status'] === 'maintenance') {
                echo "<script>alert('System under maintenance, try again later! / 系统维护中，请待维护完成后登录！');</script>";
                echo "
                    <script>
                       history.go(-1);
                    </script>";//返回上一页
				mysql_close();//关闭数据库
				exit;
             } else if ($row['status'] === 'stopped') {
                echo "<script>alert('ENCAS stopped, try again later! / 系统暂停中，请关注ENC官方发布的最新动态');</script>";
                echo "
                    <script>
                       history.go(-1);
                    </script>";//返回上一页
			 mysql_close();//关闭数据库
			 exit;
			 }
			 
			//未登录前提下的ENCAS可用性辨别结束

    }

    $e_email = safeStrings($_POST['entry_email']);//post获得用户邮箱表单值
    $e_password = safeStrings($_POST['entry_password']);//post获得用户密码单值
    
if ($e_email && $e_password){    //如果用户名和密码都不为空==============================================================================================
@mysql_connect("localhost","your_database","your_password_here")or die;    //链接数据库
@mysql_select_db("your_database")or die;    //选择数据库
$query = @mysql_query("select * from en_user_auth")or die;    //查询‘en_user_auth’表中的所有记录

$n=0;
while ($row = mysql_fetch_array($query))    //遍历‘works’表中的数据，并形成数组

             $sql = "select * from en_user_auth where email = '$e_email' and password='$e_password'";//检测数据库是否有对应的username和password的sql
             $result = mysql_query($sql);//执行sql
             $rows=mysql_num_rows($result);//返回一个数值
             if($rows){//0 false 1 true
			 //===========================================================================================以下为账号密码核实正确后的操作
			
				$rs = mysql_query("select * from en_user_auth where email='$e_email'");
				//echo "查询信息如下：";
				while($row = mysql_fetch_array($rs))
               
               //帐号状态辨别开始
               if($row['account_status'] == 'enabled'){
					$_SESSION['encas_user_login_status']='logged';
					$_SESSION['e-mail']=$e_email;
					$encas_sessionid = session_id();

					//$safe_key = urlencode($sessionid);//生成安全码
					//输出一个登陆成功提示页，并跳转到请求登陆的站点
					//echo "<iframe width='0' height='0' src='https://www.ensonyan.com/encas_client/set_cookie.php?sessid=$encas_sessionid'></iframe>";
					//sleep(1);
              } else if ($row['account_status'] == 'validating') {
				  session_unset();//free all session variable
                  session_destroy();//销毁一个会话中的全部数据
                  setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
				  echo '<script type="text/javascript">alert("Account Validating, please check your E-Mail or register again to get a new activation link. / 账号待激活，请检查您的邮箱或重新注册以获得激活链接。");history.go(-1);</script>';
				  exit;
			  } else if ($_SESSION['account_status'] == "banned") {
				  session_unset();//free all session variable
                  session_destroy();//销毁一个会话中的全部数据
                  setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
				  echo '<script type="text/javascript">alert("Account banned, click to check details ! / 账号封禁中，点击查看更多细节！");history.go(-1);</script>';
				  exit;
			 } else if ($_SESSION['account_status'] == "disabled") {
				  session_unset();//free all session variable
                  session_destroy();//销毁一个会话中的全部数据
                  setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
				  echo '<script type="text/javascript">alert("Account has been disabled! / 账号停用中！");history.go(-1);</script>';
				  exit;
			 }
               //帐号状态辨别结束

			     function randomkeys($length){
					$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';  
					for($i=0;$i<$length;$i++)   
						{   
						$key .= $pattern{mt_rand(0,35)};    //生成php随机数   
						}   
					return $key;
				}
				$encas_st = randomkeys(12);
                error_reporting(0);
				# 写入 ENCAS ST Token
				$sql_st = "update en_user_auth set encas_server_token='$encas_st' where email = '$e_email'";//数据库写入ST
				$result_st = mysql_query($sql_st);//执行sql
				//$encas_cli_login_request_src_url=$_GET['src'];
				
				// 如果URL中不含参数 则默认ENCOM主站
				if (isset($_GET['src'])){
				    if ($is_it_enc=="yes"){
				        $encas_cli_login_request_src_url=$_GET['src'];
				    } else {
				        $encas_cli_login_request_src_url="https%3A%2F%2Fwww.yan.net.cn%2Fencas_client%2Flogin_action.php%2F%3Fsrc%3Dhttps%253A%252F%252Fwww.ensonyan.com%252F%26";
				    }
				    
				} else {
				    $encas_cli_login_request_src_url="https%3A%2F%2Fwww.ensonyan.com%2Fencas_client%2Flogin_action.php%2F%3Fsrc%3Dhttps%253A%252F%252Fwww.ensonyan.com%252F%26";
				}
				$final_src=urlencode($encas_cli_login_request_src_url);
				$final_encas_callback=urlencode($encas_callback_in_url);
				
				if (isset($_GET['enc_web_id'])){
				    $enc_web_id=$_GET['enc_web_id'];
				} else {
				    $enc_web_id="encom_main";
				}
				
				# 写入 ENCAS PHP Session
				$sql_esess = "update en_user_auth set encas_php_session='$encas_sessionid' where email = '$e_email'";//数据库写入PHP Session
				mysql_query($sql_esess);//执行sql
				
				# 参数：src=最初的URL, encas_st=最终与数据库核验代码, sessid=session传参
				echo "
                    <script>
                            setTimeout(function(){window.location.href='https://passport.ensonyan.com/encom_main/encas/encom_action/encas_client/set_cookie.php?src=$final_src&encas_callback=$final_encas_callback&encas_st=$encas_st&enc_web_id=$enc_web_id&sessid=$encas_sessionid';},0);
                    </script>";

                //echo "
                 //   <script>
                 //           setTimeout(function(){window.location.href='、encas_cli_login_request_src_url?encas_st=、encas_st';},0);
                 //   </script>";
                 mysql_close();//关闭数据库
				 exit;
             }else{
				  session_unset();//free all session variable
                  session_destroy();//销毁一个会话中的全部数据
                  setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
                echo "<script>alert('Incorrect username or password, please try again! / 用户名或密码错误，请重试！');history.go(-1);</script>";
                //如果错误使用js 1秒后跳转到登录页面重试，DEF=1000; setTimeout(function(){window.location.href='../index.php?type=wp&ref=encas_main_lgin&token=FoqXwKt0T59jDXrP';},100);
             }
             

    }else{    //如果用户名或密码有空
				  session_unset();//free all session variable
                  session_destroy();//销毁一个会话中的全部数据
                  setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
                echo "<script>alert('Please fill in all the blanks! Try again. / 用户名或密码为空，请重新输入！');history.go(-1);</script>";
                        //如果错误使用js 1秒后跳转到登录页面重试;
    }

    mysql_close();//关闭数据库
?>