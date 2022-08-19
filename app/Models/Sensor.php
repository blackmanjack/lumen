<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
	protected $table = 'sensors';

	protected $fillable = [
		'name',
		'unit',
	];

	public function node()
    {
        return $this->belongsTo(Node::class);
    }

	public function hardware()
    {
        return $this->belongsTo(Hardware::class);
    }

	public function channel()
    {
        return $this->hasMany(Channel::class);
    }
}