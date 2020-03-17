<?php


namespace AdvanceQuest\form;

use pocketmine\Player;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

use AdvanceQuest\QuestLoader;

use AdvanceQuest\form\player\QuestDrawingForm;

use AdvanceQuest\form\manage\CreateQuestForm;
use AdvanceQuest\form\manage\DeleteQuestForm;
use AdvanceQuest\form\manage\ListQuestForm;
use AdvanceQuest\form\manage\reward\QuestRewardManageForm;

class QuestMenuForm extends QuestUI
{
	
	
	public static function getFormId (): int
	{
		return 8989891;
	}
	
	public static function sendForm (Player $player): bool
	{
		$packet = new ModalFormRequestPacket ();
		$packet->formId = self::getFormId ();
		
		$buttons = [];
		$buttons [] = [ "text" => "§l팝업 닫기\n§r§8현재 창을 닫습니다." ];
		$buttons [] = [ "text" => "§l퀘스트 도감§r\n§8클리어한 퀘스트를 봅니다." ];
		$buttons [] = [ "text" => "§l퀘스트 클리어§r\n§8진행중인 퀘스트를 클리어 합니다." ];
		
		if ($player->isOp ()) {
			$buttons [] = [ "text" => "§l퀘스트 추가§r\n§8퀘스트를 추가합니다." ];
			$buttons [] = [ "text" => "§l퀘스트 삭제§r\n§8퀘스트를 삭제합니다." ];
			$buttons [] = [ "text" => "§l퀘스트 목록§r\n§8퀘스트 목록을 봅니다." ];
			$buttons [] = [ "text" => "§l퀘스트 보상관리§r\n§8퀘스트 보상을 관리합니다." ];
		}
		
		$packet->formData = json_encode ([
			"type" => "form",
			"title" => "§l퀘스트",
			"content" => "\n§f원하시는 버튼을 눌러주세요!",
			"buttons" => $buttons
		]);
		return $player->sendDataPacket ($packet);
	}
	
	public static function handleReceive (Player $player, $data): bool
	{
		$res = json_decode ($data, true);
		
		if (is_null ($res)) {
			return true;
		}
		
		switch ($res) {
			case 1:
				return QuestDrawingForm::sendForm ($player);
			case 2:
				if (isset (QuestLoader::$db ["player"] [$player->getName ()] ["nowQuest"])) {
					$category = QuestLoader::$db ["quest"] [QuestLoader::$db ["player"] [$player->getName ()] ["nowQuest"]] ["category"];
					QuestLoader::message ($player, '현재 진행중인 퀘스트는 §a" §l§f[§bQ§f]§r§f ' . $category . '§r§a "§7 을(를) 찾아가시면 됩니다.');
				} else {
					QuestLoader::message ($player, "현재 진행중인 퀘스트가 없습니다.");
				}
				return true;
		}
		
		if ($player->isOp ()) {
			switch ($res) {
				case 3:
					return CreateQuestForm::sendForm ($player);
				case 4:
					return DeleteQuestForm::sendForm ($player);
				case 5:
					return ListQuestForm::sendForm ($player);
				//case 6:
					//return QuestRewardManageForm::sendForm ($player);
			}
		}
		return true;
	}
}