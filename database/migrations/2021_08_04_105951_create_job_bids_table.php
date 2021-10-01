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
            $table->integer('completion_time');     // in days
            $table->foreignId('offered_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('work_jobs')->cascadeOnDelete();
            $table->integer('status');
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
