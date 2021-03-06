<?php
ob_start('ob_gzhandler');
require_once $_SERVER['DOCUMENT_ROOT'].'/class/autoload.php';
use NERDZ\Core\Pms;
use NERDZ\Core\User;
ob_start(array('NERDZ\\Core\\Utils','minifyHTML'));

$pms = new Pms();
$user = new User();

if(!$user->isLogged())
    die($user->lang('REGISTER'));

switch(isset($_GET['action']) ? trim(strtolower($_GET['action'])) : '')
{
case 'conversation':
    $from = isset($_POST['from']) && is_numeric($_POST['from']) ? $_POST['from'] : false;
    $to   = isset($_POST['to']) && is_numeric($_POST['to']) ? $_POST['to'] : false;

    if (!$from || !$to || !in_array ($_SESSION['id'], array ($from, $to)))
        die($user->lang('ERROR'));

    $conv = null;
    if (isset ($_POST['start']) && isset ($_POST['num']) && is_numeric ($_POST['start']) && is_numeric ($_POST['num']))
        $conv = $pms->readConversation ($from, $to, false, $_POST['num'], $_POST['start']);
    else if (isset ($_POST['pmid']) && is_numeric ($_POST['pmid']))
        $conv = $pms->readConversation ($from, $to, $_POST['pmid']);
    else
        $conv = $pms->readConversation ($from, $to);
    $doShowForm = !isset ($_POST['pmid']) && (!isset ($_POST['start']) || $_POST['start'] == 0) && !isset ($_POST['forceNoForm']);
    if (!$doShowForm && empty ($conv))
        die();
    $vals['toid_n'] = ( $_SESSION['id'] != $to ? $to : $from );
    $vals['to_n'] = User::getUsername ($vals['toid_n']);
    if (!$vals['to_n']) die ($user->lang ('ERROR'));
    $vals['list_a'] = $conv;
    $vals['pmcount_n'] = $pms->count ($from, $to);
    $vals['needmorebtn_b'] = $doShowForm && $vals['pmcount_n'] > 10;
    $vals['needeverymsgbtn_b'] = $doShowForm && $vals['pmcount_n'] > 20;
    $vals['showform_b'] = $doShowForm;
    $user->getTPL()->assign($vals);
    $user->getTPL()->draw('pm/conversation');
    break;
default:
    die($user->lang('ERROR'));
    break;
}
