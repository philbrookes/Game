<?php
use Model\Networking\socket;

set_time_limit (0);
$max_clients = 10;

$listenSock = new socket('95.154.246.7', "10100");
for($i=0;$i<$max_clients;$i++)$clients[$i] = false;
$listenSock->open();
while (true) {  
	for ($i = 0; $i < $max_clients; $i++){
		if ($clients[$i] == false) {
			$tmp = new socket();
			if(!$tmp->accept($listenSock->getSock())){
				break;
			}
			echo "accepting connection to client: $i\n";
			$clients[$i] = $tmp;
			$clients[$i]->write("welcome to our server!");
		}
		elseif ($i == $max_clients - 1){ 
			print ("no empty connections\n");
		}
	}
}