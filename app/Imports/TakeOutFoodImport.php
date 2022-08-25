<?php

namespace App\Imports;

use App\Model\TakeFoodPool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TakeOutFoodImport implements ToCollection,WithHeadingRow,WithBatchInserts,WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {

        foreach ($rows as $row){
            $data=[
                'cid'=>isset($row['菜品分类id'])?(int)$row['菜品分类id']:0,
                'name'=>$row['菜品名称'],
                'description'=>isset($row['菜品简介'])?$row['菜品简介']:'',
                'food_image'=>'',
                'stock'=>(int)($row['库存']>0?$row['库存']:1),
                'ot_price'=>isset($row['价格'])&&$row['价格']?$row['价格']:0.00,
                'price'=>isset($row['价格'])&&$row['价格']?$row['价格']:0.00,
                'is_new'=>0
            ];
            TakeFoodPool::create($data);
        }
    }
    public function batchSize(): int
    {
        return 300;
    }
    public function chunkSize(): int
    {
        return 1000;
    }

}
