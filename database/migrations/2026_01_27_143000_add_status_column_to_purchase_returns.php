<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToPurchaseReturns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('purchase_returns', 'status')) {
            Schema::table('purchase_returns', function (Blueprint $table) {
                $table->string('status')->default('pending')->after('reason');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('purchase_returns', 'status')) {
            Schema::table('purchase_returns', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
