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
use pocketmine\world\World;

class CavePopulator extends AmountPopulator
{
	/** @var ChunkManager */
	protected $world;
	const STOP = false;
	const CONTINUE = true;

	public function populate(ChunkManager $world, $chunkX, $chunkZ, Random $random): void
	{
		$this->world = $world;
		$amount = $this->getAmount($random);
		for ($i = 0; $i < $amount; $i++) {
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $random->nextRange(10, $this->getHighestWorkableBlock($x, $z));
			// echo "Generating cave at $x, $y, $z." . PHP_EOL;
			$this->generateCave($x, $y, $z, $random);
		}
		// echo "Finished Populating chunk $chunkX, $chunkZ !" . PHP_EOL;
		// Filling water & lava sources randomly
		for ($i = 0; $i < $random->nextBoundedInt(5) + 3; $i++) {
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $random->nextRange(10, $this->getHighestWorkableBlock($x, $z));
			if ($world->getBlockAt($x, $y, $z) === VanillaBlocks::STONE() && ($world->getBlockAt($x + 1, $y, $z) === VanillaBlocks::AIR() ||
					$world->getBlockAt($x - 1, $y, $z) === VanillaBlocks::AIR() || $world->getBlockAt($x, $y, $z + 1) === VanillaBlocks::AIR() || $world->getBlockAt($x, $y, $z - 1) === VanillaBlocks::AIR()) &&
				$world->getBlockAt($x, $y - 1, $z) !== VanillaBlocks::AIR() && $world->getBlockAt($x, $y + 1, $z) !== VanillaBlocks::AIR()) {
				if ($y < 40 && $random->nextBoolean()) {
					$world->setBlockAt($x, $y, $z, VanillaBlocks::LAVA());
				} else {
					$world->setBlockAt($x, $y, $z, VanillaBlocks::WATER());
				}
			}
		}
	}

	protected function getHighestWorkableBlock($x, $z): int
	{
		for ($y = World::Y_MAX - 1; $y > 0; --$y) {
			$b = $this->world->getBlockAt($x, $y, $z);
			if ($b === VanillaBlocks::DIRT() or $b === VanillaBlocks::GRASS() or $b === VanillaBlocks::PODZOL() or $b === VanillaBlocks::SAND() or $b === VanillaBlocks::SNOW() or $b === VanillaBlocks::SANDSTONE()) {
				break;
			} elseif ($b !== 0 and $b !== VanillaBlocks::SNOW() and $b !== VanillaBlocks::WATER()) {
				return -1;
			}
		}

		return ++$y;
	}

	public function generateCave($x, $y, $z, Random $random)
	{
		$generatedBranches = $random->nextBoundedInt(10) + 1;
		foreach ($gen = $this->generateBranch($x, $y, $z, 5, 3, 5, $random) as $v3) {
			$generatedBranches--;
			if ($generatedBranches <= 0) {
				$gen->send(self::STOP);
			} else {
				$gen->send(self::CONTINUE);
			}
		}
	}

	public function generateBranch($x, $y, $z, $length, $height, $depth, Random $random)
	{
		if (!(yield new Vector3($x, $y, $z))) {
			for ($i = 0; $i <= 4; $i++) {
				Main::buildRandom($this->world, new Vector3($x, $y, $z), new Vector3($length - $i, $height - $i, $depth - $i), $random, VanillaBlocks::AIR());
				$x += round(($random->nextBoundedInt(round(30 * ($length / 10)) + 1) / 10 - 2));
				$yP = $random->nextRange(-14, 14);
				if ($yP > 12) {
					$y++;
				} elseif ($yP < -12) {
					$y--;
				}
				$z += round(($random->nextBoundedInt(round(30 * ($depth / 10)) + 1) / 10 - 1));
				return;
			}
		}
		$repeat = $random->nextBoundedInt(25) + 15;
		while ($repeat-- > 0) {
			Main::buildRandom($this->world, new Vector3($x, $y, $z), new Vector3($length, $height, $depth), $random, VanillaBlocks::AIR());
			$x += round(($random->nextBoundedInt(round(30 * ($length / 10)) + 1) / 10 - 2));
			$yP = $random->nextRange(-14, 14);
			if ($yP > 12) {
				$y++;
			} elseif ($yP < -12) {
				$y--;
			}
			$z += round(($random->nextBoundedInt(round(30 * ($depth / 10)) + 1) / 10 - 1));
			$height += $random->nextBoundedInt(3) - 1;
			$length += $random->nextBoundedInt(3) - 1;
			$depth += $random->nextBoundedInt(3) - 1;
			if ($height < 3)
				$height = 3;
			if ($length < 3)
				$length = 3;
			if ($height < 3)
				$height = 3;
			if ($height < 7)
				$height = 7;
			if ($length < 7)
				$length = 7;
			if ($height < 7)
				$height = 7;
			if ($random->nextBoundedInt(10) == 0) {
				foreach ($generator = $this->generateBranch($x, $y, $z, $length, $height, $depth, $random) as $gen) {
					if (!(yield $gen))
						$generator->send(self::STOP);
				}
			}
		}
		return;
	}
}