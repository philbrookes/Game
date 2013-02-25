<?php
namespace Outer\View\MagicFunctions\Exposed;
/**
 * Description of abExposed
 *
 * @author philip
 */

use Core\Model\Utility\registry;
use Core\View\MagicFunctions\exposedFunction;

class tellPlayer implements exposedFunction{
    public function process($args){
        $playerId = $args[0];
        $msg = $args[1];
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
