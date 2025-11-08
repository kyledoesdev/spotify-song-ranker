<?php

namespace App\Console\Commands;

use App\Models\Ranking;
use App\Models\User;
use App\Stats\LoginStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyDigest extends Command
{
    protected $signature = 'daily-digest:send';

    protected $description = 'Send a discord message with Daily Stats';

    private Carbon $now;

    public function __construct()
    {
        $this->now = now()->tz('America/New_York');
    }

    public function handle()
    {
        $message = "Daily Digest " . $this->now->toFormattedDayDateString() . ". \n";

        $message .= "New Users: {$this->getNewUsers()}. \n";
        $message .= "Logins Today: {$this->getLogins()}. \n";
        $message .= "Rankings Started: {$this->getNewRankings()}. \n";
        $message .= "Rankings Deleted: {$this->getDeletedRankings()}. \n";
        $message .= "Rankings Completed: {$this->getCompletedRankings()}. \n";

        Log::channel('discord_other_updates')->info($message);

        return Command::SUCCESS;
    }

    private function getNewUsers(): int
    {
        return User::query()
            ->whereBetween('created_at', [$this->now->startOfDay(), $this->now->endOfDay()])
            ->count();
    }

    private function getLogins(): int
    {
        return LoginStat::query()
            ->start($this->now->startOfDay())
            ->end($this->now->endOfDay())
            ->get()
            ->sum('increments');
    }

    private function getNewRankings(): int
    {
        return Ranking::query()
            ->whereBetween('created_at', [$this->now->startOfDay(), $this->now->endOfDay()])
            ->count();
    }

    private function getDeletedRankings(): int
    {
        return Ranking::query()
            ->onlyTrashed()
            ->whereBetween('deleted_at', [$this->now->startOfDay(), $this->now->endOfDay()])
            ->count();
    }

    private function getCompletedRankings(): int
    {
        return Ranking::query()
            ->whereBetween('completed_at', [$this->now->startOfDay(), $this->now->endOfDay()])
            ->count();
    }
}