<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
	protected $table = 'nodes';

	protected $fillable = [
		'name',
		'location',
	];

	public function hardware()
    {
        return $this->belongsTo(Hardware::class);
    }

	public function sensor()
    {
        return $this->hasMany(Sensor::class);
    }
}