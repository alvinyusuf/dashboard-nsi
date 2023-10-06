<?php

namespace App\Console;

use App\Models\MachineRepair;
use App\Models\TotalDowntime;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // function untuk menambahkan antara 2 downtime yang memiliki format '0:0:0:0'
    public function addDowntimeByDowntime($firstDowntime, $secDowntime) {
        $firstDowntimeParts = explode(':', $firstDowntime);
        $secDowntimeParts = explode(':', $secDowntime);

        $firstDowntimeDays = intval($firstDowntimeParts[0]);
        $firstDowntimeHours = intval($firstDowntimeParts[1]);
        $firstDowntimeMinutes = intval($firstDowntimeParts[2]);
        $firstDowntimeSeconds = intval($firstDowntimeParts[3]);

        $secDowntimeDays = intval($secDowntimeParts[0]);
        $secDowntimeHours = intval($secDowntimeParts[1]);
        $secDowntimeMinutes = intval($secDowntimeParts[2]);
        $secDowntimeSeconds = intval($secDowntimeParts[3]);

        $totalSeconds = (($firstDowntimeDays * 86400) + ($firstDowntimeHours * 3600) + ($firstDowntimeMinutes * 60) + $firstDowntimeSeconds) + (($secDowntimeDays * 86400) + ($secDowntimeHours * 3600) + ($secDowntimeMinutes * 60) + $secDowntimeSeconds);

        $days = floor($totalSeconds / 86400);
        $totalSeconds %= 86400;
        $hours = floor($totalSeconds / 3600);
        $totalSeconds %= 3600;
        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds %  60;

        $result = "$days:$hours:$minutes:$seconds";
        return $result;
    }

    public function subDowntimeByDowntime($firstDowntime, $secDowntime) {
        $firstDowntimeParts = explode(':', $firstDowntime);
        $secDowntimeParts = explode(':', $secDowntime);

        $firstDowntimeDays = intval($firstDowntimeParts[0]);
        $firstDowntimeHours = intval($firstDowntimeParts[1]);
        $firstDowntimeMinutes = intval($firstDowntimeParts[2]);
        $firstDowntimeSeconds = intval($firstDowntimeParts[3]);

        $secDowntimeDays = intval($secDowntimeParts[0]);
        $secDowntimeHours = intval($secDowntimeParts[1]);
        $secDowntimeMinutes = intval($secDowntimeParts[2]);
        $secDowntimeSeconds = intval($secDowntimeParts[3]);

        $totalSeconds = (($firstDowntimeDays * 86400) + ($firstDowntimeHours * 3600) + ($firstDowntimeMinutes * 60) + $firstDowntimeSeconds) - (($secDowntimeDays * 86400) + ($secDowntimeHours * 3600) + ($secDowntimeMinutes * 60) + $secDowntimeSeconds);

        $days = floor($totalSeconds / 86400);
        $totalSeconds %= 86400;
        $hours = floor($totalSeconds / 3600);
        $totalSeconds %= 3600;
        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds %  60;

        $result = "$days:$hours:$minutes:$seconds";
        return $result;
    }

    public function getAllTotalMonthlyDowntime() {
        $now = Carbon::now()->subDay();
        $monthNow = $now->format('m');
        $yearNow = $now->format('Y');
        $totalSeconds = 0;
        $machinesRepair = MachineRepair::whereMonth('downtime_month', "$monthNow")->whereYear('downtime_month', "$yearNow")->get(['prod_downtime', 'monthly_downtime']);
        foreach ($machinesRepair as $machineRepair) {
            $monthlyDowntime = $machineRepair->monthly_downtime;
            $prodDowntime = $machineRepair->prod_downtime;
            $totalSeconds = $this->subDowntimeByDowntime($monthlyDowntime, $prodDowntime);
        }
        return $totalSeconds;
    }

    // function untuk mendapatkan interval antara waktu strat downtime dan waktu sekarang ini (current downtime)
    public function getInterval($startDowntime, $now) {
        $start = Carbon::parse($startDowntime);
        $result = $start->diff($now)->format('%a:%h:%i:%s');
        return $result;
    }

    // function save current downtime atau prod downtime ke database
    // function ini akan dijalankan selama 1 menit sekali, sehingga menjadi fitur auto update downtime
    public function saveCurrentOrProdDowntime($id, $currentDowntime) {
        $machineRepair = MachineRepair::find($id);
        if ($machineRepair->status_mesin == 'Stop by Prod') {
            $machineRepair->prod_downtime = $currentDowntime;
            $machineRepair->save();
        } else {
            $machineRepair->current_downtime = $currentDowntime;
            $machineRepair->save();
        }
    }

    // function save current downtime atau prod downtime ke database
    // function ini akan dijalankan selama 1 menit sekali, sehingga menjadi fitur auto update downtime
    public function saveCurrentMonthly($id, $currentMonthlyDowntime) {
        $machineRepair = MachineRepair::find($id);
        if ($machineRepair->status_mesin != 'Stop by Prod') {
            $machineRepair->current_monthly_downtime = $currentMonthlyDowntime;
            $machineRepair->save();
        }
    }


    public function downtime() {
        $data = MachineRepair::whereNotIn('status_mesin', ['OK Repair (Finish)'])
                ->where('status_aktifitas', 'Stop')
                ->get([
                    'id', 'start_downtime', 'start_monthly_downtime', 'current_downtime',
                    'prod_downtime', 'total_downtime', 'current_monthly_downtime',
                    'total_monthly_downtime', 'downtime_month', 'status_mesin', 'status_aktifitas'
                ]);
        $now = Carbon::now();
        foreach ($data as $d) {
            $intervalDowntime = $this->getInterval($d['start_downtime'], $now);
            $intervalMonthlyDowntime = $this->getInterval($d['start_monthly_downtime'], $now);
            $this->saveCurrentOrProdDowntime($d['id'], $intervalDowntime);
            $this->saveCurrentMonthly($d['id'], $intervalMonthlyDowntime);
        }
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // melakukan create or update ke tabel total_downtime tiap sebulan sekali di awal bulan
        $schedule->call(function () {
            TotalDowntime::updateOrCreate(
                ['bulan_downtime' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d')],
                ['total_downtime' => $this->getAllTotalMonthlyDowntime()]
            );
        })->monthlyOn(1, '00:01');

        // melakukan update downtime downtime setiap satu menit sekali
        $schedule->call(function () {
            $this->downtime();
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
