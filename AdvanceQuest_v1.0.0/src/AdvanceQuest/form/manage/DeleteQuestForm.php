<?php



namespace AdvanceQuest\form\manage;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

use AdvanceQuest\QuestLoader;
use AdvanceQuest\form\QuestUI;
use AdvanceQuest\util\QuestType;

class DeleteQuestForm extends QuestUI
{
	
	
	public static function getFormId (): int
	{
		return 8989894;
	}
	
	public static function sendForm (Player $player): bool
	{
		$packet = new ModalFormRequestPacket ();
		$packet->formId = self::getFormId ();
		
		$buttons = [];
		foreach (QuestLoader::$db ["quest"] as $questName => $arr) {
			$buttons [] = [ "text" => "- " . $questName ];
		}
		
		$packet->formData = json_encode ([
			"type" => "form",
			"title" => "§l퀘스트",
			"content" => "\n§f제거하실 퀘스트를 터치해주세요.",
			"buttons" => $buttons
		]);
		return $player->sendDataPacket ($packet);
	}
	
	public static function handleReceive (Player $player, $data): bool
	{
		$res = json_decode ($data, true);
		
		if (is_null ($res)) {
			return false;
		}
		
		$buttons = [];
		foreach (QuestLoader::$db ["quest"] as $questName => $arr) {
			$buttons [] = $questName;
		}
		if (isset ($buttons [$res])) {
			unset (QuestLoader::$db ["quest"] [$buttons [$res]]);
			QuestLoader::message ($player, "§a{$buttons [$res]}§r§7 퀘스트를 삭제하셨습니다.");
		}
		return true;
	}
}