<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Zone;

class Photograph extends Model
{
    use HasFactory;
     use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'photo',
        'time',
        'longitude',
        'latitude',
        'video',
        'cargo_id',
        'zone_id',
        'reason',
        'phase_id',
        'palette_no',
        // Add other fields as needed
    ];
    protected $primaryKey = 'id';
    // other fields for photographs

    public function cargo()
    {
        return $this->belongsTo(CargoDetail::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }
}
