<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NotificationChannels\WebPush\PushSubscription;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = config('webpush.database_connection');
        $table = config('webpush.table_name');

        Schema::connection($connection)->table($table, function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['endpoint']);
        });

        Schema::connection($connection)->table($table, function (Blueprint $blueprint): void {
            $blueprint->string('endpoint', PushSubscription::ENDPOINT_MAX_LENGTH)
                ->charset('ascii')
                ->unique()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('webpush.database_connection');
        $table = config('webpush.table_name');

        Schema::connection($connection)->table($table, function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['endpoint']);
        });

        Schema::connection($connection)->table($table, function (Blueprint $blueprint): void {
            $blueprint->string('endpoint', 500)
                ->unique()
                ->change();
        });
    }
};
