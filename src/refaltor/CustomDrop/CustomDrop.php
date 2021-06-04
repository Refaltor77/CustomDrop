<?php

namespace refaltor\CustomDrop;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;

class CustomDrop extends PluginBase implements Listener
{
    public function onEnable()
    {
        $this->saveResource("config.yml");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        foreach ($this->getConfig()->get("block") as $id => $value)
        {
            if ($block->getId() . ":" . $block->getDamage() === $id) {
            	foreach ($value['drops'] as $drops){
            		$item = explode(":", $drops);
            		if (mt_rand(1, $item[3]) === 1){
            			$event->setDrops([Item::get($item[0], $item[1], $item[2])]);
					}
				}
            }
        }
    }
}