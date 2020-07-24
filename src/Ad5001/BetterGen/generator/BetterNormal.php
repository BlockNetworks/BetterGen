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

namespace Ad5001\BetterGen\generator;

use Ad5001\BetterGen\biome\BetterDesert;
use Ad5001\BetterGen\biome\BetterForest;
use Ad5001\BetterGen\biome\BetterIcePlains;
use Ad5001\BetterGen\biome\BetterMesa;
use Ad5001\BetterGen\biome\BetterMesaPlains;
use Ad5001\BetterGen\biome\BetterRiver;
use Ad5001\BetterGen\biome\Mountainable;
use Ad5001\BetterGen\Main;
use Ad5001\BetterGen\populator\CavePopulator;
use Ad5001\BetterGen\populator\LakePopulator;
use Ad5001\BetterGen\populator\RavinePopulator;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\TreeType;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\populator\GroundCover;
use pocketmine\world\generator\populator\Ore;
use pocketmine\world\generator\object\OreType;
use pocketmine\utils\Random;
use pocketmine\world\biome\Biome;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\populator\Populator;
use pocketmine\world\World;

class BetterNormal extends Generator
{
	const NOT_OVERWRITABLE = [
		BlockLegacyIds::STONE,
		BlockLegacyIds::GRAVEL,
		BlockLegacyIds::BEDROCK,
		BlockLegacyIds::DIAMOND_ORE,
		BlockLegacyIds::GOLD_ORE,
		BlockLegacyIds::LAPIS_ORE,
		BlockLegacyIds::REDSTONE_ORE,
		BlockLegacyIds::IRON_ORE,
		BlockLegacyIds::COAL_ORE,
		BlockLegacyIds::WATER,
		BlockLegacyIds::STILL_WATER
	];
	/** @var BetterBiomeSelector */
	protected $selector;
	/** @var World */
	protected $world;
	/** @var Random */
	protected $random;
	/** @var Populator[] */
	protected $populators = [];
	/** @var Populator[] */
	protected $generationPopulators = [];
	/** @var Biome[][] */
	public static $biomes = [];
	/** @var Biome[] */
	public static $biomeById = [];
	/** @var World[] */
	public static $levels = [];
	/** @var int[][] */
	protected static $GAUSSIAN_KERNEL = null; // From main class
	/** @var int */
	protected static $SMOOTH_SIZE = 2;
	/** @var int */
	protected $waterHeight = 63;
	protected $noiseBase;

	/**
	 * Picks a biome by X and Z
	 *
	 * @param    $x    int
	 * @param    $z    int
	 * @return Biome
	 */
	public function pickBiome($x, $z): Biome
	{
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->random->getSeed();
		$hash *= $hash + 223;
		$xNoise = $hash >> 20 & 3;
		$zNoise = $hash >> 22 & 3;
		if ($xNoise == 3) {
			$xNoise = 1;
		}
		if ($zNoise == 3) {
			$zNoise = 1;
		}

		$b = $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
		if ($b instanceof Mountainable && $this->random->nextBoundedInt(1000) < 3) {
			$b = clone $b;
			// $b->setElevation($b->getMinElevation () + (50 * $b->getMinElevation () / 100), $b->getMaxElevation () + (50 * $b->getMinElevation () / 100));
		}
		return $b;
	}

	public static function registerBiome(Biome $biome): bool
	{
		foreach (self::$levels as $lvl) if (isset($lvl->selector)) $lvl->selector->addBiome($biome); // If no selector created, it would cause errors. These will be added when selectoes
		if (!isset(self::$biomes[(string)$biome->getRainfall()])) self::$biomes[( string)$biome->getRainfall()] = [];
		self::$biomes[( string)$biome->getRainfall()] [( string)$biome->getTemperature()] = $biome;
		ksort(self::$biomes[( string)$biome->getRainfall()]);
		ksort(self::$biomes);
		self::$biomeById[$biome->getId()] = $biome;
		return true;
	}

	public static function getBiome($temperature, $rainfall)
	{
		$ret = null;
		if (!isset(self::$biomes[( string)round($rainfall, 1)])) {
			while (!isset(self::$biomes[( string)round($rainfall, 1)])) {
				if (abs($rainfall - round($rainfall, 1)) >= 0.05)
					$rainfall += 0.1;
				if (abs($rainfall - round($rainfall, 1)) < 0.05)
					$rainfall -= 0.1;
				if (round($rainfall, 1) < 0)
					$rainfall = 0;
				if (round($rainfall, 1) >= 0.9)
					$rainfall = 0.9;
			}
		}
		$b = self::$biomes[( string)round($rainfall, 1)];
		foreach ($b as $t => $biome) {
			if ($temperature <= (float)$t) {
				$ret = $biome;
				break;
			}
		}
		if (is_string($ret)) {
			$ret = new $ret ();
		}
		return $ret;
	}

	public function getBiomeById(int $id): Biome
	{
		return self::$biomeById[$id] ?? self::$biomeById[Biome::OCEAN];
	}

	public function generateChunk(int $chunkX, int $chunkZ): void
	{
		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $this->world->getChunk($chunkX, $chunkZ);

		$biomeCache = [];

		for ($x = 0; $x < 16; $x++) {
			for ($z = 0; $z < 16; $z++) {
				$minSum = 0;
				$maxSum = 0;
				$weightSum = 0;

				$biome = $this->pickBiome($chunkX * 16 + $x, $chunkZ * 16 + $z);
				$chunk->setBiomeId($x, $z, $biome->getId());

				for ($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; $sx++) {
					for ($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; $sz++) {

						$weight = self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] [$sz + self::$SMOOTH_SIZE];

						if ($sx === 0 and $sz === 0) {
							$adjacent = $biome;
						} else {
							$index = World::chunkHash($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
							if (isset($biomeCache[$index])) {
								$adjacent = $biomeCache[$index];
							} else {
								$biomeCache[$index] = $adjacent = $this->pickBiome($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
							}
						}
						$minSum += ($adjacent->getMinElevation() - 1) * $weight;
						$maxSum += $adjacent->getMaxElevation() * $weight;

						$weightSum += $weight;
					}
				}

				$minSum /= $weightSum;
				$maxSum /= $weightSum;

				$smoothHeight = ($maxSum - $minSum) / 2;

				for ($y = 0; $y < 128; $y++) {
					if ($y < 3 || ($y < 5 && $this->random->nextBoolean())) {
						$chunk->setFullBlock($x, $y, $z, VanillaBlocks::BEDROCK()->getFullId());
						continue;
					}
					$noiseValue = $noise[$x] [$z] [$y] - 1 / $smoothHeight * ($y - $smoothHeight - $minSum);

					if ($noiseValue > 0) {
						$chunk->setFullBlock($x, $y, $z, VanillaBlocks::STONE()->getFullId());
					} elseif ($y <= $this->waterHeight) {
						$chunk->setFullBlock($x, $y, $z, VanillaBlocks::WATER()->getFullId());
					}
				}
			}
		}

		foreach ($this->generationPopulators as $populator) {
			$populator->populate($this->world, $chunkX, $chunkZ, $this->random);
		}
	}

	public function populateChunk(int $chunkX, int $chunkZ): void
	{
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->world->getSeed());
		foreach ($this->populators as $populator) {
			$populator->populate($this->world, $chunkX, $chunkZ, $this->random);
		}

		// Filling lava (lakes & rivers underground)...
		for ($x = $chunkX; $x < $chunkX + 16; $x++)
			for ($z = $chunkZ; $z < $chunkZ + 16; $z++)
				for ($y = 1; $y < 11; $y++) {
					if (!in_array($this->world->getBlockAt($x, $y, $z)->getId(), self::NOT_OVERWRITABLE))
						$this->world->setBlockAt($x, $y, $z, VanillaBlocks::LAVA());
				}

		$chunk = $this->world->getChunk($chunkX, $chunkZ);
		$biome = self::getBiomeById($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->world, $chunkX, $chunkZ, $this->random);
	}

	/**
	 * Constructs the class
	 *
	 * @param array $options
	 */
	public function __construct(ChunkManager $world, int $seed, array $options = [])
	{
		parent::__construct($world, $seed, $options);

		if (self::$GAUSSIAN_KERNEL === null) {
			self::generateKernel();
		}

		$this->world = $world;

		self::$levels[] = $world;

		$this->random->setSeed($seed);
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);

		$this->registerBiome(Biome::getBiome(Biome::OCEAN));
		$this->registerBiome(Biome::getBiome(Biome::PLAINS));
		$this->registerBiome(new BetterDesert ());
		$this->registerBiome(new BetterMesa ());
		$this->registerBiome(new BetterMesaPlains ());
		$this->registerBiome(Biome::getBiome(Biome::TAIGA));
		$this->registerBiome(Biome::getBiome(Biome::SWAMP));
		$this->registerBiome(new BetterRiver ());
		$this->registerBiome(new BetterIcePlains ());
		$this->registerBiome(new BetterForest(TreeType::OAK(), [
			0.6,
			0.5
		]));
		$this->registerBiome(new BetterForest(TreeType::SPRUCE(), [
			0.7,
			0.8
		]));
		$this->registerBiome(new BetterForest(TreeType::BIRCH(), [
			0.6,
			0.4
		]));

		$this->selector = new BetterBiomeSelector($this->random, [
			self::class,
			"getBiome"
		], self::getBiome(0, 0));

		foreach (self::$biomes as $rain) {
			foreach ($rain as $biome) {
				$this->selector->addBiome($biome);
			}
		}

		$this->selector->recalculate();

		$cover = new GroundCover();
		$this->generationPopulators[] = $cover;

			$lake = new LakePopulator();
			$lake->setBaseAmount(0);
			$lake->setRandomAmount(1);
			$this->generationPopulators[] = $lake;

			$cave = new CavePopulator ();
			$cave->setBaseAmount(0);
			$cave->setRandomAmount(2);
			$this->generationPopulators[] = $cave;

			$ravine = new RavinePopulator ();
			$ravine->setBaseAmount(0);
			$ravine->setRandomAmount(51);
			$this->generationPopulators[] = $ravine;

			$ores = new Ore();
			$ores->setOreTypes([
				new OreType(VanillaBlocks::COAL_ORE(), 20, 16, 0, 128),
				new OreType(VanillaBlocks::IRON_ORE(), 20, 8, 0, 64),
				new OreType(VanillaBlocks::REDSTONE_ORE(), 8, 7, 0, 16),
				new OreType(VanillaBlocks::LAPIS_LAZULI_ORE(), 1, 6, 0, 32),
				new OreType(VanillaBlocks::GOLD_ORE(), 2, 8, 0, 32),
				new OreType(VanillaBlocks::DIAMOND_ORE(), 1, 7, 0, 16),
				new OreType(VanillaBlocks::DIRT(), 20, 32, 0, 128),
				new OreType(VanillaBlocks::GRAVEL(), 10, 16, 0, 128)
			]);
			$this->populators[] = $ores;
	}

	/**
	 * Generates the generation kernel based on smooth size (here 2)
	 */
	protected static function generateKernel()
	{
		self::$GAUSSIAN_KERNEL = [];

		$bellSize = 1 / self::$SMOOTH_SIZE;
		$bellHeight = 2 * self::$SMOOTH_SIZE;

		for ($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; $sx++) {
			self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];

			for ($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; $sz++) {
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] [$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
			}
		}
	}

	/**
	 * Return the name of the generator
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return "betternormal";
	}
}
