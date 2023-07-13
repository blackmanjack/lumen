<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
	protected $table = 'sensor';

	protected $fillable = [
		'name',
		'unit',
	];

    public $timestamps = false;
    protected $primaryKey = 'id_sensor';

	public function node()
    {
        return $this->belongsTo(Node::class, 'id_node');
    }

	public function hardware()
    {
        return $this->belongsTo(Hardware::class, 'id_hardware');
    }

	public function channel()
{
    return $this->hasMany(Channel::class, 'id_sensor');
}
}