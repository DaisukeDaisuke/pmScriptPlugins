<?php

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\ItemFrame;


/**
 * @name NeverDisappearingItemsInFrame
 * @api 5.31.0
 * @description Items do not disappear when you break an item frame!
 * @version 1.0.0
 * @main NeverDisappearingItemsInFrame_MpqCz
 * @author DaisukeDaisuke
 */
class NeverDisappearingItemsInFrame_MpqCz extends PluginBase implements Listener{
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @priority MONITOR
	 */
	public function onBlockBreak(BlockBreakEvent $event): void{
		$block = $event->getBlock();
		$player = $event->getPlayer();
		if($block instanceof ItemFrame && $player->isCreative()){
			$item = $block->getFramedItem();
			if($item !== null){
				$block->getPosition()->getWorld()->dropItem($block->getPosition(), $item);
			}
		}
	}

}