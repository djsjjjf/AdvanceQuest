<?php


namespace AdvanceQuest\form;

use pocketmine\Player;


use AdvanceQuest\form\player\QuestDrawingForm;

use AdvanceQuest\form\manage\CreateQuestForm;
use AdvanceQuest\form\manage\DeleteQuestForm;
use AdvanceQuest\form\manage\ListQuestForm;
use AdvanceQuest\form\manage\ModifyQuestForm;

abstract class QuestUI
{
	
	private static $forms = [];
	
	
	abstract public static function getFormId (): int;
	
	abstract public static function sendForm (Player $player): bool;
	
	abstract public static function handleReceive (Player $player, $data): bool;
	
	
	public static function init (): void
	{
		self::$forms [QuestMenuForm::getFormId ()] = QuestMenuForm::class;
		self::$forms [QuestDrawingForm::getFormId ()] = QuestDrawingForm::class;
		self::$forms [CreateQuestForm::getFormId ()] = CreateQuestForm::class;
		self::$forms [DeleteQuestForm::getFormId ()] = DeleteQuestForm::class;
		self::$forms [ListQuestForm::getFormId ()] = ListQuestForm::class;
		self::$forms [ModifyQuestForm::getFormId ()] = ModifyQuestForm::class;
	}
	
	public static function getFormById (int $id): ?string
	{
		return isset (self::$forms [$id]) ? self::$forms [$id] : null;
	}
}