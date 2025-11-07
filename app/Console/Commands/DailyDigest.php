<?php

namespace App\Console\Commands;

use App\Models\Ranking;
use App\Models\User;
use App\Stats\LoginStat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyDigest extends Command
{
    protected $signature = 'daily-digest:send';

    protected $description = 'Send a discord message with Daily Stats';

    public function handle()
    {
        $message = "Daily Digest " . now()->toFormattedDayDateString() . ". \n";

        $message .= "New Users: {$this->getNewUsers()}. \n";
        $message .= "Logins Today: {$this->getLogins()}. \n";
        $message .= "Rankings Started: {$this->getNewRankings()}. \n";
        $message .= "Rankings Deleted: {$this->getDeletedRankings()}. \n";
        $message .= "Rankings Completed: {$this->getCompletedRankings()}. \n";

        Log::channel('discord_other_updates')->info($message);
    }

    private function getNewUsers(): int
    {
        return User::query()
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
    }

    private function getLogins(): int
    {
        return LoginStat::query()
            ->start(now()->startOfDay())
            ->end(now()->endOfDay())
            ->get()
            ->sum('increments');
    }

    private function getNewRankings(): int
    {
        return Ranking::query()
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
    }

    private function getDeletedRankings(): int
    {
        return Ranking::query()
            ->onlyTrashed()
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
    }

    private function getCompletedRankings(): int
    {
        return Ranking::query()
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->whereNotNull('completed_at')
            ->count();
    }
}