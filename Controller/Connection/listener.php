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
	for ($i = 0; $i < $max_clients; $i++){
		if ($clients[$i]!= false){
			$input = trim($clients[$i]->getData());
			if ($input == false){
				continue;
			}
			if ($input == 'exit') {
				echo "received exit command, closing socket... \n";
				$clients[$i]->close();
				$clients[$i] = false;
			}else if($input == "serveroff"){
				echo "received serveroff command, closing server!\n";
				break;
			} elseif ($input) {
				echo "received input... $input\n";
				$output = ereg_replace("[\n\r]","",$input)."\n".chr(0);
				for ($j = 0; $j < $max_clients; $j++){
					echo "alerting other users...\n";
					if($clients[$j] != false){
						if($i != $j){
							echo "sending '".$i." said: ".$output."' to client $j\n";
							$clients[$j]->write($i ." said: ".$output);
						}else{
							echo "sending 'You said: ".$output."' to client $j\n";
							$clients[$j]->write("You said: ".$output);
						}
					}
				}
			}
		}
	}
	if(isset($input) && $input == "serveroff"){
		echo "serveroff found, killing server\n";
		break;
	}
}
echo "closing all client connections...\n";
for ($j = 0; $j < $max_clients; $j++){
	if($clients[$j] != false){
		$clients[$j]->close();
	}
}
echo "connections closed!\n";

echo "closing listener\n";
$listenSock->close();
echo "================================\nserver finished...\n";
