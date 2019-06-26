<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 26 Jun 2019 09:14:53 +0800.
 */

namespace App\Model;

use Illuminate\Support\Facades\Storage;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Discover
 * 
 * @property int $id
 * @property string $title
 * @property string $images
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Discover extends Eloquent
{
	protected $table = 'discover';

	protected $fillable = [
		'title',
		'images',
		'description'
	];
	//删除后删掉对应的图片文件
	public static function boot(){
	    parent::boot();
	    static ::deleted(function($discover){
            Storage::disk('admin')->delete($discover->images);
        });
    }
	public function setImagesAttribute($image){
        if (is_array($image)) {
            $this->attributes['images'] = json_encode($image);
        }
    }
    public function getImagesAttribute($image){
	    return json_decode($image,true);
    }
}
