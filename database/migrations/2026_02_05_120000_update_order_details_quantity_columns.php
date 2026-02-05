<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderDetailsQuantityColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update ordered_qty and received_qty columns to support decimal values
        DB::statement('ALTER TABLE `order_details` MODIFY `ordered_qty` DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `order_details` MODIFY `received_qty` DECIMAL(10,2) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to integer if needed
        DB::statement('ALTER TABLE `order_details` MODIFY `ordered_qty` INT(11) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `order_details` MODIFY `received_qty` INT(11) NOT NULL DEFAULT 0');
    }
}
