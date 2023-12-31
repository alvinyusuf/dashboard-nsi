<?php

namespace App\Http\Controllers;

use App\Models\Quality;
use App\Http\Requests\StoreQualityRequest;
use App\Http\Requests\UpdateQualityRequest;
use App\Models\HistoryQuality;
use Carbon\Carbon;

class QualityController extends Controller
{
    public function autoFillNoLot($departement, $section) {
        $now = Carbon::now();
        $monthNow = $now->format('m');
        $yearNow = $now->format('Y');

        $no = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('departement', $departement)->where('section', $section)->count();
        if ($no < 10) {
            $no = "0$no";
        }
        $noLot = "$no/$section/$yearNow";
        return $noLot;
    }

    public function updateCurrentHistoryQuality() {
        $now = Carbon::now();
        $monthNow = $now->format('m');
        $yearNow = $now->format('Y');

        $pastDate = $now->startOfMonth()->format('Y-m-d');

        $ncrCamIpqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'IPQC')->where('section', 'CAM')->where('keterangan', 'NCR')->count();
        $lotCamIpqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'IPQC')->where('section', 'CAM')->where('keterangan', 'LOT TAG')->count();
        $ncrCncIpqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'IPQC')->where('section', 'CNC')->where('keterangan', 'NCR')->count();
        $lotCncIpqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'IPQC')->where('section', 'CNC')->where('keterangan', 'LOT TAG')->count();
        $ncrMfgIpqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'IPQC')->where('section', 'MFG2')->where('keterangan', 'NCR')->count();
        $lotMfgIpqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'IPQC')->where('section', 'MFG2')->where('keterangan', 'LOT TAG')->count();
        $ncrCamOqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'OQC')->where('section', 'CAM')->where('keterangan', 'NCR')->count();
        $lotCamOqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'OQC')->where('section', 'CAM')->where('keterangan', 'LOT TAG')->count();
        $ncrCncOqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'OQC')->where('section', 'CNC')->where('keterangan', 'NCR')->count();
        $lotCncOqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'OQC')->where('section', 'CNC')->where('keterangan', 'LOT TAG')->count();
        $ncrMfgOqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'OQC')->where('section', 'MFG2')->where('keterangan', 'NCR')->count();
        $lotMfgOqc = Quality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)
            ->where('departement', 'OQC')->where('section', 'MFG2')->where('keterangan', 'LOT TAG')->count();

        $data = [
            'ncr_cam_ipqc' => $ncrCamIpqc,
            'lot_cam_ipqc' => $lotCamIpqc,
            'ncr_cnc_ipqc' => $ncrCncIpqc,
            'lot_cnc_ipqc' => $lotCncIpqc,
            'ncr_mfg_ipqc' => $ncrMfgIpqc,
            'lot_mfg_ipqc' => $lotMfgIpqc,
            'ncr_cam_oqc' => $ncrCamOqc,
            'lot_cam_oqc' => $lotCamOqc,
            'ncr_cnc_oqc' => $ncrCncOqc,
            'lot_cnc_oqc' => $lotCncOqc,
            'ncr_mfg_oqc' => $ncrMfgOqc,
            'lot_mfg_oqc' => $lotMfgOqc,
        ];

        HistoryQuality::updateOrCreate(['date' => $pastDate], $data);
    }

    public function indexHome()
    {
        $now = Carbon::now();
        $monthNow = $now->format('m');
        $yearNow = $now->format('Y');

        $historyQuality = HistoryQuality::whereMonth('date', $monthNow)->whereYear('date', $yearNow)->first();

        $user = auth()->user()->username;
        // $departement = substr($user, -1);

        if ($historyQuality === null) {
            $historyQuality = new HistoryQuality();
        }

        $actualCamIpqc = $historyQuality->ncr_cam_ipqc + $historyQuality->lot_cam_ipqc;
        $actualCncIpqc = $historyQuality->ncr_cnc_ipqc + $historyQuality->lot_cnc_ipqc;
        $actualMfgIpqc = $historyQuality->ncr_mfg_ipqc + $historyQuality->lot_mfg_ipqc;
        $actualCamOqc = $historyQuality->ncr_cam_oqc + $historyQuality->lot_cam_oqc;
        $actualCncOqc = $historyQuality->ncr_cnc_oqc + $historyQuality->lot_cnc_oqc;
        $actualMfgOqc = $historyQuality->ncr_mfg_oqc + $historyQuality->lot_mfg_oqc;

        return view('quality.home.index', [
            'historyQuality' => $historyQuality,
            // 'departement' => $departement,
            'actualCamIpqc' => $actualCamIpqc,
            'actualCncIpqc' => $actualCncIpqc,
            'actualMfgIpqc' => $actualMfgIpqc,
            'actualCamOqc' => $actualCamOqc,
            'actualCncOqc' => $actualCncOqc,
            'actualMfgOqc' => $actualMfgOqc,
        ]);
    }

    public function indexIpqc()
    {
        $user = auth()->user()->username;
        $data = Quality::where('departement', 'IPQC')->orderBy('date', 'desc')->get();
        return view('quality.dashboard-ipqc.index', [
            'data' => $data,
        ]);
    }

    public function indexOqc()
    {
        $user = auth()->user()->username;
        $data = Quality::where('departement', 'OQC')->orderBy('date', 'desc')->get();
        return view('quality.dashboard-oqc.index', [
            'data' => $data,
        ]);
    }

    // fungsi untuk menambahkan data history/detail claim QC
    public function store(StoreQualityRequest $request)
    {
        $data = $request->except('_token');
        if ($data['ng'] === null) {
            $data['ng'] = 0;
        }

        $departement = $request->departement;
        $quality = Quality::create($data);

        $departement = $request->departement;
        $section = $request->section;
        $noLot = $this->autoFillNoLot($departement, $section);
        $quality->no_ncr_lot = $noLot;
        $quality->save();

        $this->updateCurrentHistoryQuality();

        if ($departement == 'IPQC') {
            return redirect('/quality/dashboard-ipqc');
        } else {
            return redirect('/quality/dashboard-oqc');
        }
    }

    // fungsi untuk update target ipqc
    public function updateTargetIpqc(UpdateQualityRequest $request, HistoryQuality $historyQuality)
    {
        $now = Carbon::now()->startOfMonth()->format('Y-m-d');
        $data = $request->except(['_token', '_method']);
        $historyQuality::updateOrCreate(['date' => $now], $data);
        $this->updateCurrentHistoryQuality();
        return redirect('/quality/home')->with('success', 'Update data target IPQC!');
    }

    // fungsi untuk update target oqc
    public function updateTargetOqc(UpdateQualityRequest $request, HistoryQuality $historyQuality)
    {
        $now = Carbon::now()->startOfMonth()->format('Y-m-d');
        $data = $request->except(['_token', '_method']);
        $historyQuality::updateOrCreate(['date' => $now], $data);
        $this->updateCurrentHistoryQuality();
        return redirect('/quality/home')->with('success', 'Update data target IPQC!');
    }

    public function updateDataIpqc(UpdateQualityRequest $request, Quality $quality) {
        $update = $request->except(['_method', '_token']);
        $data = $quality->find($update['id']);
        $data->update($update);
        $data->save();
        $this->updateCurrentHistoryQuality();
        return redirect('/quality/dashboard-ipqc')->with('success', 'Data NCR / LOT TAG Berhasil Diubah!');
    }

    public function updateDataOqc(UpdateQualityRequest $request, Quality $quality) {
        $update = $request->except(['_method', '_token']);
        $data = $quality->find($update['id']);
        $data->update($update);
        $data->save();
        $this->updateCurrentHistoryQuality();
        return redirect('/quality/dashboard-oqc')->with('success', 'Data NCR / LOT TAG Berhasil Diubah!');
    }

    public function destroyDataIpqc(Quality $quality, $id) {
        $data = $quality->find($id);
        $data->delete();
        $this->updateCurrentHistoryQuality();
        return redirect('/quality/dashboard-ipqc')->with('success', 'Data NCR / LOT TAG Sudah Dihapus!');
    }

    public function destroyDataOqc(Quality $quality, $id) {
        $data = $quality->find($id);
        $data->delete();
        $this->updateCurrentHistoryQuality();
        return redirect('/quality/dashboard-oqc')->with('success', 'Data NCR / LOT TAG Sudah Dihapus!');
    }
}
