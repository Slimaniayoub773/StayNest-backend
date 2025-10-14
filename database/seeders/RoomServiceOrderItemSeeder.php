<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceItem;
use App\Models\RoomServiceOrderItem;

class RoomServiceOrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = RoomServiceItem::all();

        RoomServiceOrder::all()->each(function ($order) use ($items) {
            $randomItems = $items->random(rand(1, 3));

            foreach ($randomItems as $item) {
                RoomServiceOrderItem::factory()->create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                ]);
            }
        });
    }
}
