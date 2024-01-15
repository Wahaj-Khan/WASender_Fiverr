<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEventToResetOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE EVENT reset_otp_event
            ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 5 MINUTE
            DO
            BEGIN
                UPDATE users
                SET otp = NULL
                WHERE otp IS NOT NULL;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP EVENT IF EXISTS reset_otp_event');
    }
}
