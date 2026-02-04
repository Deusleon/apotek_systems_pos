<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWasteCollectionTable extends Migration
{
    public function up()
    {
        Schema::create('waste_collection', function (Blueprint $table) {
            $table->increments('id'); // <-- Old Laravel fix
            $table->string('item_name')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('receipt_number')->unique();
            $table->date('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waste_collection');
    }
}
