<?php

declare(strict_types=1);

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\Server;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\AvailableEnchantmentRegistry;

/**
 * @name UnregisterEnchantmentPlugin
 * @api 5.30.0
 * @description Let's enable Vibrant Visuals!
 * @version 1.0.0
 * @main UnregisterEnchantment_MpqCz
 */
class UnregisterEnchantment_MpqCz extends PluginBase {

	protected function onEnable() : void {
		$this->saveDefaultConfig();
		$this->getServer()->getCommandMap()->register("unregisterenchantment", new UnregisterEnchantment_Command_MpqCz($this, "unenc", "Unregister or remove enchantments", "/unenc <name|remove>", []));

		$config = $this->getConfig();
		foreach ($this->getList() as $index => $enc) {
			$name = $this->translatable($enc->getName());
			if ($config->get(strtolower($name), null) !== null) {
				$this->unregister($index, $name, false);
			}
		}
	}

	/**
	 * @return Enchantment[]
	 * @phpstan-return array<int, Enchantment>
	 */
	public function getList() : array {
		$map = EnchantmentIdMap::getInstance();
		return (fn() => $this->idToEnum)->bindTo($map, $map)();
	}

	public function unregister(int $id, string $name, bool $save) : void {
		$map = EnchantmentIdMap::getInstance();
		$enchantment = (function() use ($id) : ?Enchantment {
			$enc = $this->idToEnum[$id] ?? null;
			unset($this->idToEnum[$id]);
			return $enc;
		})->bindTo($map, $map)();

		if ($enchantment === null) return;

		$name = strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($name)));
		$parser = StringToEnchantmentParser::getInstance();

		$refClass = new ReflectionClass(get_parent_class($parser));
		$property = $refClass->getProperty("callbackMap");
		$property->setAccessible(true);

		$callbackMap = $property->getValue($parser);
		unset($callbackMap[$name]);
		$property->setValue($parser, $callbackMap);

		AvailableEnchantmentRegistry::getInstance()->unregister($enchantment);

		if ($save) {
			$this->getConfig()->set($name, true);
			$this->getConfig()->save();
		}
	}

	public function remove(string $name) : bool {
		$name = strtolower($name);
		if (!$this->getConfig()->exists($name)) {
			return false;
		}
		$this->getConfig()->remove($name);
		$this->getConfig()->save();
		return true;
	}

	public function getAll() : array{
		return $this->getConfig()->getAll();
	}

	public function translatable(Translatable|string|int $name) : string|int {
		return $name instanceof Translatable
			? Server::getInstance()->getLanguage()->translate($name)
			: $name;
	}
}

class UnregisterEnchantment_Command_MpqCz extends Command {

	public function __construct(private UnregisterEnchantment_MpqCz $main, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []) {
		$this->setPermission(DefaultPermissionNames::BROADCAST_ADMIN);
		parent::__construct($name, $description, $usageMessage, $aliases);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
		$cmd = $args[0] ?? null;
		if ($cmd === null) {
			foreach ($this->main->getList() as $enc) {
				$name = $this->main->translatable($enc->getName());
				$sender->sendMessage("§a" . $name);
			}
			return false;
		}

		if (str_starts_with("remove", strtolower($cmd))) {
			$name = $args[1] ?? null;
			if ($name === null) {
				foreach($this->main->getAll() as $index => $item){
					$sender->sendMessage("remove: $index");
				}
				$sender->sendMessage("/$commandLabel remove <name>");
				return true;
			}

			$result = [];
			foreach($this->main->getAll() as $item => $true){
				if(str_starts_with(strtolower($item), strtolower($name))){
					$result[] = $item;
				}
			}

			if(count($result) === 0){
				$sender->sendMessage("remove: §c$item not found in config.");
				return true;
			}
			if(count($result) === 1){
				$item = $result[array_key_first($result)];
				if ($this->main->remove($item)) {
					$sender->sendMessage("§a$item removed from config. Will be effective on next restart.");
				} else {
					$sender->sendMessage("remove: §c$item not found in config.");
				}
				return true;
			}

			foreach($result as $item){
				$sender->sendMessage("remove: $item");
			}

			return false;
		}

		$result = [];
		foreach ($this->main->getList() as $index => $enc) {
			$name = $this->main->translatable($enc->getName());
			if (str_starts_with(strtolower($name), strtolower($cmd))) {
				$result[] = [$index, $name];
			}
		}

		if (count($result) === 0) {
			$sender->sendMessage("Enchantment $cmd not found");
			return false;
		}

		if (count($result) === 1) {
			[$index, $enc1] = $result[0];
			$sender->sendMessage("Unregistering enchantment $enc1");
			$this->main->unregister($index, $enc1, true);
			return true;
		}

		foreach ($result as [$index, $enc1]) {
			$sender->sendMessage("$enc1 ($index)");
		}

		return true;
	}
}
