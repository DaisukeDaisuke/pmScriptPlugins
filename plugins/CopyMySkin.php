<?php


declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\entity\Human;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\entity\EntityFactory;
use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntityDataHelper as Helper;

/**
 * @name CopyMySkin
 * @api 5.31.0
 * @description Human
 * @version 1.0.0
 * @main CopyMySkin_MpqCz
 * @author DaisukeDaisuke
 */
class CopyMySkin_MpqCz extends PluginBase implements Listener{
    protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		EntityFactory::getInstance()->register(MyHuman::class, function(World $world, CompoundTag $nbt) : MyHuman{
			return new MyHuman(Helper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
		}, ['MyHuman']);
    }

	public function playerjoin(PlayerJoinEvent $event): void{
		$player = $event->getPlayer();
		$skin = $player->getSkin();
		$entity = new MyHuman($player->getLocation(), $skin, null);
		$entity->spawnToAll();
	}
}

class MyHuman extends Human{

}