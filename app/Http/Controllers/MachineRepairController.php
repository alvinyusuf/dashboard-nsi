<?php

namespace App\Http\Controllers;

use App\Models\MachineRepair;
use App\Http\Requests\StoreMachineRepairRequest;
use App\Http\Requests\UpdateMachineRepairRequest;
use App\Models\Machine;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MachineRepairController extends Controller
{
    public function cek() {
        $now = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $now1 = Carbon::now()->subMonth()->startOfMonth();
        $data = MachineRepair::whereNotIn('status_mesin', ['OK Repair (Finish)'])->where('status_aktifitas', 'Stop')->get(['id', 'start_downtime', 'current_downtime', 'prod_downtime', 'total_downtime', 'current_monthly_downtime', 'total_monthly_downtime', 'downtime_month', 'status_mesin', 'status_aktifitas']);
        return dd($now, $now1);
    }

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

    // function untuk mendapatkan interval antara waktu strat downtime dan waktu sekarang ini (current downtime)
    public function getInterval($startDowntime, $now) {
        $start = Carbon::parse($startDowntime);
        $result = $start->diff($now)->format('%a:%h:%i:%s');
        return $result;
    }

    // function yang akan menyimpan downtime dari kolom current_downtime atau prod_downtime yang sudah dijumlahkan dengan nilai pada kolom total_downtime sebelumnya ke kolom total_downtime dan mereset current_downtime ke '0:0:0:0'
    public function saveCurrentAndProdToTotalDowntime($id) {
        $machineRepair = MachineRepair::find($id);
        $now = Carbon::now();
        if ($machineRepair->status_mesin == 'Stop by Prod') {
            $currentProd = $this->getInterval($machineRepair->start_downtime, $now);
            $machineRepair->prod_downtime = $currentProd;
            $machineRepair->save();
        } else {
            $currentDowntime = $this->getInterval($machineRepair->start_downtime, $now);
            $currentMonthly = $this->getInterval($machineRepair->start_monthly_downtime, $now);

            $prod = $machineRepair->prod_downtime;
            $totalDowntime = $machineRepair->total_downtime;
            $totalMonthly = $machineRepair->total_monthly_downtime;

            $currentAndProd = $this->addDowntimeByDowntime($currentDowntime, $prod);

            $machineRepair->total_downtime = $this->addDowntimeByDowntime($currentAndProd, $totalDowntime);
            $machineRepair->total_monthly_downtime = $this->addDowntimeByDowntime($currentMonthly, $totalMonthly);

            $machineRepair->current_downtime = '0:0:0:0';
            $machineRepair->current_monthly_downtime = '0:0:0:0';
            $machineRepair->save();
        }
    }

    // function ini berfungsi untuk mengupdate kolom start_downtime menjadi waktu sekarang ini
    public function updateStartDowntime($id) {
        $now = Carbon::now();
        $machine = MachineRepair::find($id);
        $machine->start_downtime = $now;
        $machine->start_monthly_downtime = $now;
        $machine->save();
    }

    // function save current downtime atau prod downtime ke database
    // function ini akan dijalankan selama 1 menit sekali, sehingga menjadi fitur auto update downtime
    public function saveCurrentDowntimeToMonthly($id, $currentDowntime) {
        $machineRepair = MachineRepair::find($id);
        if ($machineRepair->status_mesin != 'Stop by Prod') {
            $machineRepair->monthly_downtime = $currentDowntime;
            $machineRepair->save();
        }
    }

    // function ini yang menangani ajax request dari halaman dashboard, dan berfungsi sebagai fitur realtime downtime counter dan auto update downtime ke database
    public function downtime(Request $request) {
        $data = $request->data;
        $now = Carbon::now();
        $result = [];
        foreach ($data as $d) {
            if ($d['status_mesin'] !== 'OK Repair (Finish)' && $d['status_aktifitas'] !== 'Running') {
                $interval = $this->getInterval($d['start_downtime'], $now);
                $result[$d['id']] = $this->addDowntimeByDowntime($interval, $d['total_downtime']);
            }
        }
        return $result;
    }

    public function index()
    {
        $machinesRepair = MachineRepair::all();
        $jsMachinesRepair = MachineRepair::get(['id', 'start_downtime', 'current_downtime', 'prod_downtime', 'total_downtime', 'current_monthly_downtime', 'total_monthly_downtime', 'downtime_month', 'status_mesin', 'status_aktifitas'])->toArray();
        $machines = Machine:: all();
        return view('dashboard.index', [
            'machines' => $machines,
            'machinesOnRepair' => $machinesRepair,
            'jsMachinesOnRepair' => $jsMachinesRepair,
        ]);
    }

    public function store(StoreMachineRepairRequest $request)
    {
        // return dd($request->all());
        $request->validate([
            'noMesin' => 'required|exists:machines,no_mesin',
            'request' => 'required',
            'status_mesin' => 'required',
            'status_aktifitas' => 'required',
        ]);

        $now = Carbon::now();
        $dataPayload = $request->all();
        $machine = Machine::where('no_mesin', $dataPayload['noMesin'])->get('id')->first();

        if ($dataPayload['tgl_kerusakan'] === null) {
            $dataPayload['tgl_kerusakan'] = $now;
        }

        $startDowntime = $dataPayload['tgl_kerusakan'];

        $downtime = '0:0:0:0';
        $defaultDowntime = $downtime;
        $start = Carbon::parse($startDowntime);

        $addExtraData = [];
        $extraData = [
            'mesin_id' => $machine->id,
            'start_downtime' => $startDowntime,
            'start_monthly_downtime' => $startDowntime,
            'downtime_month' => $now->format('Y-m-d'),
        ];

        if ($dataPayload['status_mesin'] == 'OK Repair (Finish)') {
            if ($dataPayload['finish'] !== null) {
                $end = Carbon::parse($dataPayload['finish']);
                $downtime = $start->diff($end)->format('%a:%h:%i:%s');
                $addExtraData = [
                    'current_downtime' => $defaultDowntime,
                    'prod_downtime' => $defaultDowntime,
                    'total_downtime' => $downtime,
                    'current_monthly_downtime' => $defaultDowntime,
                    'total_monthly_downtime' => $downtime,
                ];
            } else {
                $end = $now;
                $downtime = $start->diff($end)->format('%a:%h:%i:%s');
                $addExtraData = [
                    'current_downtime' => $defaultDowntime,
                    'prod_downtime' => $defaultDowntime,
                    'total_downtime' => $downtime,
                    'current_monthly_downtime' => $defaultDowntime,
                    'total_monthly_downtime' => $downtime,
                ];
            }
        } else if ($dataPayload['status_mesin'] == 'Stop by Prod') {
            $downtime = $start->diff($now)->format('%a:%h:%i:%s');
            $addExtraData = [
                'current_downtime' => $defaultDowntime,
                'prod_downtime' => $downtime,
                'total_downtime' => $defaultDowntime,
                'current_monthly_downtime' => $defaultDowntime,
                'total_monthly_downtime' => $defaultDowntime,
            ];
        } else {
            $downtime = $start->diff($now)->format('%a:%h:%i:%s');
            $addExtraData = [
                'current_downtime' => $downtime,
                'prod_downtime' => $defaultDowntime,
                'total_downtime' => $defaultDowntime,
                'current_monthly_downtime' => $downtime,
                'total_monthly_downtime' => $defaultDowntime,
            ];
        }


        $data = Arr::except($dataPayload, ['noMesin', 'finish']);
        $insertData = Arr::collapse([$extraData, $data, $addExtraData]);
        DB::table('machine_repairs')->insert($insertData);
        return redirect('/dashboard')->with('success', 'Data Baru Berhasil Ditambahkan!');;
    }

    public function update(UpdateMachineRepairRequest $request, MachineRepair $machineRepair)
    {
        $now = Carbon::now();
        $data = $request->except(['_method']);
        $machineRepair = $machineRepair->find($data['id']);
        $machineStatusInDB = $machineRepair->status_mesin;
        $machineStatusInput = $data['status'];

        $machineActivityInDB = $machineRepair->status_aktifitas;
        $machineActivityInput = $data['aktivitas'];

        if ($machineStatusInDB == 'Stop by Prod' && $machineStatusInput != 'Stop by Prod') {
            if ($machineActivityInDB == 'Stop' && $machineActivityInput == 'Stop') {
                // harus update nilai downtime prod dan mulai jalan downtime di current
            } else if ($machineActivityInDB == 'Stop' && $machineActivityInput == 'Running') {
                // downtime stop(pause) dari yang awalnya jalan
                // return dd('hello world');
                $this->saveCurrentAndProdToTotalDowntime($machineRepair->id);
            } else if ($machineActivityInDB == 'Running' && $machineActivityInput == 'Stop') {
                // downtime lanjut dari yang awalnya stop
                $this->updateStartDowntime($machineRepair->id);
            }
            if ($machineActivityInDB == 'Running' && $machineActivityInput == 'Running') {
                // downtime stop(pause) yang awalnya stop(pause)
                // tidak terjadi apa apa
            }
            $currentProdDowntime = $this->getInterval($machineRepair->start_downtime, $now);
            $this->saveCurrentAndProdToTotalDowntime($machineRepair->id);
            $this->saveCurrentDowntimeToMonthly($data['id'], $currentProdDowntime);
            $this->updateStartDowntime($machineRepair->id);
        } else {
            if ($machineActivityInDB == 'Stop' && $machineActivityInput == 'Stop') {
                // downtime jalan dari yang awalnya jalan dan save current downtime
                // tidak terjadi apa-apa
            }

            if ($machineActivityInDB == 'Stop' && $machineActivityInput == 'Running') {
                // downtime stop(pause) dari yang awalnya jalan
                $this->saveCurrentAndProdToTotalDowntime($machineRepair->id);
            }
            if ($machineActivityInDB == 'Running' && $machineActivityInput == 'Stop') {
                // downtime lanjut dari yang awalnya stop
                $this->updateStartDowntime($machineRepair->id);
            }
            if ($machineActivityInDB == 'Running' && $machineActivityInput == 'Running') {
                // downtime stop(pause) yang awalnya stop(pause)
                // tidak terjadi apa apa
            }
        }

        $machineRepair->kedatangan_prl = $data['kedatanganPrl'];
        $machineRepair->kedatangan_po = $data['kedatanganPo'];
        $machineRepair->tgl_kerusakan = $data['tanggalKerusakan'];
        $machineRepair->bagian_rusak = $data['bagianRusak'];
        $machineRepair->status_aktifitas = $data['aktivitas'];
        $machineRepair->status_mesin= $data['status'];
        $machineRepair->save();
        $machineRepair->update($data);
        return redirect('/dashboard')->with('success', 'Data Mesin Rusak Berhasil Diubah!');
    }

    public function destroy(MachineRepair $machineRepair)
    {
        //
    }
}
