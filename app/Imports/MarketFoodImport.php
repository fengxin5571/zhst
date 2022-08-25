<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/10/15
 * Time: 6:10 PM
 */
namespace App\Imports;
use App\Model\MarketFoodPool;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class MarketFoodImport implements ToCollection,WithHeadingRow,WithBatchInserts,WithChunkReading{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row){
            $data=[
                'cid'=>isset($row['菜品分类id'])?(int)$row['菜品分类id']:0,
                'name'=>$row['菜品名称'],
                'description'=>isset($row['菜品简介'])?$row['菜品简介']:'',
                'food_image'=>'',
                'price'=>isset($row['价格'])&&$row['价格']?$row['价格']:0.00,
                'is_new'=>1,
            ];
            MarketFoodPool::create($data);
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