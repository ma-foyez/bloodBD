<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('mobile')->nullable()->unique();
            $table->date('dob')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('occupation')->nullable();
            $table->boolean('is_weight_50kg')->default(false);
            $table->date('last_donation')->nullable();

            // Address
            $table->foreignId('division_id')->nullable(); // Constraints added later or loosely coupled
            $table->foreignId('district_id')->nullable();
            $table->foreignId('area_id')->nullable();
            $table->string('post_office')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->string('pic')->default('https://www.pngitem.com/pimgs/m/130-1300253_female-user-icon-png-download-user-image-color.png');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
