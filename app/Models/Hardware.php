<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hardware extends Model
{
	protected $table = 'hardware';

	protected $fillable = [
		'name',
		'type',
        'description'
	];

    protected $primaryKey = 'id_hardware';

    public $timestamps = false;

	public function node()
    {
        return $this->hasMany(Node::class, 'id_hardware');
    }

	// public function sensor()
    // {
    //     return $this->hasMany(Sensor::class, 'id_hardware');
    // }
}