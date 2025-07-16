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
 * @name CspHandler
 * @api 5.30.0
 * @description csp counter plugin for PocketMine-MP
 * @version 1.0.0
 * @main CspHandler_MpqCz
 * @author DaisukeDaisuke
 */
class CspHandler_MpqCz extends PluginBase implements Listener{

	/* The interval at which this plugin broadcasts messages */
	private const SETTING_INTERVAL = 5;
	private const SETTING_ANTI_TAPPING_TOOL = 19;
	private const SETTING_CPS_MESSAGE = TextFormat::DARK_GREEN."CSP: ";
	private const SETTING_CPS_INVALID_MESSAGE = TextFormat::DARK_GRAY."CSP: ";

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
						if(count($this->cpsData[$playerName]) >= self::SETTING_ANTI_TAPPING_TOOL){
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
		$name = $player->getName();

		if($packet instanceof InventoryTransactionPacket&&$packet->trData instanceof UseItemOnEntityTransactionData&&($packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK)){
			if($this->processCPS($player)){
				$event->cancel();
			}
		}
	}

	private function processCPS(Player $player): bool{
		$this->cpsData[$player->getName()][] = microtime(true);
		return count($this->cpsData[$player->getName()]) >= self::SETTING_ANTI_TAPPING_TOOL;
	}
}