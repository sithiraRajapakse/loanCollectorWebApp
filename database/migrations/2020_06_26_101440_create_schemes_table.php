<?php

use App\Enums\SchemeType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schemes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', [SchemeType::DAILY, SchemeType::WEEKLY, SchemeType::MONTHLY, SchemeType::CUSTOM, SchemeType::BI_WEEKLY]);
            $table->unsignedDecimal('interest_rate', 5, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schemes');
    }
}
