<?php

namespace App\Imports;

use App\Models\Shape;
use App\Models\StarMelee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportStarMelee implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 3;
    }
    public function collection(Collection $rows) {
        foreach ($rows as $row)
        {
            if($row[3] != null){
                $shape = $row[2] == null ? $row[1] : $row[2];
                // $shapefound = Shape::where('name' , $shape)->first();
                if($row[1] == 'ROUND' || $row[1] == 'FANCY LAYOUT'){
                    $data['shape'] = $shape;
                    $data['size'] = $row[3] ?? null;
                    $data['sieve'] = $row[4] ?? null;
                    $data['carat'] = $row[5] ?? null;
                    $data['def_vvs_vs'] = $row[6] ?? 0;
                    $data['def_vs_si'] = $row[7] ?? 0;
                    $data['fg_vvs_vs'] = $row[8] ?? 0;
                    $data['fg_vs_si'] = $row[9] ?? 0;
                    $data['pink_vvs_vs_si1'] = $row[10] ?? 0;
                    $data['yellow_vvs_vs_si1'] = $row[11] ?? 0;
                    $data['blue_vvs_vs_si1'] = $row[12] ?? 0;
                    $StarMelee = StarMelee::where('shape', $shape)->where('carat', $row[5])->first();
                    if($StarMelee){
                        $StarMelee->update($data);
                    } else {
                        StarMelee::create($data);
                    }
                }
            }
        }
    }
}
