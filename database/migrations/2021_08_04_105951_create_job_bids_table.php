<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_bids', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('offered_amount');
            $table->text('details');
            $table->foreignId('offered_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('work_jobs')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_bids');
    }
}
