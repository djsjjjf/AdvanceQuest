<?php


namespace AdvanceQuest;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use AdvanceQuest\command\{
	CategoryCommand,
	QuestCommand,
 QuestRewardCommand
};
use AdvanceQuest\form\QuestUI;
use AdvanceQuest\entity\QuestHuman;
use pocketmine\entity\Entity;

class QuestLoader extends PluginBase
{
	
	private static $instance = null;
	
	public static $prefix = "§l§b[퀘스트]§r§7 ";
	
	public static $config = [], $db = [];
	
	public static $mode = [];
	
	
	public static function runFunction (): QuestLoader
	{
		return self::$instance;
	}
	
	public function onLoad (): void
	{
	   date_default_timezone_set('Asia/Seoul');
		if (self::$instance === null) {
			self::$instance = $this;
		}
		if (!file_exists ($this->getDataFolder ())) {
			@mkdir ($this->getDataFolder ());
		}
		self::$config ["category"] = new Config ($this->getDataFolder () . "category.yml", Config::YAML);
		self::$db ["category"] = self::$config ["category"]->getAll ();
		self::$config ["quest"] = new Config ($this->getDataFolder () . "quest.yml", Config::YAML);
		self::$db ["quest"] = self::$config ["quest"]->getAll ();
		self::$config ["player"] = new Config ($this->getDataFolder () . "player.yml", Config::YAML);
		self::$db ["player"] = self::$config ["player"]->getAll ();
	}
	
	public function onEnable (): void
	{
		$this->getServer ()->getCommandMap ()->registerAll ("avas", [
			new CategoryCommand ($this),
			new QuestCommand ($this),
   new QuestRewardCommand ($this)
		]);
		QuestUI::init ();
		$this->getServer ()->getPluginManager ()->registerEvents (new EventListener ($this), $this);
		Entity::registerEntity (QuestHuman::class, true);
	}
	
	public function onDisable (): void
	{
		foreach ([
			"category", "quest", "player"
		] as $data) {
			if (self::$config [$data] instanceof Config) {
				self::$config [$data]->setAll (self::$db [$data]);
				self::$config [$data]->save ();
			}
		}
	}
	
	public function getData (string $data): array
	{
		return self::$db [$data];
	}
	
	public static function message ($player, string $msg): void
	{
		$player->sendMessage (self::$prefix . $msg);
	}
}