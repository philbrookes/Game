<?php
namespace Outer\View\MagicFunctions\Exposed;
/**
 *
 * @author peter
 */

use Core\Model\Utility\registry;
use Core\View\MagicFunctions\exposedFunction;

class tellAllOtherPlayers implements exposedFunction{
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
