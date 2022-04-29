<?php
$logout_trace_id=$_GET['logout_trace_id'];
$enc_web_id=$_GET['enc_web_id'];
//if ($logout_trace_id == '1'){
    session_start();
    header('Content-type:text/html;charset=utf-8');
//    if(isset($_SESSION['entry_name']) && $_SESSION['logged']==='yes'){
            session_unset();//free all session variable
            session_destroy();//销毁一个会话中的全部数据
            setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
            if ($enc_web_id=='encom_main'){
                header('location:https://www.ensonyan.com');
            } else if ($enc_web_id=='yan_net_cn'){
                header('location:https://www.yan.net.cn');
            } else {
                header('location:https://www.yan.net.cn');
            }
//        }else{
//            header('location:/apps/vms/account/logout/failed.php?ref=https%3A%2F%2Fvolunteer.ensonyan.com%2Faccount%2Flogout%2Flogout.php?token//=FoqXwKt0T59jDXrP');
     //   }
//} else {
//    header('location:https://passport.ensonyan.com/encom_main/encas/logout/logout.php');
//}
?>