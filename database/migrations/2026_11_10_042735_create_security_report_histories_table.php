
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_report_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('security_report_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_report_histories');
    }
};
