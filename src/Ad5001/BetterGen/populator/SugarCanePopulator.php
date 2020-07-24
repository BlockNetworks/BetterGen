<?php
/**
 *  ____             __     __                    ____
 * /\  _`\          /\ \__ /\ \__                /\  _`\
 * \ \ \L\ \     __ \ \ ,_\\ \ ,_\     __   _ __ \ \ \L\_\     __     ___
 *  \ \  _ <'  /'__`\\ \ \/ \ \ \/   /'__`\/\`'__\\ \ \L_L   /'__`\ /' _ `\
 *   \ \ \L\ \/\  __/ \ \ \_ \ \ \_ /\  __/\ \ \/  \ \ \/, \/\  __/ /\ \/\ \
 *    \ \____/\ \____\ \ \__\ \ \__\\ \____\\ \_\   \ \____/\ \____\\ \_\ \_\
 *     \/___/  \/____/  \/__/  \/__/ \/____/ \/_/    \/___/  \/____/ \/_/\/_/
 * Tomorrow's pocketmine generator.
 * @author Ad5001 <mail@ad5001.eu>, XenialDan <https://github.com/thebigsmileXD>
 * @link https://github.com/Ad5001/BetterGen
 * @category World Generator
 * @api 3.0.0
 * @version 1.1
 */

namespace Ad5001\BetterGen\populator;

use Ad5001\BetterGen\structure\SugarCane;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;

class SugarCanePopulator extends AmountPopulator
{
	/** @var ChunkManager */
	protected $world;

	public function __construct()
	{
		$this->setBaseAmount(1);
		$this->setRandomAmount(2);
	}

	public function populate(ChunkManager $world, $chunkX, $chunkZ, Random $random): void
	{
		$this->world = $world;
		$amount = $this->getAmount($random);
		$sugarcane = new SugarCane ();
		for ($i = 0; $i < $amount; $i++) {
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if ($y !== -1 and $sugarcane->canPlaceObject($world, $x, $y, $z, $random)) {
				$sugarcane->placeObject($world, $x, $y, $z);
			}
		}
	}

	protected function getHighestWorkableBlock(int $x, int $z): int
	{
		for ($y = World::Y_MAX - 1; $y >= 0; --$y) {
			$b = $this->world->getBlockAt($x, $y, $z);
			if ($b !== VanillaBlocks::AIR() and $b->getId() !== BlockLegacyIds::LEAVES and $b->getId() !== BlockLegacyIds::LEAVES2) {
				break;
			}
		}
		return $y === 0 ? -1 : ++$y;
	}
}