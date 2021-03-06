<?php
ob_start('ob_gzhandler');
require_once $_SERVER['DOCUMENT_ROOT'].'/class/autoload.php';
ob_start(array('NERDZ\\Core\\Utils','minifyHTML'));

use NERDZ\Core\Comments;
use NERDZ\Core\Messages;
use NERDZ\Core\Gravatar;
use NERDZ\Core\System;
use NERDZ\Core\User;

$prj = isset($prj);
$user = new User();
$comments = new Comments();

if(!$user->isLogged())
    die($user->lang('REGISTER'));

switch(isset($_GET['action']) ? strtolower($_GET['action']) : '')
{
case 'get':
    $hcid  = isset($_POST['hcid']) && is_numeric($_POST['hcid']) ? $_POST['hcid']  : false;
    if(!$hcid)
        die($user->lang('ERROR').': no hcid');

    $vals = [];
    $vals['list_a']          = $comments->get($hcid, $prj);
    $vals['showform_b']      = false;
    $vals['needmorebtn_b']   = false;
    $vals['commentcount_n']  = 0;
    $vals['hpid_n']          = 0;
    $vals['onerrorimgurl_n'] = System::getResourceDomain().'/static/images/red_x.png';

    $user->getTPL()->assign($vals);
    $user->getTPL()->draw(($prj ? 'project' : 'profile'). '/comments');
    break;

case 'show':
    $hpid  = isset($_POST['hpid']) && is_numeric($_POST['hpid']) ? $_POST['hpid']  : false;
    if(!$hpid )
        die($user->lang('ERROR').': no hpid');
    $_list = null;
    if (isset ($_POST['start']) && isset ($_POST['num']) &&
        is_numeric ($_POST['start']) && is_numeric ($_POST['num']))
        $_list = $comments->getLastComments ($hpid, $_POST['num'], $_POST['start'], $prj);
    else if (isset ($_POST['hcid']) && is_numeric ($_POST['hcid']))
        $_list = $comments->getCommentsAfterHcid ($hpid, $_POST['hcid'], $prj);
    else
        $_list = $comments->getAll($hpid, $prj);

    $doShowForm = !isset ($_POST['hcid']) && (!isset ($_POST['start']) || $_POST['start'] == 0) && !isset ($_POST['forceNoForm']);

    if (empty ($_list) && !$doShowForm)
        die();

    $vals = [];       
    $vals['currentuserprofile_n'] = \NERDZ\Core\Utils::userLink($_SESSION['id']);
    $vals['currentusergravatar_n'] = $user->getGravatar($_SESSION['id']);
    $vals['currentusername_n'] = User::getUsername();
    $vals['onerrorimgurl_n'] = System::getResourceDomain().'/static/images/red_x.png';
    $vals['list_a'] = $_list;
    $vals['showform_b'] = $doShowForm;
    $vals['hpid_n'] = $hpid;
    $vals['commentcount_n'] = (new Messages())->countComments($hpid, $prj);
    $vals['needmorebtn_b'] = $doShowForm && $vals['commentcount_n'] > 10;
    $vals['needeverycommentbtn_b'] = $doShowForm && $vals['commentcount_n'] > 20; 

    $user->getTPL()->assign($vals);
    $user->getTPL()->draw(($prj ? 'project' : 'profile'). '/comments');
    break;
default:
    die($user->lang('ERROR'));
    break;
}
