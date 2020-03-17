<?php


namespace AdvanceQuest;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

use AdvanceQuest\form\QuestUI;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class EventListener implements Listener
{
	
	protected $plugin;
	
	
	
	public function __construct (QuestLoader $plugin)
	{
		$this->plugin = $plugin;
	}
	
	public function handlePacket (DataPacketReceiveEvent $event): void
	{
		$player = $event->getPlayer ();
		if (($packet = $event->getPacket ()) instanceof ModalFormResponsePacket) {
			if (($class = QuestUI::getFormById ($packet->formId)) !== null) {
				$class::handleReceive ($player, $packet->formData);
			}
		}
	}
	
	public function onJoin (PlayerJoinEvent $event): void
	{
		$player = $event->getPlayer ();
		$name = $player->getName ();
		if (!isset (QuestLoader::$db ["player"] [$name])) {
			QuestLoader::$db ["player"] [$name] = [
				"clear" => []
			];
			QuestLoader::$db ["player"] [$name] ["clear"] ["첫접속"] = [
				"category" => "튜토리얼",
				"date" => date ("Y년 m월 d일 h시 i분 s초")
			];
		}
	}
	
	
}