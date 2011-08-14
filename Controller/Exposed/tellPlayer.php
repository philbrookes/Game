<?php
namespace Controller\Exposed;
/**
 * Description of abExposed
 *
 * @author philip
 */

use Model\Utility\registry;

class tellPlayer {
    public function process($args){
        $playerId = $arg[0];
        $msg = $arg[1];
        $players = registry::getObject("players");
        foreach($players as $player){
            echo $player->getId()." == ".$playerId."\n";
            if($player->getId() == $playerId){
                echo "sending $msg to ".$player->getName()."\n";
                $player->sendData($msg);
                break;
            }
        }
    }
}

?>
