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
        echo "running tellPlayer with...\n";
        print_r($args);
        $playerId = $arg[0];
        $msg = $arg[1];
        $players = registry::getObject("players");
        foreach($players as $player){
            if($player->getId() == $playerId){
                $player->sendData($msg);
                break;
            }
        }
    }
}

?>
