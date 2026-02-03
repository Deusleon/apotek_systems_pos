<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountReceivedToPettyCashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('petty_cash', function (Blueprint $table) {
            if (!Schema::hasColumn('petty_cash', 'amount_received')) {
                $table->decimal('amount_received', 15, 2)->default(0)->after('opening_balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petty_cash', function (Blueprint $table) {
            if (Schema::hasColumn('petty_cash', 'amount_received')) {
                $table->dropColumn('amount_received');
            }
        });
    }
}
