<?php
ob_start('ob_gzhandler');
require_once $_SERVER['DOCUMENT_ROOT'].'/class/Notification.class.php';
$core = new Notification();

if($core->isLogged())
    die($core->jsonResponse('ok',$core->countPms()));

die($core->jsonResponse('error',$core->lang('ERROR')));
?>
