<?php

namespace App\Imports;

use App\Models\TcSample;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SampleForImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    // public function model(array $row)
    // {
    //     dd($row[0]);
    //     return new TcSample([
    //         'sample_number' => $row[0],
    //         'master_treefile_id' => $row[1],
    //         'resample' => $row[2],
    //         'program' => $row[3],
    //         'desc' => $row[4],
    //     ]);
    // }
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $value) {
            if($key!=0){
                $data[] = [
                    'sample_number' => $value[0],
                    'master_treefile_id' => $value[1],
                    'program' => $value[2],
                    'desc' => $value[3],
                    'created_at' => Carbon::createFromFormat('d/m/Y', $value[4])->format('Y-m-d'),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        TcSample::insert($data);
    }
}
