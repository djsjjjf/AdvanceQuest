<?php


namespace AdvanceQuest\entity;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Server;
use pocketmine\Player;

use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent
};

use pocketmine\entity\Human;

use CustomUI\CustomUI;
use AdvanceQuest\QuestLoader;
use NLevel\NLevel;

class QuestHuman extends Human
{
	
	/** @var string */
	protected $category = "";
	
	
	public function __construct (Level $level, CompoundTag $nbt)
	{
		parent::__construct ($level, $nbt);
	}
	
	public function initEntity (): void
	{
		parent::initEntity ();
		$nbt = $this->namedtag;
		if (!$nbt->hasTag ("category", StringTag::class)) {
			$this->close ();
		}
		$this->category = $nbt->getString ("category");
	}
	
	public function saveNBT (): void
	{
		$this->namedtag->setString ("category", $this->category);
		parent::saveNBT ();
	}
	
	public function getCategory (): string
	{
		return $this->category;
	}
	
	public function QuestUI (Player $player): void
	{
		$name = $player->getName ();
		if (isset (QuestLoader::$db ["player"] [$name] ["nowQuest"])) {
			$questName = QuestLoader::$db ["player"] [$name] ["nowQuest"];
			if ($this->getCategory () === QuestLoader::$db ["quest"] [$questName] ["category"]) {
				
				$handle = CustomUI::runFunction ()->ModalForm (function (Player $player, array $data) {
					if (!isset ($data [0])) {
						return false;
					}
					$name = $player->getName ();
					$questName = QuestLoader::$db ["player"] [$name] ["nowQuest"];
					if ($data [0] === true) {
						$type = QuestLoader::$db ["quest"] [$questName] ["type"];
						if ($type === "아이템 가져오기") {
							[ $id, $dm, $count, $tag ] = explode (":", QuestLoader::$db ["quest"] [$questName] ["value"]);
							$item = Item::get ($id, $dm, $count, base64_decode ($tag));
							if ($player->getInventory ()->contains ($item)) {
								$player->getInventory ()->removeItem ($item);
								QuestLoader::$db ["player"] [$name] ["clear"] [$questName] = [
									"category" => $this->getCategory (),
									"date" => date ("Y년 m월 d일 h시 i분 s초")
								];
								QuestLoader::$db ["quest"] [$questName] ["clear"] [$player->getName ()] = date ("Y년 m월 d일 h시 i분 s초");
								$exp = is_numeric (QuestLoader::$db ["quest"] [$questName] ["exp"]) ? QuestLoader::$db ["quest"] [$questName] ["exp"] : (int) QuestLoader::$db ["quest"] [$questName] ["exp"];
								NLevel::runFunction ()->addExp ($player->getName (), $exp);
								$this->rewardItem ($player, $questName);
								Server::getInstance ()->broadcastMessage ("§l§b [§f퀘스트 클리어§b]§r§a {$player->getName ()}님§f 께서 §a{$this->getCategory ()}§r§f 카테고리의 §a{$questName} 퀘스트§f 을(를) 클리어 하셨습니다.");
								$this->reset ($player->getName ());
							} else {
								QuestLoader::message ($player, "아이템이 부족합니다.");
							}
						} else {
							if (QuestLoader::$db ["player"] [$name] ["min"] >= QuestLoader::$db ["player"] [$name] ["max"]) {
								QuestLoader::$db ["player"] [$name] ["clear"] [$questName] = [
									"category" => $this->getCategory (),
									"date" => date ("Y년 m월 d일 h시 i분 s초")
								];
								QuestLoader::$db ["quest"] [$questName] ["clear"] [$player->getName ()] = date ("Y년 m월 d일 h시 i분 s초");
								$exp = is_numeric (QuestLoader::$db ["quest"] [$questName] ["exp"]) ? QuestLoader::$db ["quest"] [$questName] ["exp"] : (int) QuestLoader::$db ["quest"] [$questName] ["exp"];
								NLevel::runFunction ()->addExp ($player->getName (), $exp);
								$this->rewardItem ($player, $questName);
								Server::getInstance ()->broadcastMessage ("§l§b [§f퀘스트 클리어§b]§r§a {$player->getName ()}님§f 께서 §a{$this->getCategory ()}§r§f 카테고리의 §a{$questName} 퀘스트§f 을(를) 클리어 하셨습니다.");
								$this->reset ($player->getName ());
							} else {
								QuestLoader::message ($player, "퀘스트를 클리어 할 수 없습니다.");
							}
						}
					} else {
						QuestLoader::message ($player, "진행중인 퀘스트를 포기하셨습니다.");
						$this->reset ($player->getName ());
					}
				});
				$handle->setTitle ("§l{$this->getCategory ()}");
				$content = "\n§f현재 진행중인 퀘스트는 §b{$questName}§r§f 입니다.\n\n";
				$content .= "§f{$this->getGageBar ($player)}\n§f(" . QuestLoader::$db ["player"] [$name] ["min"] . "/" . QuestLoader::$db ["player"] [$name] ["max"] . "§r§f)\n\n";
				$content .= "" . $this->getQuestContent ($questName, $player->getName ()) . "\n";
				$handle->setContent ($content);
				$handle->setButton1 ("§l퀘스트 클리어하기§r\n§8퀘스트를 클리어 합니다.");
				$handle->setButton2 ("§l퀘스트 포기하기§r\n§8퀘스트를 포기합니다.");
				$handle->sendToPlayer ($player);
			}
		} else {
			$arr = [];
			foreach (QuestLoader::$db ["quest"] as $questName => $d) {
				if (!isset ($d ["clear"] [$player->getName ()])) {
					$arr [] = $questName;
				}
			}
			$handle = CustomUI::runFunction ()->SimpleForm (function (Player $player, array $data) {
				if (!isset ($data [0])) {
					return false;
				}
				$arr = [];
				foreach (QuestLoader::$db ["quest"] as $questName => $d) {
					if (!isset ($d ["clear"] [$player->getName ()])) {
						$arr [] = $questName;
					}
				}
				if (isset ($arr [$data [0]])) {
					QuestLoader::message ($player, "§a{$arr [$data [0]]}§r§7 퀘스트를 시작합니다.");
					QuestLoader::$db ["player"] [$player->getName ()] ["nowQuest"] = $arr [$data [0]];
					QuestLoader::$db ["player"] [$player->getName ()] ["min"] = 0;
					$type = QuestLoader::$db ["quest"] [$questName] ["type"];
					if ($type === "일반 몬스터 잡기") {
					   QuestLoader::$db ["player"] [$player->getName ()] ["entityId"] = (string) explode (":", QuestLoader::$db ["quest"] [$questName] ["value"]) [0];
						QuestLoader::$db ["player"] [$player->getName ()] ["max"] = (int) explode (":", QuestLoader::$db ["quest"] [$questName] ["value"]) [1];
					} else if ($type === "던전 몬스터 잡기") {
					   QuestLoader::$db ["player"] [$player->getName ()] ["entityId"] = (int) explode (":", QuestLoader::$db ["quest"] [$questName] ["value"]) [0];
						QuestLoader::$db ["player"] [$player->getName ()] ["max"] = (int) explode (":", QuestLoader::$db ["quest"] [$questName] ["value"]) [1];
					} else if ($type === "명령어 입력하기") {
					   QuestLoader::$db ["player"] [$player->getName ()] ["command"] = QuestLoader::$db ["quest"] [$questName] ["value"];
					   QuestLoader::$db ["player"] [$player->getName ()] ["max"] = 1;
					} else {
						QuestLoader::$db ["player"] [$player->getName ()] ["max"] = QuestLoader::$db ["quest"] [$questName] ["value"];
					}
				} else {
					return false;
				}
			});
			$handle->setTitle ("§l퀘스트");
			$handle->setContent ("");
			foreach ($arr as $c) {
				$handle->addButton ("- {$c}");
			}
			$handle->sendToPlayer ($player);
		}
	}
	
	public function getGageBar (Player $player): string
	{
		$maxhp = QuestLoader::$db ["player"] [$player->getName ()] ["max"];
		$hp = QuestLoader::$db ["player"] [$player->getName ()] ["min"];
		$o = $maxhp / 50;
		if ($o == 0) return "로딩중...";
		if ($maxhp == $hp) {
			$a = str_repeat("§6⎪§r", round($maxhp / $o));
			return $a . "";
		} elseif ($maxhp - $hp > 0) {
			$a = str_repeat("§6⎪§r", round($hp / $o)) . str_repeat("§7⎪§r", round($maxhp / $o - $hp / $o));
			return $a;
		}
	}
	
	public function attack (EntityDamageEvent $source): void
	{
		if ($source instanceof EntityDamageByEntityEvent) {
			$source->setCancelled (true);
			if (($player = $source->getDamager ()) instanceof Player) {
				$this->QuestUI ($player);
				if ($player->isOp ()) {
					if ($player->isSneaking ()) {
						$this->kill ();
					}
				}
			}
		}
	}
	
	public function reset (string $name): void
	{
		unset (QuestLoader::$db ["player"] [$name] ["nowQuest"]);
		unset (QuestLoader::$db ["player"] [$name] ["min"]);
		unset (QuestLoader::$db ["player"] [$name] ["max"]);
	}
	
	public function rewardItem (Player $player, string $questName): void
	{
		foreach (QuestLoader::$db ["quest"] [$questName] ["item"] as $code) {
			[ $id, $dm, $count, $tag ] = explode (":", $code);
			$player->getInventory ()->addItem (Item::get ($id, $dm, $count, base64_decode ($tag)));
		}
	}
	
	public function getQuestContent (string $questName, string $name): string
	{
		$type = QuestLoader::$db ["quest"] [$questName] ["type"];
		$content = "";
		if ($type === "광질 하기") {
			$content .= "§f광질 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 하기\n";
		} else if ($type === "농사 하기") {
			$content .= "§f농사 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 하기\n";
		} else if ($type === "아이템 가져오기") {
			[ $id, $dm, $count, $tag ] = explode (":", QuestLoader::$db ["player"] [$name] ["value"]);
			$item = Item::get ($id, $dm, $count, base64_decode ($tag));
			$iname = $item->hasCustomName () ? $item->getCustomName () : $item->getName ();
			$content .= "§f아이템 §a" . $iname . "§r§a × {$count}개§f 가져오기\n";
		} else if ($type === "일반 몬스터 잡기") {
			$content .= "§f지정된 몬스터 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 잡기\n";
		} else if ($type === "던전 몬스터 잡기") {
			$content .= "§a" . explode (":", QuestLoader::$db ["quest"] [$questName] ["value"]) [0] . "§r§f 몬스터 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 잡기\n";
			//var_dump (QuestLoader::$db ["player"] [$name] ["max"]);
		} else if ($type === "킬 하기") {
			$content .= "§f킬 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 하기\n";
		} else if ($type === "데스 하기") {
			$content .= "§f데스 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 하기\n";
		} else if ($type === "이스터에그 찾기") {
			$content .= "§f아스터에그 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "번§f 찾기\n";
		} else if ($type === "명령어 입력하기") {
			$content .= "§f명령어 §a" . QuestLoader::$db ["player"] [$name] ["max"] . "§f 입력하기\n";
		}
		//var_dump ($type);
		return $content;
	}
}