<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('client_name')->after('client_id');
            $table->string('client_phone')->after('client_name');

            $table->string('city')->after('total');
            $table->string('street')->after('city');
            $table->string('building')->after('street');
            $table->string('entrance')->nullable()->after('building');
            $table->string('apartment')->nullable()->after('entrance');
            $table->string('intercom')->nullable()->after('apartment');

            $table->string('subscription')->default('one_time')->after('intercom');
            $table->string('volume')->default('g200')->after('subscription');
            $table->boolean('grind')->default(false)->after('volume');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'client_name', 'client_phone',
                'city', 'street', 'building', 'entrance', 'apartment', 'intercom',
                'subscription', 'volume', 'grind',
            ]);
        });
    }
};
