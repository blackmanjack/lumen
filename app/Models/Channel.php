<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
	protected $table = 'channels';

	protected $fillable = [
		'value'
	];

	public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}