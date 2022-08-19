<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hardware extends Model
{
	protected $table = 'hardwares';

	protected $fillable = [
		'name',
		'type',
        'description'
	];

	public function node()
    {
        return $this->hasMany(Node::class);
    }

	public function sensor()
    {
        return $this->hasMany(Sensor::class);
    }
}