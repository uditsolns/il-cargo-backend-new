<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CargoDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = [
        'veh_reg_no',
        'cargo_unit_serial_no',
        'driver_lic_no',
        'veh_fitness_cert',
        'veh_carrying_capacity',
        'invoice',
        'packing_list',
        'serial_no',
        'invoice_value',
        'dispatch_lat',
        'dispatch_long',
        'destination_long',
        'destination_lat',
        'value_add',
        'date_transit',
        'pending_servey',
        'created_at',
        'updated_at',
        'pending_servey',
        'address',
        'group_id',
        'origin_pin',
        'destination_pin',
        'flat_track_number',
        'dispatch_type',
        'destination_address',
        'dispatch_id',
        'group_id',
        'channel_partner_id',
        'user_id',
        'consignee_id',
        'remarks',
        'dl_no',
        'dl_dob',
        'driver_aadhaar_no',
        'is_rc_verified',
        'is_dl_verified',
        'is_aadhaar_verified',
        'is_verification_done',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function photographs()
    {
        return $this->hasMany(Photograph::class, 'cargo_id');
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'cargo_id');
    }

   public function checklistPhotos()
    {
        return $this->hasMany(ChecklistPhoto::class);
    }

    public function consignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consignee_id');
    }
}
