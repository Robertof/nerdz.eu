<?php
ob_start('ob_gzhandler');
require_once $_SERVER['DOCUMENT_ROOT'].'/class/autoload.php';
use NERDZ\Core\Config;

$core = new NERDZ\Core\Comments();

ob_start(array('NERDZ\\Core\\Core','minifyHtml'));

if(!$core->isLogged())
    die($core->lang('REGISTER'));
    
switch(isset($_GET['action']) ? strtolower($_GET['action']) : '')
{
    case 'show':
        $hpid  = isset($_POST['hpid']) && is_numeric($_POST['hpid']) ? $_POST['hpid']  : false;
        if(!$hpid )
            die($core->lang('ERROR'));
        $_list = null;
        if (isset ($_POST['start']) && isset ($_POST['num']) &&
            is_numeric ($_POST['start']) && is_numeric ($_POST['num']))
            $_list = $core->getLastComments ($hpid, $_POST['num'], $_POST['start']);
        else if (isset ($_POST['hcid']) && is_numeric ($_POST['hcid']))
            $_list = $core->getCommentsAfterHcid ($hpid, $_POST['hcid']);
        else
            $_list = $core->getComments ($hpid);
        $doShowForm = !isset ($_POST['hcid']) && (!isset ($_POST['start']) || $_POST['start'] == 0) && !isset ($_POST['forceNoForm']); 
        if (empty ($_list) && !$doShowForm)
            die();
        $vals = [];
        
        $vals['currentuserprofile_n'] = \NERDZ\Core\Core::userLink($core->getUsername());
        $vals['currentusergravatar_n'] = (new NERDZ\Core\Gravatar())->getURL($core->getUserId());
        $vals['currentusername_n'] = $core->getUsername();
        $vals['onerrorimgurl_n'] = Config\STATIC_DOMAIN.'/static/images/red_x.png';
        $vals['list_a'] = $_list;
        $vals['showform_b'] = $doShowForm;
        $vals['hpid_n'] = $hpid;
        $vals['commentcount_n'] = $core->countComments ($hpid);
        $vals['needmorebtn_b'] = $doShowForm && $vals['commentcount_n'] > 10;
        $vals['needeverycommentbtn_b'] = $doShowForm && $vals['commentcount_n'] > 20;
        $core->getTPL()->assign($vals);
        $core->getTPL()->draw('profile/comments');
    break;
default:
    die($core->lang('ERROR'));
break;
}
