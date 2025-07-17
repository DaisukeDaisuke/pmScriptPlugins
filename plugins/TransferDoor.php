<?php

declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerItemUseEvent;

/**
 * @name TransferDoor
 * @api 5.30.0
 * @description Transfer door plugin for PocketMine-MP
 * @version 1.0.0
 * @main TransferDoor_MpqCz
 * @author DaisukeDaisuke
 */
class TransferDoor_MpqCz extends PluginBase implements Listener{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onInteract(PlayerInteractEvent $event) : void{
		$player = $event->getPlayer();
		$item = $event->getItem();

		if($item->equals(VanillaBlocks::OAK_DOOR()->asItem(), false, false)){
			$this->onTransferDoor($player);
			$event->cancel();
		}
	}

	public function onItemUse(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		$item = $event->getItem();

		if($item->equals(VanillaBlocks::OAK_DOOR()->asItem(), false, false)){
			$this->onTransferDoor($player);
			$event->cancel();
		}
	}

	public function onTransferDoor(Player $player) : void{
		$player->transfer("194.87.217.160", 5000, "Transfère vers le Lobby en cours...");
	}
}
