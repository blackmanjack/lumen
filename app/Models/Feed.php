<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
	protected $table = 'feed';

	public $timestamps = false;

	protected $fillable = [
		'value'
	];

	public function node()
    {
        return $this->belongsTo(Node::class, 'id_node');
    }
}