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

namespace Ad5001\BetterGen\structure;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class Cactus
{
	protected $totalHeight;

	/**
	 * Checks if a cactus is placeable
	 *
	 * @param ChunkManager $world
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @param Random $random
	 * @return bool
	 */
	public function canPlaceObject(ChunkManager $world, int $x, int $y, int $z, Random $random): bool
	{
		$this->totalHeight = 1 + $random->nextBoundedInt(3);
		$below = $world->getBlockAt($x, $y - 1, $z);
		for ($yy = $y; $yy <= $y + $this->totalHeight; $yy++) {
			if (
				$world->getBlockAt($x, $yy, $z) !== VanillaBlocks::AIR() || ($below !== VanillaBlocks::SAND() && $below !== VanillaBlocks::CACTUS()) ||
				(
					$world->getBlockAt($x - 1, $yy, $z) !== VanillaBlocks::AIR() || $world->getBlockAt($x + 1, $yy, $z) !== VanillaBlocks::AIR() ||
					$world->getBlockAt($x, $yy, $z - 1) !== VanillaBlocks::AIR() || $world->getBlockAt($x, $yy, $z + 1) !== VanillaBlocks::AIR()
				)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Places a cactus
	 *
	 * @param ChunkManager $world
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return void
	 */
	public function placeObject(ChunkManager $world, int $x, int $y, int $z)
	{
		for ($yy = 0; $yy < $this->totalHeight; $yy++) {
			if ($world->getBlockAt($x, $y + $yy, $z) !== VanillaBlocks::AIR()) {
				return;
			}
			$world->setBlockAt($x, $y + $yy, $z, VanillaBlocks::CACTUS());
		}
	}
}
