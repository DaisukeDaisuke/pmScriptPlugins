<?php

use pocketmine\plugin\PluginBase;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\event\EventPriority;

/**
 * @name EnableVibrantVisualsPlugin
 * @api 5.30.0
 * @description Let's enable Vibrant Visuals!
 * @version 1.21.92
 * @main EnableVibrantVisualsPlugin_MpqCz
 * @author DaisukeDaisuke
 */
class EnableVibrantVisualsPlugin_MpqCz extends PluginBase{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvent(DataPacketSendEvent::class, function(DataPacketSendEvent $event) : void{
			$array = $event->getPackets();
			foreach($array as $key => $packet){
				if($packet instanceof ResourcePacksInfoPacket){
					\Closure::bind(fn() => ($this->forceDisableVibrantVisuals = false), $packet, $packet)();
				}
			}
		}, EventPriority::NORMAL, $this, false);
	}
}
