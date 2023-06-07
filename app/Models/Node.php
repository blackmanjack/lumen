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

    /**
     * Get the field_sensor attribute as an array.
     *
     * @param  string  $value
     * @return array|null
     */
    public function getFieldSensorAttribute($value)
    {
        if ($value === null) {
            return null;
        }

        $fieldSensorArray = explode(',', str_replace(['{', '}'], '', $value));

        return $fieldSensorArray;
    }

    /**
     * Get the id_hardware_sensor attribute as an array.
     *
     * @param  string  $value
     * @return array|null
     */
    public function getIdHardwareSensorAttribute($value)
    {
        if ($value === null) {
            return null;
        }

        $fieldIdHardwareSensor = explode(',', str_replace(['{', '}'], '', $value));
        // Loop through each element in the array
        foreach ($fieldIdHardwareSensor as $key => $element) {
            // if ($element === "NULL") {
            //     $fieldIdHardwareSensor[$key] = null;
            // }
            if (is_numeric($element)) {
                // Convert the element to the appropriate numeric type
                if (strpos($element, '.') !== false) {
                    $fieldIdHardwareSensor[$key] = floatval($element);
                } else {
                    $fieldIdHardwareSensor[$key] = intval($element);
                }
            }
            // // Check if the element is "NULL"
            // if ($element === "NULL") {
            //     $fieldIdHardwareSensor[$key] = "NULL";
            // } else {
            //     // Check if the element is numeric
            //     if (is_numeric($element)) {
            //         // Convert the element to the appropriate numeric type
            //         if (strpos($element, '.') !== false) {
            //             $fieldIdHardwareSensor[$key] = floatval($element);
            //         } else {
            //             $fieldIdHardwareSensor[$key] = intval($element);
            //         }
            //     }
            // }
        }
        return $fieldIdHardwareSensor;
    }

	public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

	public function hardware()
    {
        return $this->belongsTo(Hardware::class, 'id_hardware');
    }

	// public function sensor()
    // {
    //     return $this->hasMany(Sensor::class, 'id_sensor');
    // }
}