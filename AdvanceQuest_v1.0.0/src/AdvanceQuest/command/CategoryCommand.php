<?php


namespace AdvanceQuest\command;

use pocketmine\command\{
	Command,
	CommandSender
};

use pocketmine\Player;
use pocketmine\entity\Entity;

use pocketmine\nbt\tag\{
	CompoundTag,
	StringTag,
	ByteArrayTag
};

use AdvanceQuest\QuestLoader;

class CategoryCommand extends Command
{
	
	/** @var null|QuestLoader */
	protected $plugin = null;
	
	public const PERMISSION = "op";
	
	
	public function __construct (QuestLoader $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct ("카테고리", "카테고리 명령어 입니다.");
		$this->setPermission (self::PERMISSION);
	}
	
	public function execute (CommandSender $player, string $label, array $args): bool
	{
		if ($player instanceof Player) {
			if ($player->hasPermission (self::PERMISSION)) {
				switch ($args [0] ?? "x") {
					case "추가":
						if (isset ($args [1])) {
							if (!isset (QuestLoader::$db ["category"] [$args [1]])) {
								QuestLoader::$db ["category"] [$args [1]] = true;
								QuestLoader::message ($player, "§a{$args [1]}§r§7 퀘스트 카테고리를 생성하셨습니다.");
							} else {
								QuestLoader::message ($player, "이미 존재하는 카테고리 입니다.");
							}
						} else {
							QuestLoader::message ($player, "/카테고리 추가 (카테고리명)");
						}
						break;
					case "제거":
						if (isset ($args [1])) {
							if (isset (QuestLoader::$db ["category"] [$args [1]])) {
								unset (QuestLoader::$db ["category"] [$args [1]]);
								$this->deleteQuestNPC ($args [1]);
								QuestLoader::message ($player, "§a{$args [1]}§r§7 퀘스트 카테고리를 제거하셨습니다.");
							} else {
								QuestLoader::message ($player, "이미 존재하지 않은 카테고리 입니다.");
							}
						} else {
							QuestLoader::message ($player, "/카테고리 제거 (카테고리명)");
						}
						break;
					case "소환":
						if (isset ($args [1])) {
							if (isset (QuestLoader::$db ["category"] [$args [1]])) {
								$pos = $player;
								$nbt = Entity::createBaseNBT ($pos->asVector3 (), null, $player->yaw, $player->pitch);
								$nbt->setTag (new CompoundTag ("Skin", [
									new StringTag ("Name", $player->getSkin ()->getSkinId ()),
									new ByteArrayTag ("Data", $player->getSkin ()->getSkinData ())
								]));
								$nbt->setString ("category", $args [1]);
								$entity = Entity::createEntity ("QuestHuman", $player->level, $nbt);
								$entity->setNameTag ("§l§f[§bQ§f] {$args [1]}");
								$entity->spawnToAll ();
								QuestLoader::message ($player, "§a{$args [1]}§r§7 카테고리 퀘스트 엔피시를 소환했습니다.");
							} else {
								QuestLoader::message ($player, "존재하지 않는 카테고리 입니다.");
							}
						} else {
							QuestLoader::message ($player, "/카테고리 소환 (카테고리명)");
						}
						break;
					default:
						QuestLoader::message ($player, "/카테고리 추가 (카테고리) - 카테고리를 추가합니다.");
						QuestLoader::message ($player, "/카테고리 제거 (카테고리) - 카테고리를 제거합니다.");
						QuestLoader::message ($player, "/카테고리 소환 (카테고리) - 카테고리NPC를 소환합니다.");
						break;
				}
			} else {
				QuestLoader::message ($player, "당신은 이 명령어를 사용할 권한이 없습니다.");
			}
		} else {
			QuestLoader::mesaage ($player, "인게임에서만 사용이 가능합니다.");
		}
		return true;
	}
}