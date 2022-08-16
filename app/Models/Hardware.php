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
}