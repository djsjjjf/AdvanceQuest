<?php


namespace AdvanceQuest\form\manage;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

use AdvanceQuest\QuestLoader;
use AdvanceQuest\form\QuestUI;
use AdvanceQuest\util\QuestType;

class ModifyQuestForm extends QuestUI
{
	
	public static function getFormId (): int
	{
		return 8989896;
	}
	
	public static function sendForm (Player $player): bool
	{
		if (isset (QuestLoader::$mode [$player->getName ()])) {
			$data = QuestLoader::$mode [$player->getName ()];
			
			$packet = new ModalFormRequestPacket ();
			$packet->formId = self::getFormId ();
			
			$content = "광질 횟수를 설정해주세요.";
			if ($data ["type"] === "농사 하기") {
				$content = "농사 횟수를 설정해주세요.";
			} else if ($data ["type"] === "아이템 가져오기") {
				$content = "가져올 아이템 코드를 적어주세요. (id:meta:count)";
			} else if ($data ["type"] === "일반 몬스터 잡기") {
				$content = "몬스터 세부 설정을 적어주세요. (네트워크ID:횟수)";
			} else if ($data ["type"] === "던전 몬스터 잡기") {
				$content = "몬스터 세부 설정을 적어주세요. (던전몹 이름:횟수)";
			} else if ($data ["type"] === "킬 하기") {
				$content = "킬 횟수를 적어주세요.";
			} else if ($data ["type"] === "데스 하기") {
				$content = "데스 횟수를 적어주세요.";
			} else if ($data ["type"] === "이스터에그 찾기") {
				$content = "이스터에그 횟수를 적어주세요.";
			} else if ($data ["type"] === "명령어 입력하기") {
				$content = "명령어를 적어주세요.";
			}
			
			$packet->formData = json_encode ([
				"type" => "custom_form",
				"title" => "§l{$data ["quest"]} 퀘스트 설정",
				"content" => [
					[
						"type" => "input",
						"text" => $content
					],
					[ "type" => "input", "text" => "- 보상 경험치를 적어주세요." ]
				]
			]);
			return $player->sendDataPacket ($packet);
		} else {
			return true;
		}
	}
	
	public static function handleReceive (Player $player, $data): bool
	{
		$res = json_decode ($data, true);
		
		if ($res [0] === null or $res [1] === null) {
			return self::sendForm ($player);
		} else {
			if (isset (QuestLoader::$mode [$player->getName ()])) {
				$data = QuestLoader::$mode [$player->getName ()];
				QuestLoader::$db ["quest"] [$data ["quest"]] ["value"] = intval ($res [0]);
				QuestLoader::$db ["quest"] [$data ["quest"]] ["exp"] = intval ($res [1]);
				QuestLoader::$db ["quest"] [$data ["quest"]] ["item"] = [];
				QuestLoader::message ($player, "설정을 완료하셨습니다.");
			} else {
				QuestLoader::message ($player, "퀘스트 실패");
			}
		}
		return true;
	}
}