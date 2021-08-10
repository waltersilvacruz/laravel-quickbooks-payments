<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickbooksTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_tokens', function (Blueprint $table) {
            $columnType = DB::getSchemaBuilder()->getColumnType('users', 'id');
            if ($columnType == 'string') {
                $user_id_type = 'uuid';
            } else if ($columnType == 'bigint') {
                $user_id_type = 'unsignedBigInteger';
            } else {
                $user_id_type = 'unsignedInteger';
            }
            $table->bigIncrements('id');
            $table->{$user_id_type}('user_id');
            $table->unsignedBigInteger('realm_id');
            $table->longtext('access_token');
            $table->datetime('access_token_expires_at');
            $table->string('refresh_token');
            $table->datetime('refresh_token_expires_at');
            $table->timestamps();

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quickbooks_tokens');
    }
}
