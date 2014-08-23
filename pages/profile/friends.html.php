<?php
if(!isset($id, $user))
    die('$id & user required');
require_once $_SERVER['DOCUMENT_ROOT'].'/class/autoload.php';
use NERDZ\Core\Db;

$users = $user->getFriends($id);
$type = 'friends';
$dateExtractor = function($friendId) use ($id,$user) {
    $profileId = $id;
    $since = Db::query(
        [
            'SELECT EXTRACT(EPOCH FROM T.cc) AS time
            FROM (
                    SELECT MAX("time") AS cc FROM "followers"
                    WHERE ("from" = :id AND "to" = :fid) OR ("from" = :fid AND "to" = :id)
                ) AS T',
            [
                ':id' => $profileId,
                ':fid' => $friendId
            ]
        ],Db::FETCH_OBJ);
     if(!$since) {
        $since = new StdClass();
        $since->time = 0;
    }
    return $user->getDateTime($since->time);
};

return require $_SERVER['DOCUMENT_ROOT'].'/pages/common/userslist.html.php';
?>
