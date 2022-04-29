<?php
//解密$key
//urldecode($key);
//把当前站点的sessionid设置为传递的sessionid

# 参数：src=最初的URL, encas_st=最终与数据库核验代码, sessid=session传参
$encas_sessid=$_GET['sessid'];
session_id($encas_sessid);
session_start();

$encas_cli_login_request_src_url=$_GET['src'];
$src_final=$encas_cli_login_request_src_url;

$encas_cli_login_request_encas_callback_url=$_GET['encas_callback'];
$encas_callback_final=$encas_cli_login_request_encas_callback_url;

$encas_st=$_GET['encas_st'];
$enc_web_id=$_GET['enc_web_id'];

$encas_callback_final_domain_raw=parse_url($encas_callback_final);
$encas_callback_final_domain=$encas_callback_final_domain_raw['host'];

/**if ($enc_web_id == 'encom_main'){$redir_domain='www.ensonyan.com';}
else if ($enc_web_id == 'yan_net_cn') {$redir_domain='www.yan.net.cn';}
else if ($enc_web_id == 'vms_main') {$redir_domain='volunteer.ensonyan.com';}
else if ($enc_web_id == 'ssl_encom') {$redir_domain='ssl.ensonyan.com';}
else {exit("Illegal Parameter Detected! / 非法参数！");
		header("Refresh:2;url=https://www.ensonyan.com/?rsn=ipd");
		exit;
		}**/

echo "
     <script>
         setTimeout(function(){window.location.href='https://$encas_callback_final_domain/encas_client/login_action.php?src=$src_final&encas_callback=$encas_callback_final&encas_st=$encas_st&enc_web_id=$enc_web_id&sessid=$encas_sessid';},0);
     </script>";

exit;
?>