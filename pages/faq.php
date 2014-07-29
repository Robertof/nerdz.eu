<?php
$lang = $core->isLogged() ? $core->getUserLanguage($_SESSION['id']) : $core->getBrowserLanguage();

$vals = [];
$vals['faq_n'] = file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/tpl/{$core->getTemplate()}/langs/{$lang}/faq.html");

require_once $_SERVER['DOCUMENT_ROOT'].'/pages/common/vars.php';
$core->getTPL()->assign($vals);
$core->getTPL()->draw('base/faq');
?>
