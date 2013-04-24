<?php

include 'Framework.php';

$framework = new Framework(array('DBConnections' => array('development' => 'mysql://root:root@localhost/test')));
$framework->Router->AddRoute('/:page#0-9#', array('controller' => 'Blog', 'action' => 'index', 'page' => 1));
$framework->Router->AddRoute('/:action#a-z#/:id#0-9#', array('controller' => 'Blog', 'id' => 1));
$framework->Router->AddRoute('/add', array('controller' => 'Blog', 'action' => 'add'));

$server = new FCGI_Server();
while ($req = $server->Accept()) {
	$framework->Router->Route($req);
}