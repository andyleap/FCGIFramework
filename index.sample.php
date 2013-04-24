<?php

include 'Framework.php';

$framework = new Framework(array('DBConnections' => array('development' => 'mysql://root:root@localhost/test')));
$framework->Router->AddRoute('/:controller/:action', array());

$server = new FCGI_Server();
while ($req = $server->Accept()) {
	$framework->Router->Route($req);
}