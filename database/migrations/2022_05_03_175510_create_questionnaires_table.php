<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnairesTable extends Migration
{
    public function up()
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name')->nullable()->comment('ФИО');
            $table->string('email')->nullable()->comment('Email');
            $table->string('mobile_phone')->nullable()->comment('Мобильный телефон');
            $table->string('campus')->nullable()->comment('Корпус');
            $table->string('apartment')->nullable()->comment('Квартира');
            $table->string('parking')->nullable()->comment('Парковочное место');
            $table->json('images')->nullable()->comment('Вложения');
            $table->boolean('status')->default(0)->comment('Приглашен');
            $table->bigInteger('telegram_id')->nullable()->comment('Идентификатор чата Telegram');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questionnaires');
    }
}
