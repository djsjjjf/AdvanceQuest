<?php


namespace AdvanceQuest\form\player;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

use AdvanceQuest\QuestLoader;
use AdvanceQuest\form\QuestUI;

class QuestDrawingForm extends QuestUI
{
	
	
	public static function getFormId (): int
	{
		return 8989892;
	}
	
	public static function sendForm (Player $player): bool
	{
		$packet = new ModalFormRequestPacket ();
		$packet->formId = self::getFormId ();
		
		$content = "\n";
		foreach (QuestLoader::$db ["player"] [$player->getName ()] ["clear"] as $questName => $arr) {
			$content .= "§f || 퀘스트 이름: §b" . $questName . "§r\n";
			$content .= "§f || 퀘스트 카테고리: §b" . $arr ["category"] . "§r\n";
			$content .= "§f || 퀘스트 클리어 날짜: §b" . $arr ["date"] . "§r\n\n";
		}
		
		$packet->formData = json_encode ([
			"type" => "custom_form",
			"title" => "§l퀘스트 도감",
			"content" => [
				[
					"type" => "label",
					"text" => "\n§f현재 당신이 클리어한 퀘스트는 §b" . count (QuestLoader::$db ["player"] [$player->getName ()] ["clear"]) . "개§f 입니다.\n"
				],
				[
					"type" => "label",
					"text" => $content
				]
			]
		]);
		return $player->sendDataPacket ($packet);
	}
	
	public static function handleReceive (Player $player, $data): bool
	{
		return true;
	}
}