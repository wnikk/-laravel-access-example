<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The shop of the second tutorial, docs/tutorial-abac-step-by-step.md, Step 4.
 * Examples 8 and up read these tables through conditions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('department_id')->default(1);
            $table->integer('approval_limit')->default(0);
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city')->nullable();
            $table->integer('team_id');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable();
            $table->string('name');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('restricted')->default(false);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('client_id')->nullable();
            $table->integer('user_id');            // the author, read by the suffix ".self"
            $table->integer('department_id');
            $table->integer('category_id');
            $table->integer('cost');
            $table->integer('budget')->nullable();
            $table->string('status');
            $table->boolean('locked')->default(false);
            $table->dateTime('created_at');
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('price');
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('quantity')->default(1);
        });
    }

    public function down(): void
    {
        foreach (['order_product', 'items', 'orders', 'products', 'categories', 'clients'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department_id', 'approval_limit']);
        });
    }
};
