<?php

namespace App\Services;

use App\Models\Penilaian;
use App\Models\Period;
use App\Models\ProgramCriteria;
use App\Models\CalculateReceiver;
use App\Models\Receiver;
use App\Models\Criteria;

class PenilaianService
{
    public function startCalculate($id)
    {
        $penilaian = Penilaian::find($id);
        if (!$penilaian) {
            return null;
        }

        $penilaian->status = 'In Progress';
        $penilaian->save();

        $period = Period::find($penilaian->period_id);
        if (!$period) {
            return null;
        }

        $programCriteria = ProgramCriteria::where('program_id', $period->program_id)->get();
        $calculateReceivers = CalculateReceiver::where('penilaian_id', $id)->get();

        $criteria = [];
        foreach ($programCriteria as $item) {
            $criteriaModel = Criteria::find($item->criteria_id);
            if ($criteriaModel) {
                $criteria[] = $criteriaModel->title;
            }
        }

        $users = [];
        foreach ($calculateReceivers as $item) {
            $receiver = Receiver::find($item->receiver_id);
            if ($receiver) {
                $users[] = $receiver;
            }
        }

        if (!empty($criteria) && !empty($users)) {
            $this->setParameterWeight($id, $users, $criteria);
            $this->setUtilityValue($id, $users, $criteria);
            $this->setNilaiAkhir($id, $users, $criteria, $period->program_id);
            $this->setRanking($id, $users, $penilaian->jumlah_penerima);
        }

        $penilaian->status = 'Done';
        $penilaian->save();

        return null;
    }

    // Tambahkan implementasi fungsi-fungsi di bawah ini sesuai kebutuhan
    private function setParameterWeight($id, $users, $criteria) { // Mapping: nama kriteria => nama field pada model Receiver dan NilaiBobot
        $fieldMap = [
            'Penghasilan' => 'penghasilan',
            'Status Tempat Tinggal' => 'status_tempat_tinggal',
            'Pekerjaan' => 'pekerjaan',
            'Status Pernikahan Kepala keluarga' => 'status_perkawinan',
            'Jumlah Tanggungan' => 'jumlah_tanggungan',
            'Keadaan Rumah' => 'keadaan_rumah',
            'Bahan Bakar Masak' => 'bahan_bakar_harian',
            'Disabilitas' => 'disabilitas',
            'Tingkat Pendidikan' => 'pendidikan',
            'Fasilitas MCK' => 'fasilitas_mck',
            'Kepemilikan Kendaraan' => 'kepemilikan_kendaraan',
        ];
    
        foreach ($users as $user) {
            $bobotUser = new \App\Models\NilaiBobot();
            $bobotUser->penilaian_id = $penilaianId;
            $bobotUser->receiver_id = $user->id;
    
            foreach ($criteria as $criterion) {
                if (!isset($fieldMap[$criterion])) {
                    continue; // skip jika kriteria tidak dikenal
                }
    
                $field = $fieldMap[$criterion];
                $title = $user->$field ?? null;
    
                if (!$title) continue;
    
                $parameter = \App\Models\Parameter::where('title', $title)->first();
    
                if ($parameter) {
                    $bobotUser->$field = $parameter->parameter_weight;
                }
            }
    
            $bobotUser->save();
        } 
    }

    private function setUtilityValue($id, $users, $criteria) { 
        foreach ($receivers as $receiver) {
            $bobotUser = new NilaiUtility();
            $bobotUser->penilaian_id = $penilaianId;
            $bobotUser->receiver_id = $receiver->id;
    
            $allNilai = NilaiBobot::where('penilaian_id', $penilaianId)->get();
            $nilaiBobot = NilaiBobot::where('receiver_id', $receiver->id)
                                    ->where('penilaian_id', $penilaianId)
                                    ->first();
    
            if (!$nilaiBobot) continue;
    
            foreach ($criteria as $criterion) {
                $fieldMap = [
                    'Penghasilan' => 'penghasilan',
                    'Status Tempat Tinggal' => 'status_tempat_tinggal',
                    'Pekerjaan' => 'pekerjaan',
                    'Status Pernikahan Kepala keluarga' => 'status_perkawinan',
                    'Jumlah Tanggungan' => 'jumlah_tanggungan',
                    'Keadaan Rumah' => 'keadaan_rumah',
                    'Bahan Bakar Masak' => 'bahan_bakar_harian',
                    'Disabilitas' => 'disabilitas',
                    'Tingkat Pendidikan' => 'pendidikan',
                    'Fasilitas MCK' => 'fasilitas_mck',
                    'Kepemilikan Kendaraan' => 'kepemilikan_kendaraan',
                ];
    
                if (!isset($fieldMap[$criterion])) continue;
    
                $field = $fieldMap[$criterion];
                $nilai = $allNilai->pluck($field)->filter();
                $max = $nilai->max() ?? 0;
                $min = $nilai->min() ?? 0;
                $divider = $max - $min;
    
                $utility = 0;
                if ($divider != 0) {
                    $utility = round(($nilaiBobot->$field - $min) / $divider, 2);
                }
    
                $bobotUser->$field = $utility;
            }
    
            $bobotUser->save();
        }
    }

    private function setNilaiAkhir($id, $users, $criteria, $programId) { 
        foreach ($receivers as $receiver) {
            $nilaiBobot = NilaiUtility::where('receiver_id', $receiver->id)
                                      ->where('penilaian_id', $penilaianId)
                                      ->first();
    
            if (!$nilaiBobot) continue;
    
            $bobotUser = new NilaiAkhir();
            $bobotUser->penilaian_id = $penilaianId;
            $bobotUser->receiver_id = $receiver->id;
    
            foreach ($criteria as $criterion) {
                $fieldMap = [
                    'Penghasilan' => 'penghasilan',
                    'Status Tempat Tinggal' => 'status_tempat_tinggal',
                    'Pekerjaan' => 'pekerjaan',
                    'Status Pernikahan Kepala keluarga' => 'status_perkawinan',
                    'Jumlah Tanggungan' => 'jumlah_tanggungan',
                    'Keadaan Rumah' => 'keadaan_rumah',
                    'Bahan Bakar Masak' => 'bahan_bakar_harian',
                    'Disabilitas' => 'disabilitas',
                    'Tingkat Pendidikan' => 'pendidikan',
                    'Fasilitas MCK' => 'fasilitas_mck',
                    'Kepemilikan Kendaraan' => 'kepemilikan_kendaraan',
                ];
    
                // If the criterion exists in the map, process it
                if (array_key_exists($criterion, $fieldMap)) {
                    $this->setUtilityForCriterion($bobotUser, $nilaiBobot, $criterion, $fieldMap[$criterion], $programId);
                }
            }
    
            $bobotUser->save();
        }
     }

     private function setUtilityForCriterion(NilaiAkhir $bobotUser, NilaiUtility $nilaiBobot, $criterion, $field, $programId)
     {
    $criteriaData = Criteria::where('title', $criterion)->first();
    if (!$criteriaData) return;

    $getCriteriaWeight = ProgramCriteria::where('program_id', $programId)
                                        ->where('criteria_id', $criteriaData->id)
                                        ->first();
    if (!$getCriteriaWeight) return;

    $utility = $this->calculateUtility($nilaiBobot, $criterion, $getCriteriaWeight->weight);
    
    // Assign utility to the field dynamically using the field map
    $bobotUser->{$field} = $utility;
}


private function calculateUtility(NilaiUtility $nilaiBobot, $criterion, $weight)
{
    $value = 0;
    switch ($criterion) {
        case "Penghasilan":
            $value = $nilaiBobot->penghasilan;
            break;
        case "Status Tempat Tinggal":
            $value = $nilaiBobot->status_tempat_tinggal;
            break;
        case "Pekerjaan":
            $value = $nilaiBobot->pekerjaan;
            break;
        case "Status Pernikahan Kepala keluarga":
            $value = $nilaiBobot->status_perkawinan;
            break;
        case "Jumlah Tanggungan":
            $value = $nilaiBobot->jumlah_tanggungan;
            break;
        case "Keadaan Rumah":
            $value = $nilaiBobot->keadaan_rumah;
            break;
        case "Bahan Bakar Masak":
            $value = $nilaiBobot->bahan_bakar_harian;
            break;
        case "Disabilitas":
            $value = $nilaiBobot->disabilitas;
            break;
        case "Tingkat Pendidikan":
            $value = $nilaiBobot->pendidikan;
            break;
        case "Fasilitas MCK":
            $value = $nilaiBobot->fasilitas_mck;
            break;
        case "Kepemilikan Kendaraan":
            $value = $nilaiBobot->kepemilikan_kendaraan;
            break;
    }

    // Calculate utility as per weight (round to 2 decimal places)
    return $value * (BigDecimal::valueOf($weight) / 100)->round(2, PHP_ROUND_HALF_DOWN);
}

     
    private function setRanking($id, $users, $jumlahPenerima) { 
        $rankings = [];

        foreach ($users as $user) {
            $ranking = new Ranking();
            $ranking->penilaian_id = $id;
            $ranking->receiver_id = $user->id;
    
            // Ambil nilai total untuk masing-masing penerima
            $total = BigDecimal::ZERO;
    
            $totalValue = NilaiAkhir::where('receiver_id', $user->id)
                                    ->where('penilaian_id', $id)
                                    ->first();
    
            if ($totalValue) {
                $total = $this->calculateTotal($totalValue);
            }
    
            $ranking->total = $total;
            $rankings[] = $ranking;
        }
    
        // Urutkan berdasarkan total (descending) dan berikan ranking
        $rankedList = collect($rankings)->sortByDesc('total')->values();
    
        foreach ($rankedList as $index => $rank) {
            $rank->ranking = $index + 1;
            $rank->is_ranked = $index < $penerima;
            $rank->status = $rank->is_ranked ? 'Yes' : 'No';
        }
    
        // Simpan semua ranking ke database
        Ranking::upsert($rankedList->toArray(), ['penilaian_id', 'receiver_id']);
    }

    private function calculateTotal(NilaiAkhir $nilaiAkhir)
{
    return collect([
        $nilaiAkhir->umur,
        $nilaiAkhir->pekerjaan,
        $nilaiAkhir->penghasilan,
        $nilaiAkhir->status_tempat_tinggal,
        $nilaiAkhir->status_perkawinan,
        $nilaiAkhir->jumlah_tanggungan,
        $nilaiAkhir->keadaan_rumah,
        $nilaiAkhir->disabilitas,
        $nilaiAkhir->pendidikan,
        $nilaiAkhir->fasilitas_mck,
        $nilaiAkhir->bahan_bakar_harian,
        $nilaiAkhir->kepemilikan_kendaraan,
    ])
    ->map(function ($value) {
        return $value ?? 0;
    })
    ->sum();
}
}
