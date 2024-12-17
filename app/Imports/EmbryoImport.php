<?php

namespace App\Imports;

use App\Models\TcEmbryoBottle;
use App\Models\TcEmbryoList;
use App\Models\TcEmbryoOb;
use App\Models\TcEmbryoObDetail;
use App\Models\TcEmbryoTransferBottle;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmbryoImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $qTcEmbryoBottleId = TcEmbryoBottle::select('id')->orderBy('id','desc')->first();
        $lastTcEmbryoBottleId = is_null($qTcEmbryoBottleId)?1:$qTcEmbryoBottleId->id+1;

        $qTcEmbryoObsId = TcEmbryoOb::select('id')->orderBy('id','desc')->first();
        $lastTcEmbryoObsId = is_null($qTcEmbryoBottleId)?1:$qTcEmbryoObsId->id+1;

        foreach ($rows as $key => $value) {
            if ($key != 0) {
                $dtTcEmbryoBottle[] = [
                    'id' => $lastTcEmbryoBottleId++,
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => $value[1],
                    'tc_laminar_id' => 0,
                    'sub' => $value[2],
                    'number_of_bottle' => $value[3],
                    'status' => 1,
                    'bottle_date' => Carbon::createFromFormat('d/m/Y', $value[4])->format('Y-m-d'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $dtTcEmbryoObs[] = [
                    'id' => $lastTcEmbryoObsId++,
                    'tc_init_id' => $value[0],
                    'tc_worker_id' => $value[1],
                    'sub' => 1,
                    'total_bottle_embryo' => $value[5],
                    'total_bottle_oxidate' => $value[6],
                    'total_bottle_contam' => $value[7],
                    'total_bottle_other' => $value[8],
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcEmbryoObsDetail[] = [
                    'tc_init_id' => $value[0],
                    'tc_embryo_ob_id' => $lastTcEmbryoObsId-1,
                    'tc_embryo_bottle_id' => $lastTcEmbryoBottleId-1,
                    'tc_worker_id' => $value[1],
                    'bottle_embryo' => $value[5],
                    'bottle_oxidate' => $value[6],
                    'bottle_contam' => $value[7],
                    'bottle_other' => $value[8],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcEmbryoTransferBottle[] = [
                    'tc_init_id' => $value[0],
                    'tc_embryo_ob_id' => $lastTcEmbryoObsId-1,
                    'tc_embryo_bottle_id' => $lastTcEmbryoBottleId-1,
                    'tc_worker_id' => $value[1],
                    'bottle_embryo' => $value[5],
                    'bottle_left' => $value[5],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $dtTcEmbryoObsTemp[] = [
                    'id' => $lastTcEmbryoObsId++,
                    'tc_init_id' => $value[0],
                    'total_bottle_embryo' => 0,
                    'total_bottle_oxidate' => 0,
                    'total_bottle_contam' => 0,
                    'total_bottle_other' => 0,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                

                $dtTcEmbryoList[] = [
                    'tc_init_id' => $value[0],
                    'tc_embryo_ob_id' => $lastTcEmbryoObsId-2,
                    'tc_embryo_bottle_id' => $lastTcEmbryoBottleId-1,
                    'tc_worker_id' => $value[1],
                    'first_total' => $value[3],
                    'last_total' => $value[3] - ($value[6]+$value[7]+$value[8]),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

            }

        }
        DB::unprepared('SET IDENTITY_INSERT tc_embryo_bottles ON');
        DB::table('tc_embryo_bottles')->insert($dtTcEmbryoBottle);
        DB::unprepared('SET IDENTITY_INSERT tc_embryo_bottles OFF');

        DB::unprepared('SET IDENTITY_INSERT tc_embryo_obs ON');
        DB::table('tc_embryo_obs')->insert($dtTcEmbryoObs);
        DB::table('tc_embryo_obs')->insert($dtTcEmbryoObsTemp);
        DB::unprepared('SET IDENTITY_INSERT tc_embryo_obs OFF');

        DB::table('tc_embryo_ob_details')->insert($dtTcEmbryoObsDetail);
        DB::table('tc_embryo_lists')->insert($dtTcEmbryoList);
        TcEmbryoTransferBottle::insert($dtTcEmbryoTransferBottle);
    }
}
