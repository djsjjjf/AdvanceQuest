<?php

namespace AdvanceQuest\command;

use pocketmine\command\{
	Command,
	CommandSender
};

use pocketmine\Player;

use AdvanceQuest\QuestLoader;
use AdvanceQuest\form\QuestMenuForm;

class QuestCommand extends Command
{
	
	/** @var null|QuestLoader */
	protected $plugin = null;
	
	
	public function __construct (QuestLoader $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct ("퀘스트", "퀘스트 명령어 입니다.");
	}
	
	public function execute (CommandSender $player, string $label, array $args): bool
	{
		if ($player instanceof Player) {
			QuestMenuForm::sendForm ($player);
		} else {
			QuestLoader::message ($player, "인게임에서만 사용이 가능합니다.");
		}
		return true;
	}
}