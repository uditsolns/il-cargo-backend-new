<?php

use App\Models\User;
use App\Services\VideoWatchService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoStatusToUsersTable extends Migration
{
    /**
     * Denormalized rollup of "does this driver have any pending video,
     * across all their trips" - mirrors cargo_details.driver_videos_status
     * (same null/pending/completed semantics), kept in sync by
     * VideoWatchService rather than computed on every request, since it's
     * filtered/paginated on (GET /drivers?video_status=) far more often
     * than it actually changes.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('video_status')->nullable()->after('role');
        });

        $service = app(VideoWatchService::class);

        User::where('role', 'Driver')->pluck('id')->each(
            fn($driverId) => $service->recalculateDriverVideoStatus($driverId),
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('video_status');
        });
    }
}
