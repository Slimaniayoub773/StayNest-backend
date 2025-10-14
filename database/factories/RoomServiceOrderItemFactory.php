<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceItem;
use App\Models\RoomServiceOrderItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomServiceOrderItem>
 */
class RoomServiceOrderItemFactory extends Factory
{
    protected $model = RoomServiceOrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => RoomServiceOrder::inRandomOrder()->first()->id ?? RoomServiceOrder::factory(),
            'item_id' => RoomServiceItem::inRandomOrder()->first()->id ?? RoomServiceItem::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price' => $this->faker->randomFloat(2, 10, 100),
            'notes' => $this->faker->sentence(),
        ];
    }
}
