<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQboRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        If(!Schema::hasTable('qbo_requests'))
        {
            Schema::Create('qbo_requests', function($table){
                $table->increments('request_id');
                $table->text('request_header');  
                $table->text('response_header');                              
                $table->text('request_query');
                $table->text('response_body');
                $table->datetime('sent');
                $table->integer('status_code');
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
        if(Schema::hasTable('qbo_requests'))
        {
            Schema::drop('qbo_requests');
        }
    }
}
