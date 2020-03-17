<?php


namespace AdvanceQuest\util;



class QuestType
{
	
	
	public const QUEST_TYPE_MINER = 0;
	
	public const QUEST_TYPE_FARMER = 1;
	
	public const QUEST_TYPE_INVEN_ITEM = 2;
	
	public const QUEST_TYPE_ORIGIN_HUNTER = 3;
	
	public const QUEST_TYPE_DUNGEON_HUNTER = 4;
	
	public const QUEST_TYPE_PVP_KILL = 5;
	
	public const QUEST_TYPE_PVP_DEATH = 6;
	
	public const QUEST_TYPE_FOUND_EGG = 7;
	
	public const QUEST_TYPE_INPUT_COMMAND = 8;
	
	
	
	public static function convertQuestType (int $type = -1): string
	{
		switch ($type) {
			case self::QUEST_TYPE_MINER:
				return "광질 하기";
			case self::QUEST_TYPE_FARMER:
				return "농사 하기";
			case self::QUEST_TYPE_INVEN_ITEM:
				return "아이템 가져오기";
			case self::QUEST_TYPE_ORIGIN_HUNTER:
				return "일반 몬스터 잡기";
			case self::QUEST_TYPE_DUNGEON_HUNTER:
				return "던전 몬스터 잡기";
			case self::QUEST_TYPE_PVP_KILL:
				return "킬 하기";
			case self::QUEST_TYPE_PVP_DEATH:
				return "데스 하기";
			case self::QUEST_TYPE_FOUND_EGG:
				return "이스터에그 찾기";
			case self::QUEST_TYPE_INPUT_COMMAND:
				return "명령어 입력하기";
			default:
				return "알 수 없음";
		}
	}
	
	public static function getQuests (): array
	{
		return [
			"광질 하기",
			"농사 하기",
			"아이템 가져오기",
			"일반 몬스터 잡기",
			"던전 몬스터 잡기",
			"킬 하기",
			"데스 하기",
			"이스터에그 찾기",
			"명령어 입력하기"
		];
	}
}