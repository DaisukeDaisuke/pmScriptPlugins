<?php


declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;

/**
 * @name EnableLocatorBar
 * @api 5.31.0
 * @description Re-enable the LocatorBar
 * @version 1.0.0
 * @main EnableLocatorBar_MpqCz
 * @author DaisukeDaisuke
 */
class EnableLocatorBar_MpqCz extends PluginBase implements Listener{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function pl(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$packet = GameRulesChangedPacket::create(
			[
				"locatorBar" => new BoolGameRule(true, false)
			]
		);
		$player->getNetworkSession()->sendDataPacket($packet);
	}
}