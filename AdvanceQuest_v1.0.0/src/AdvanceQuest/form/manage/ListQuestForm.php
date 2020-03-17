<?php


namespace AdvanceQuest\form\manage;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

use AdvanceQuest\QuestLoader;
use AdvanceQuest\form\QuestUI;
use AdvanceQuest\util\QuestType;

class ListQuestForm extends QuestUI
{
	
	public static function getFormId (): int
	{
		return 8989895;
	}
	
	public static function sendForm (Player $player): bool
	{
		$packet = new ModalFormRequestPacket ();
		$packet->formId = self::getFormId ();
		
		$buttons = "\n";
		foreach (QuestLoader::$db ["quest"] as $questName => $arr) {
			$buttons .= "§f퀘스트: §b" . $questName . "§r§f\n";
		}
		
		$packet->formData = json_encode ([
			"type" => "custom_form",
			"title" => "§l퀘스트",
			"content" => [
				[ "type" => "label", "text" => $buttons ]
		]
		]);
		return $player->sendDataPacket ($packet);
	}
	
	public static function handleReceive (Player $player, $data): bool
	{
		return true;
	}
}