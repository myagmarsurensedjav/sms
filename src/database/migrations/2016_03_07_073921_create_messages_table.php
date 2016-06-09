<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number');
            $table->string('vendor');
            $table->string('text');
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['sent', 'failed', 'pending']);
            $table->string('type', 64)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->getTable());
    }

    /**
     * Get the messages table name.
     *
     * @return string
     */
    public function getTable()
    {
        return Config::get('sms.table') ? 
            Config::get('sms.table') : 'messages';
    }
}