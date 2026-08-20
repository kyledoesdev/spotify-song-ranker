<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The IP lookup stored its whole response on every user, including coordinates, postal
     * code, internet provider and a second copy of the IP address. Only the four keys the
     * admin geography breakdown groups by are read, so drop the rest from existing rows.
     */
    private const KEPT_KEYS = ['country', 'countryCode', 'regionName', 'city'];

    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('user_packet')
            ->orderBy('id')
            ->chunkById(500, function ($users) {
                foreach ($users as $user) {
                    $packet = json_decode($user->user_packet, true);

                    if (! is_array($packet)) {
                        continue;
                    }

                    $trimmed = array_intersect_key($packet, array_flip(self::KEPT_KEYS));

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['user_packet' => json_encode($trimmed)]);
                }
            });
    }

    /**
     * The discarded fields came from a third party and were never ours to reconstruct.
     */
    public function down(): void
    {
        //
    }
};
