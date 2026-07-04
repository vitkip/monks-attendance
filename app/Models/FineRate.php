<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineRate extends Model
{
    protected $fillable = ['name', 'amount', 'description'];

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}
