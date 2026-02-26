<?php


declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\network\mcpe\protocol\UpdateClientOptionsPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\PacketViolationWarningPacket;
use pocketmine\event\server\DataPacketDecodeEvent;

/**
 * @name FixInvMenu
 * @api 5.41.0
 * @description All Invmenus on the server will be updated to 5.41.0
 * @version 1.0.0
 * @main FixInvMenu_MpqCz
 * @author DaisukeDaisuke
 */
class FixInvMenu_MpqCz extends PluginBase implements Listener{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public array $list = [
		NetworkStackLatencyPacket::NETWORK_ID => true,
		ContainerClosePacket::NETWORK_ID => true,
		PacketViolationWarningPacket::NETWORK_ID => true,
	];

	/**
	 * @handleCancelled
	 */
	public function packetdecode(DataPacketDecodeEvent $event) : void{
		if(isset($this->list[$event->getPacketId()])){
			$event->uncancel();
		}
	}
}
