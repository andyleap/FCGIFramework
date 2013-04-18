<?php

include 'Framework.php';

$framework = new Framework(array('DBConnections' => array('development' => 'mysql://root:root@localhost/test')));
$framework->Router->AddRoute('/:action/:id', array('controller' => 'Blog', 'action' => 'view', 'id' => 0));

$server = new FCGI_Server();
while ($req = $server->Accept()) {
	$framework->Router->Route($req);
}