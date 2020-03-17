<?php


namespace AdvanceQuest\command;

use pocketmine\command\{
	Command,
	CommandSender
};

use pocketmine\Player;

use AdvanceQuest\QuestLoader;

class QuestRewardCommand extends Command
{
	
	protected $plugin = null;
	
	public const PERMISSION = "op";
	
	
	public function __construct (QuestLoader $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct ("퀘스트보상", "퀘스트보상 명령어 입니다.");
		$this->setPermission (self::PERMISSION);
	}
	
	public function execute (CommandSender $player, string $label, array $args): bool
	{
		if ($player instanceof Player) {
			if ($player->hasPermission (self::PERMISSION)) {
				if (isset ($args [0]) and isset ($args [1]) and is_numeric ($args [1])) {
					if (isset (QuestLoader::$db ["quest"] [$args [0]])) {
						$item = $player->getInventory ()->getItemInHand ();
						if ($item->isNull ()) {
							QuestLoader::message ($player, "손에 아이템을 들고 명령어를 실행해주세요.");
							return true;
						}
						QuestLoader::$db ["quest"] [$args [0]] ["item"] [] = $item->getId () . ":" . $item->getDamage () . ":" . $args [1] . ":" . base64_encode ($item->getCompoundTag ());
						QuestLoader::message ($player, "추가 완료");
					} else {
						QuestLoader::message ($player, "존재하지 않는 퀘스트 입니다.");
					}
				} else {
					QuestLoader::message ($player, "/퀘스트보상 (퀘스트명) (수량)");
				}
			} else {
				QuestLoader::message ($player, "권한 없음.");
			}
		} else {
			QuestLoader::message ($player, "인게임에서만 사용이 가능합니다.");
		}
		return true;
	}
}