<?php



namespace AdvanceQuest\form\manage;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

use AdvanceQuest\QuestLoader;
use AdvanceQuest\form\QuestUI;
use AdvanceQuest\util\QuestType;

class CreateQuestForm extends QuestUI
{
	
	
	public static function getFormId (): int
	{
		return 8989893;
	}
	
	public static function sendForm (Player $player): bool
	{
		$packet = new ModalFormRequestPacket ();
		$packet->formId = self::getFormId ();
		
		$category = [];
		foreach (QuestLoader::$db ["category"] as $name => $bool) {
			$category [] = "- " . $name;
		}
		$types = [];
		foreach (QuestTYPE::getQuests () as $quest) {
			$types [] = "- " . $quest;
		}
		
		$packet->formData = json_encode ([
			"type" => "custom_form",
			"title" => "§l퀘스트",
			"content" => [
				[
					"type" => "input",
					"text" => "- 퀘스트 이름을 적어주세요."
				],
				[
					"type" => "dropdown",
					"text" => "- 퀘스트 카테고리를 설정해주세요.",
					"options" => $category
				],
				[
					"type" => "dropdown",
					"text" => "- 퀘스트 타입을 설정해주세요.",
					"options" => $types
				]
			]
		]);
		return $player->sendDataPacket ($packet);
	}
	
	public static function handleReceive (Player $player, $data): bool
	{
		$res = json_decode ($data, true);
		
		if ($res [0] === null || $res [1] === null || $res [2] === null) {
			QuestLoader::message ($player, "퀘스트 생성 양식이 잘못되었습니다.");
			return true;
		} else {
			if (!isset (QuestLoader::$db ["quest"] [$res [0]])) {
				$category = [];
				foreach (QuestLoader::$db ["category"] as $name => $bool) {
					$category [] = $name;
				}
				QuestLoader::$db ["quest"] [$res [0]] = [
					"category" => $category [intval ($res [1])],
					"type" => QuestType::convertQuestType ((int) $res [2]),
					"clear" => []
				];
				QuestLoader::message ($player, "§a{$res [0]}§r§7 퀘스트 을(를) 생성하셨습니다.");
				QuestLoader::$mode [$player->getName ()] = [
					"quest" => $res [0],
					"category" => $category [intval ($res [1])],
					"type" => QuestType::convertQuestType ((int) $res [2])
				];
				return ModifyQuestForm::sendForm ($player);
			} else {
				QuestLoader::message ($player, "이미 존재하는 퀘스트 입니다.");
				return true;
			}
		}
		return true;
	}
}