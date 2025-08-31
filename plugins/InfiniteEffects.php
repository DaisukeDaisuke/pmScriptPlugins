<?php


declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\MobEffectPacket;

/**
 * @name InfiniteEffects
 * @api 5.30.0
 * @description Implement an infinite effect. Just use \pocketmine\utils\Limits::INT32_MAX as the effect duration!
 * @version 1.0.0
 * @main InfiniteEffects_MpqCz
 * @author DaisukeDaisuke
 */
class InfiniteEffects_MpqCz extends PluginBase implements Listener{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function Data(DataPacketSendEvent $event) : void{
		$packets = $event->getPackets();
		$changed = false;
		foreach($packets as $key => $packet){
			if($packet instanceof MobEffectPacket && $packet->duration >= 630720000){//1 year
				$packets[$key] = MobEffectPacket::create(
					actorRuntimeId: $packet->actorRuntimeId,
					eventId: $packet->eventId,
					effectId: $packet->effectId,
					amplifier: $packet->amplifier,
					particles: $packet->particles,
					duration: -1,
					tick: $packet->tick,
				);
				$changed = true;
			}
		}
		if($changed){
			$event->setPackets($packets);
		}
	}
}