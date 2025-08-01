<?php
declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;

/**
 * @name CpsHandler
 * @api 5.30.0
 * @description Cps counter plugin for PocketMine-MP
 * @version 1.0.0
 * @main CpsHandler_MpqCz
 * @author DaisukeDaisuke
 */
class CpsHandler_MpqCz extends PluginBase implements Listener{

	/* The interval at which this plugin broadcasts messages */
	private const SETTING_INTERVAL = 5;
	/* Whether the anti-tapping tool is enabled */
	private const IS_ENABLED_ANTI_TAPPING_TOOL = true;
	/* The number of clicks per second that triggers the anti-tapping tool */
	private const SETTING_ANTI_TAPPING_TOOL = 19;
	/* Messages to be displayed in the jukebox popup */
	private const SETTING_CPS_MESSAGE = TextFormat::DARK_GREEN."CPS: ";
	/* Message when anti-tapping responds */
	private const SETTING_CPS_INVALID_MESSAGE = TextFormat::DARK_GRAY."CPS: ";

	/** @var array<string, list<float>> */
	private array $cpsData = [];

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
			$currentTime = microtime(true);
			foreach($this->cpsData as $playerName => $timestamps){
				$this->cpsData[$playerName] = array_filter($timestamps, static fn($timestamp) => ($currentTime - $timestamp) <= 1);

				// データが古い場合は削除
				if(empty($this->cpsData[$playerName])){
					unset($this->cpsData[$playerName]);
				}

				$player = Server::getInstance()->getPlayerExact($playerName);
				if($player instanceof Player&&$player->isOnline()){
					if(isset($this->cpsData[$playerName])){
						if(!self::IS_ENABLED_ANTI_TAPPING_TOOL||count($this->cpsData[$playerName]) <= self::SETTING_ANTI_TAPPING_TOOL){
							$player->sendJukeboxPopup(self::SETTING_CPS_MESSAGE.count($this->cpsData[$playerName]));
						}else{
							$player->sendJukeboxPopup(self::SETTING_CPS_INVALID_MESSAGE.count($this->cpsData[$playerName]));
						}
					}
				}
			}
		}), self::SETTING_INTERVAL);
	}

	public function onPlayerMissSwing(PlayerMissSwingEvent $event) : void{
		$player = $event->getPlayer();
		$this->processCPS($player);
	}


	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$session = $event->getOrigin();
		$player = $session->getPlayer();

		if($player === null){
			return;
		}
		$packet = $event->getPacket();

		if($packet instanceof InventoryTransactionPacket&&$packet->trData instanceof UseItemOnEntityTransactionData&&($packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK)){
			if($this->processCPS($player)&&self::IS_ENABLED_ANTI_TAPPING_TOOL){
				$event->cancel();
			}
		}
	}

	private function processCPS(Player $player) : bool{
		$this->cpsData[$player->getName()][] = microtime(true);
		if(self::SETTING_ANTI_TAPPING_TOOL <= 10){
			return (count($this->cpsData[$player->getName()]) - 2) >= self::SETTING_ANTI_TAPPING_TOOL; //There seems to be some errors
		}
		return count($this->cpsData[$player->getName()]) >= self::SETTING_ANTI_TAPPING_TOOL;
	}
}