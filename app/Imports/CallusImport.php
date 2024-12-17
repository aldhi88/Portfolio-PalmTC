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
                $data = [
                    'tc_worker_id' => $value[1],
                    'date_schedule' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
                    'date_ob' => Carbon::createFromFormat('d/m/Y', $value[2])->format('Y-m-d'),
                    'status' => 1,
                    'bottle_callus' => $value[3],
                ];
                $qUpCallusOb = TcCallusOb::where('tc_init_id', '=', $value[0])->first();
                $qUpCallusOb->update($data);
                $obsId = $qUpCallusOb->id;
                // dd($obsId);
                // dd(0);

                // //prepare data obs baru
                $data = [
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => $value[1],
                    'date_schedule' => Carbon::parse($data['date_ob'])->addMonths(1),
                    'date_ob' => Carbon::parse($data['date_ob'])->addMonths(1),
                    'status' => 0,
                    'bottle_callus' => 0
                ];
                TcCallusOb::create($data);
                unset($data);

                $jlhBotol = $value[3] + $value[5] + $value[6];
                $botol = TcInitBottle::query()
                    ->select('id')
                    ->where('tc_init_id', $value[0])
                    ->orderBy('id', 'ASC')
                    ->take($jlhBotol)
                    ->get()->toArray();
                
                $btl['callus'] = (array_chunk($botol, $value[3]))[0];
                $btl['oxi'] = array_chunk(((array_chunk($botol, $value[3]))[1]), $value[5])[0];
                $btl['contam'] = array_chunk(((array_chunk($botol, $value[3]))[1]), $value[6])[0];

                $dataWajib = [
                    'tc_init_id' => $value[0],
                    'tc_callus_ob_id' => $obsId
                ];

                $indexBotol = 0;
                $indexPlant = 1;
                for ($i=0; $i < $value[4]; $i++) { 
                    $dt['callus'][$i] = $dataWajib;
                    $dt['callus'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                    $dt['callus'][$i]['explant_number'] = $indexPlant;
                    $dt['callus'][$i]['result'] = 1;
                    $dt['callus'][$i]['created_at'] = Carbon::now();
                    $dt['callus'][$i]['updated_at'] = Carbon::now();
                    if($indexBotol == $value[3]-1){
                        $indexBotol = 0;
                        $indexPlant++;
                    }else{
                        $indexBotol++;
                    }
                }
                TcCallusObDetail::insert($dt['callus']);
                unset($dt);

                $indexBotol = $value[3];
                $indexPlant = 1;
                for ($i=0; $i < $value[5]*3; $i++) { 
                    $dt['oxi'][$i] = $dataWajib;
                    $dt['oxi'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                    $dt['oxi'][$i]['explant_number'] = $indexPlant;
                    $dt['oxi'][$i]['result'] = 2;
                    $dt['oxi'][$i]['created_at'] = Carbon::now();
                    $dt['oxi'][$i]['updated_at'] = Carbon::now();
                    if($indexBotol == $value[5]+$value[3]-1){
                        $indexBotol = $value[3];
                        $indexPlant++;
                    }else{
                        $indexBotol++;
                    }
                }
                TcCallusObDetail::insert($dt['oxi']);
                unset($dt);

                $indexBotol = $value[3]+$value[5];
                $indexPlant = 1;
                for ($i=0; $i < $value[6]*3; $i++) { 
                    $dt['contam'][$i] = $dataWajib;
                    $dt['contam'][$i]['tc_init_bottle_id'] = $botol[$indexBotol]['id'];
                    $dt['contam'][$i]['explant_number'] = $indexPlant;
                    $dt['contam'][$i]['result'] = 3;
                    $dt['contam'][$i]['tc_contamination_id'] = 1;
                    $dt['contam'][$i]['created_at'] = Carbon::now();
                    $dt['contam'][$i]['updated_at'] = Carbon::now();
                    if($indexBotol == $value[6]+$value[3]+$value[5]-1){
                        $indexBotol = $value[3]+$value[5];
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
