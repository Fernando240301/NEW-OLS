<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('name');          // nama menu
            $table->string('menu_key')->unique(); // key unik
            $table->string('icon')->nullable();

            $table->string('route')->nullable();
            $table->string('url')->nullable();

            $table->unsignedBigInteger('parent_id')->nullable(); // untuk submenu
            $table->integer('order_no')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
