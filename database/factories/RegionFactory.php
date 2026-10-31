<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'level' => Region::LEVEL_CITY,
            'code' => fake()->unique()->numerify('##########'),
            'name' => fake()->unique()->city(),
            'source' => 'cahyadsn',
        ];
    }

    public function country(): static
    {
        return $this->state(fn (): array => [
            'level' => Region::LEVEL_COUNTRY,
            'name' => fake()->unique()->country(),
            'source' => 'dr5hn',
        ]);
    }

    public function withParent(Region $parent): static
    {
        return $this->state(fn (): array => [
            'parent_id' => $parent->getKey(),
            'country_id' => $parent->country_id ?? $parent->getKey(),
            'level' => match ($parent->level) {
                Region::LEVEL_COUNTRY => Region::LEVEL_STATE,
                Region::LEVEL_STATE => Region::LEVEL_CITY,
                Region::LEVEL_CITY => Region::LEVEL_DISTRICT,
                default => Region::LEVEL_VILLAGE,
            },
            'source' => $parent->source,
        ]);
    }

    public function village(): static
    {
        return $this->state(fn (): array => [
            'level' => Region::LEVEL_VILLAGE,
            'postal_code' => fake()->numerify('#####'),
        ]);
    }
}
