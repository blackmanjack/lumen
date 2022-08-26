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

	public function user()
    {
        return $this->belongsTo(User::class);
    }

	public function hardware()
    {
        return $this->belongsTo(Hardware::class);
    }

	public function sensor()
    {
        return $this->hasMany(Sensor::class);
    }
}