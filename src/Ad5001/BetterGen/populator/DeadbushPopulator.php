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

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\biome\Biome;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;

class DeadbushPopulator extends AmountPopulator
{
	/** @var ChunkManager */
	protected $world;

	public function populate(ChunkManager $world, $chunkX, $chunkZ, Random $random): void
	{
		$this->world = $world;
		$amount = $this->getAmount($random);
		for ($i = 0; $i < $amount; $i++) {
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			if (!in_array($world->getChunk($chunkX, $chunkZ)->getBiomeId(abs($x % 16), ($z % 16)), [40, 39, Biome::DESERT])) continue;
			$y = $this->getHighestWorkableBlock($x, $z);
			if ($y !== -1 && $world->getBlockAt($x, $y - 1, $z) === VanillaBlocks::SAND()) {
				$world->setBlockAt($x, $y, $z, VanillaBlocks::DEAD_BUSH());
			}
		}
	}

	/**
	 * Gets the top block (y) on an x and z axes
	 * @param $x
	 * @param $z
	 * @return int
	 */
	protected function getHighestWorkableBlock(int $x, int $z): int
	{
		for ($y = World::Y_MAX - 1; $y > 0; --$y) {
			$b = $this->world->getBlockAt($x, $y, $z);
			if ($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS() or $b === VanillaBlocks::SAND() or $b === VanillaBlocks::SANDSTONE() or $b === VanillaBlocks::HARDENED_CLAY() or $b->getId() === BlockLegacyIds::STAINED_HARDENED_CLAY) {
				break;
			} elseif ($b !== VanillaBlocks::AIR()) {
				return -1;
			}
		}

		return ++$y;
	}
}