<?php

include 'Framework.php';

$framework = new Framework();
$framework->Router->AddRoute('/:name', array('controller' => 'Test', 'action' => 'Test'));

$server = new FCGI_Server();
while ($req = $server->Accept()) {
	$framework->Router->Route($req);
}