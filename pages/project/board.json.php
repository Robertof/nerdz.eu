<?php
ob_start('ob_gzhandler');
require_once $_SERVER['DOCUMENT_ROOT'].'/class/Messages.class.php';
$core = new Messages();

if(!$core->isLogged())
    die($core->jsonResponse('error',$core->lang('REGISTER')));

if(!$core->refererControl())
    die($core->jsonResponse('error','CSRF'));
    
switch(isset($_GET['action']) ? strtolower($_GET['action']) : '')
{
    case 'add':
        
        if(empty($_POST['to']))
            die($core->jsonResponse('error',$core->lang('ERROR').'a'));

        die($core->jsonDbResponse(
            $core->addMessage(
                $_POST['to'],
                isset($_POST['message']) ? $_POST['message'] : '',
                [
                    'news' => isset($_POST['news']),
                    'project' => true
                ]))
            );
    break;
    
    case 'del':
        if(!isset($_SESSION['delpost']) || empty($_POST['hpid']) || ($_SESSION['delpost'] != $_POST['hpid']) || !$core->deleteMessage($_POST['hpid'], true))
            die($core->jsonResponse('error',$core->lang('ERROR')));
        unset($_SESSION['delpost']);
    break;

    case 'delconfirm':
        $_SESSION['delpost'] = isset($_POST['hpid']) ? $_POST['hpid'] : -1;
        die($core->jsonResponse('ok',$core->lang('ARE_YOU_SURE')));
    break;
    
    case 'get':
        if(
            empty($_POST['hpid']) ||
            !($o = $core->getMessage($_POST['hpid'], true))
          )
            die($core->jsonResponse('error',$core->lang('ERROR').'2'));
    break;
    
    case 'edit':
        if(
            empty($_POST['hpid']) || empty($_POST['message']) ||
            !$core->editMessage($_POST['hpid'],$_POST['message'], true)
          )
            die($core->jsonResponse('error',$core->lang('ERROR')));
    break;

    default:
        die($core->jsonResponse('error',$core->lang('ERROR').'aa'));
    break;
}
die($core->jsonResponse('ok', isset($o) ? $o->message : 'OK'));
?>
