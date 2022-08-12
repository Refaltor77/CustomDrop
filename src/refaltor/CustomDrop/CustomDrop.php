<?php

namespace refaltor\CustomDrop;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\plugin\PluginBase;

class CustomDrop extends PluginBase implements Listener
{
    private const ITEM_PARSE_WARN = "There is no such item with name "

    public function onEnable() : void
    {
        $this->saveResource("config.yml");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onBreak(BlockBreakEvent $event) : void
    {
        $block = $event->getBlock();
        foreach ($this->getConfig()->get("block") as $id => $value)
        {
            $item = null;
            $id = (string)$id;
            try{
                $item = StringToItemParser::getInstance()->parse($id) ?? LegacyStringToItemParser::getInstance()->parse($id);
            }catch(LegacyStringToItemParserException){
            }
            if ($item === null) {
                $this->getLogger()->warn(self::ITEM_PARSE_WARN . $id);
            }

            if ($block->asItem()->equals($item)) {
                foreach ($value['drops'] as $drops){
                    $newItem = explode(":", $drops);
                    $chance = (int)array_pop($newItem);
                    $count = (int)array_pop($newItem);
                    $drops = implode(":", $newItem);

                    if (mt_rand(1, $chance) === 1){
                        try{
                            $newDrops = StringToItemParser::getInstance()->parse($drops) ?? LegacyStringToItemParser::getInstance()->parse($drops);
                        }catch(LegacyStringToItemParserException){
                        }
                        if ($item === null) {
                            $this->getLogger()->warn(self::ITEM_PARSE_WARN . $drops);
                        } else {
                            $event->setDrops([$newDrops->setCount($count)]);
                        }
                    }
                }
            }
        }
    }
}
