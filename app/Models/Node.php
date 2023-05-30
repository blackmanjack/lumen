<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
	protected $table = 'node';

	protected $fillable = [
		'name',
		'location',
	];

    public $timestamps = false;
    protected $primaryKey = 'id_node';

	public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

	public function hardware()
    {
        return $this->belongsTo(Hardware::class, 'id_hardware');
    }

	public function sensor()
    {
        return $this->hasMany(Sensor::class, 'id_sensor');
    }
}