<?php

use App\Model\SocialAccount;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSocialAccount extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', SocialAccount::TYPES);
            $table->string('social_id');
            $table->string('email');
            $table->string('avatar_url');
            $table->timestamps();
            $table->unique(['user_id', 'type']);
            $table->unique(['type', 'social_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_account');
    }
}
