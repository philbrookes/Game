<?php
namespace Controller\Exposed;
/**
 * Description of abExposed
 *
 * @author peter
 */

use Model\Utility\registry;

class tellAllOtherPlayers {
    public function process($args){
        $playerId = $args[0];
        $msg = $args[1];
        $players = registry::getObject("players");
        foreach($players as $player){
            if($player->getId() != $playerId){
                $player->sendData($msg);
                break;
            }
        }
    }
}

?>
