<?php

namespace Luthfi\FixBlockPlace;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class FixBlockPlace extends PluginBase implements Listener {

    private array $placedBlocks = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority HIGHEST
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $position = $block->getPosition();

        $this->placedBlocks[$this->positionToString($position)] = $block;
    }

    /**
     * @param BlockUpdateEvent $event
     */
    public function onBlockUpdate(BlockUpdateEvent $event): void {
        $block = $event->getBlock();
        $position = $block->getPosition();

        $key = $this->positionToString($position);
        if (isset($this->placedBlocks[$key])) {
            $placedBlock = $this->placedBlocks[$key];

            if ($block->getId() !== $placedBlock->getId()) {
                $position->getWorld()->setBlock($position, $placedBlock, true, true);
            }

            unset($this->placedBlocks[$key]);
        }
    }

    private function positionToString(Vector3 $position): string {
        return $position->getX() . ":" . $position->getY() . ":" . $position->getZ();
    }
}
