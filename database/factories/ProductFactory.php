<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'name' => $this->faker->word,
            'unit' => $this->faker->randomElement(['kg', 'pc', 'box']),
            'price' => $this->faker->randomFloat(2, 1, 1000), // Random price between 1 and 1000
            'quantity' => $this->faker->numberBetween(1, 100), // Random quantity between 1 and 100
            'category_id' => $this->faker->randomElement(Category::pluck('id')->toArray()), // Random category id
        ];
    }
}
