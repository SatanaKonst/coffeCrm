<?php

use App\Enums\CoffeeVolume;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Таблица цен по объёмам.
        Schema::create('product_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('volume'); // CoffeeVolume enum value
            $table->decimal('price', 10, 2)->unsigned();
            $table->timestamps();

            $table->unique(['product_id', 'volume']);
        });

        // 2. Перенос данных из products.price → product_prices (g200, g500, kg1).
        // ponytail: коэффициенты 1 / 2.4 / 4.5 — стартовые для существующих товаров,
        // дальше цены управляются через админку. Согласовать с бизнесом при необходимости.
        if (Schema::hasColumn('products', 'price')) {
            $rows = DB::table('products')->select(['id', 'price'])->get();
            foreach ($rows as $row) {
                $base = (float) $row->price;
                foreach (CoffeeVolume::cases() as $v) {
                    $mult = match ($v) {
                        CoffeeVolume::G200 => 1.0,
                        CoffeeVolume::G500 => 2.4,
                        CoffeeVolume::KG1 => 4.5,
                    };
                    DB::table('product_prices')->insert([
                        'product_id' => $row->id,
                        'volume' => $v->value,
                        'price' => round($base * $mult, 2),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            Schema::table('products', function (Blueprint $table): void {
                $table->dropColumn('price');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('price', 10, 2)->unsigned()->default(0)->after('description');
        });

        // Восстановить базовую цену (из g200, иначе минимум).
        foreach (DB::table('products')->select('id')->get() as $row) {
            $price = DB::table('product_prices')
                ->where('product_id', $row->id)
                ->orderByRaw("CASE volume WHEN 'g200' THEN 0 WHEN 'g500' THEN 1 WHEN 'kg1' THEN 2 ELSE 3 END")
                ->value('price') ?? 0;
            DB::table('products')->where('id', $row->id)->update(['price' => $price]);
        }

        Schema::dropIfExists('product_prices');
    }
};
