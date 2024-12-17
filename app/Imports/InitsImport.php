<?php

namespace App\Imports;

use App\Models\TcCallusOb;
use App\Models\TcInit;
use App\Models\TcInitBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class InitsImport implements ToCollection
{
    public function collection(Collection $rows)
    {

        $qLastId = TcInit::select('id')->orderBy('id','desc')->get();
        if($qLastId->count()==0){
            $initId = 1;
        }else{
            $initId = $qLastId->first()->id + 1;
        }

        $qLastId = TcInitBottle::select('id')->orderBy('id','desc')->get();
        if($qLastId->count()==0){
            $initBottleId = 1;
        }else{
            $initBottleId = $qLastId->first()->id + 1;
        }

        foreach ($rows as $key => $value) {
            if ($key > 0) {
                $data = [
                    // 'id' => $initId++,
                    'tc_sample_id' => $value[0],
                    'tc_room_id' => $value[1],
                    'number_of_block' => 60,
                    'number_of_bottle' => 8,
                    'number_of_plant' => 3,
                    'desc' => $value[2],
                    'date_work' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                    'created_at' => Carbon::createFromFormat('d/m/Y', $value[3])->format('Y-m-d'),
                ];

                $q = TcInit::create($data);
                unset($data);
                $id = $q->id;

                $startBottle = 1;
                for ($i = 1; $i <= 60; $i++) {
                    for ($j = $startBottle; $j < ($startBottle + 8); $j++) {
                        $dtBottle[] = [
                            // 'id' => $initBottleId++,
                            'tc_init_id' => $id,
                            'block_number' => $i,
                            'bottle_number' => $j,
                            'tc_worker_id' => 0,
                            'tc_laminar_id' => 0,
                            'tc_medium_stock_id' => 0,
                            'status' => 1,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                        // TcInitBottle::create($dtBot);
                    }
                    TcInitBottle::insert($dtBottle);
                    unset($dtBottle);
                    $startBottle = $j;
                }


                $dtCalluOb['tc_init_id'] = $id;
                $dtCalluOb['date_schedule'] = Carbon::now();
                $dtCalluOb['date_ob'] = Carbon::now();
                $dtCalluOb['status'] = 0;
                TcCallusOb::create($dtCalluOb);
                unset($dtCalluOb);
            }
        }
    }
}
