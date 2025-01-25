<?php

namespace App\Imports;

use App\Models\TcCallusOb;
use App\Models\TcCallusObDetail;
use App\Models\TcInitBottle;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class CallusImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $value) {

            if ($key != 0) {

                if($value[0]=='<end>'){
                    break;
                }

                $ex_init_id = $value[0];
                $ex_tgl_obs = $value[1];
                $ex_botol_callus = $value[2];
                $ex_daun_callus= $value[3];
                $ex_botol_oxi = $value[4];
                $ex_botol_kontam = $value[5];

                $data = [
                    'tc_worker_id' => 99,
                    'date_schedule' => Carbon::createFromFormat('d/m/Y', $ex_tgl_obs)->format('Y-m-d'),
                    'date_ob' => Carbon::createFromFormat('d/m/Y', $ex_tgl_obs)->format('Y-m-d'),
                    'status' => 1,
                    'bottle_callus' => $ex_botol_callus,
                ];
                $qUpCallusOb = TcCallusOb::where('tc_init_id', '=', $ex_init_id)->first();
                $qUpCallusOb->update($data);
                $obsId = $qUpCallusOb->id;

                // //prepare data obs baru
                $data = [
                    'tc_init_id' => $ex_init_id,
                    'tc_worker_id' => 99,
                    'date_schedule' => Carbon::parse($data['date_ob'])->addMonths(1),
                    'date_ob' => Carbon::parse($data['date_ob'])->addMonths(1),
                    'status' => 0,
                    'bottle_callus' => 0
                ];
                TcCallusOb::create($data);
                unset($data);

                $jlhBotol = $ex_botol_callus + $ex_botol_oxi + $ex_botol_kontam;
                $botol = TcInitBottle::query()
                    ->select('id')
                    ->where('tc_init_id', $ex_init_id)
                    ->orderBy('id', 'ASC')
                    ->take($jlhBotol)
                    ->get()->toArray();

                $btl['callus'] = (array_chunk($botol, $ex_botol_callus))[0];
                $btl['oxi'] = array_chunk(((array_chunk($botol, $ex_botol_callus))[1]), $ex_botol_oxi)[0];
                $btl['contam'] = array_chunk(((array_chunk($botol, $ex_botol_callus))[1]), $ex_botol_kontam)[0];

                $dataWajib = [
                    'tc_init_id' => $ex_init_id,
                    'tc_callus_ob_id' => $obsId
                ];

                $indexBotol = 0;
                $indexPlant = 1;
                for ($i=0; $i < $ex_daun_callus; $i++) {
                    $dt['callus'][$i] = $dataWajib;
                    $dt['callus'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                    $dt['callus'][$i]['explant_number'] = $indexPlant;
                    $dt['callus'][$i]['result'] = 1;
                    $dt['callus'][$i]['created_at'] = Carbon::now();
                    $dt['callus'][$i]['updated_at'] = Carbon::now();
                    if($indexBotol == $ex_botol_callus-1){
                        $indexBotol = 0;
                        $indexPlant++;
                    }else{
                        $indexBotol++;
                    }
                }
                TcCallusObDetail::insert($dt['callus']);
                unset($dt);

                $indexBotol = $ex_botol_callus;
                $indexPlant = 1;
                for ($i=0; $i < $ex_botol_oxi*3; $i++) {
                    $dt['oxi'][$i] = $dataWajib;
                    $dt['oxi'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                    $dt['oxi'][$i]['explant_number'] = $indexPlant;
                    $dt['oxi'][$i]['result'] = 2;
                    $dt['oxi'][$i]['created_at'] = Carbon::now();
                    $dt['oxi'][$i]['updated_at'] = Carbon::now();
                    if($indexBotol == $ex_botol_oxi+$ex_botol_callus-1){
                        $indexBotol = $ex_botol_callus;
                        $indexPlant++;
                    }else{
                        $indexBotol++;
                    }
                }
                TcCallusObDetail::insert($dt['oxi']);
                unset($dt);

                $indexBotol = $ex_botol_callus+$ex_botol_oxi;
                $indexPlant = 1;
                for ($i=0; $i < $ex_botol_kontam*3; $i++) {
                    $dt['contam'][$i] = $dataWajib;
                    $dt['contam'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                    $dt['contam'][$i]['explant_number'] = $indexPlant;
                    $dt['contam'][$i]['result'] = 3;
                    $dt['contam'][$i]['tc_contamination_id'] = 1;
                    $dt['contam'][$i]['created_at'] = Carbon::now();
                    $dt['contam'][$i]['updated_at'] = Carbon::now();
                    if($indexBotol == $ex_botol_kontam+$ex_botol_callus+$ex_botol_oxi-1){
                        $indexBotol = $ex_botol_callus+$ex_botol_oxi;
                        $indexPlant++;
                    }else{
                        $indexBotol++;
                    }
                }
                TcCallusObDetail::insert($dt['contam']);
                unset($dt);
            }

        }
    }
}
