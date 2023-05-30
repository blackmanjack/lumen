<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
	protected $table = 'channel';

	public $timestamps = false;

	protected $fillable = [
		'value'
	];

	public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'id_sensor');
    }
}