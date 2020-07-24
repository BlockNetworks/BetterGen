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

use Ad5001\BetterGen\Main;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class LakePopulator extends AmountPopulator
{
	/** @var ChunkManager */
	protected $world;

	public function populate(ChunkManager $world, $chunkX, $chunkZ, Random $random): void
	{
		$this->world = $world;
		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
		$ory = $random->nextRange(20, 63); // Water world
		$y = $ory;
		for ($i = 0; $i < 4; $i++) {
			$x += $random->nextRange(-1, 1);
			$y += $random->nextRange(-1, 1);
			$z += $random->nextRange(-1, 1);
			if ($world->getBlockAt($x, $y, $z) !== VanillaBlocks::AIR())
				Main::buildRandom($this->world, new Vector3($x, $y, $z), new Vector3(5, 5, 5), $random, VanillaBlocks::WATER());
		}
		for ($xx = $x - 8; $xx <= $x + 8; $xx++)
			for ($zz = $z - 8; $zz <= $z + 8; $zz++)
				for ($yy = $ory + 1; $yy <= $y + 3; $yy++)
					if ($world->getBlockAt($xx, $yy, $zz) === VanillaBlocks::WATER())
						$world->setBlockAt($xx, $yy, $zz, VanillaBlocks::AIR());
	}

	protected function getHighestWorkableBlock(int $x, int $z): int
	{
		return 0;
	}
}