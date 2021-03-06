#!/usr/bin/php
<?php
if(empty($argv[1]) || empty($argv[2]))
    die("{$argv[0]} \"<document root>\" \"<base url>\"\n");

$configFile = $argv[1].'/class/autoload.php';

if(!is_readable($configFile))
    die("{$configFile} is not readable\n");

define('DOCUMENT_ROOT',"{$argv[1]}/");
$base_url = $argv[2];

require_once $configFile;

$db = NERDZ\Core\Db::getDb();

try
{
	$r = $db->query('SELECT counter,username FROM users WHERE private IS FALSE');
    $urls = "<?xml version='1.0' encoding='UTF-8'?>\n
            <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";

	while(($u = $r->fetch(PDO::FETCH_OBJ)))
	{
		$r1 = $db->query('SELECT pid,DATE("time") AS date FROM posts WHERE "to" = '.$u->counter);

		while(($m = $r1->fetch(PDO::FETCH_OBJ)))
            $urls.= "<url>\n
                        <loc>{$base_url}/{$u->username}.{$m->pid}</loc>\n
                        <lastmod>{$m->date}</lastmod>\n
                    </url>\n";

        $urls.="<url>\n
                    <loc>{$base_url}/{$u->username}.</loc>\n
                </url>\n";
	}

	$r = $db->query('SELECT counter,name FROM groups WHERE private IS FALSE');
	
	while(($u = $r->fetch(PDO::FETCH_OBJ)))
	{
		$r1 = $db->query('SELECT pid,DATE("time") AS date FROM groups_posts WHERE "to" = '.$u->counter);

		while(($m = $r1->fetch(PDO::FETCH_OBJ)))
            $urls.="<url>\n
                        <loc>{$base_url}/{$u->name}:{$m->pid}</loc>\n
                        <lastmod>".$m->date."</lastmod>\n
                    </url>\n";

        $urls.="<url>\n
                    <loc>{$base_url}/{$u->name}:</loc>\n
                </url>\n";
	}

	$r = $db->query('SELECT username FROM users WHERE private IS TRUE');

	while(($u = $r->fetch(PDO::FETCH_OBJ)))
        $urls.="<url>\n
                    <loc>{$base_url}/{$u->username}.</loc>\n
                </url>\n";

	$r = $db->query('SELECT name FROM groups WHERE private IS TRUE');

	while(($u = $r->fetch(PDO::FETCH_OBJ)))
        $urls.="<url>\n
                    <loc>{$base_url}/{$u->name}:</loc>\n
                </url>\n";
}
catch(PDOException $ex)
{
	die($ex->getMessage());
}

$urls.='</urlset>';

file_put_contents(DOCUMENT_ROOT.'data/sitemap.xml',$urls);
chmod(DOCUMENT_ROOT.'data/sitemap.xml',0755);
