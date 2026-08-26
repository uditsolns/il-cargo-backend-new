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

    protected $primaryKey = "id";
    protected $fillable = [
        "veh_reg_no",
        "cargo_unit_serial_no",
        "driver_lic_no",
        "veh_fitness_cert",
        "veh_carrying_capacity",
        "invoice",
        "packing_list",
        "serial_no",
        "invoice_value",
        "dispatch_lat",
        "dispatch_long",
        "destination_long",
        "destination_lat",
        "value_add",
        "date_transit",
        "pending_servey",
        "created_at",
        "updated_at",
        "pending_servey",
        "address",
        "group_id",
        "origin_pin",
        "destination_pin",
        "flat_track_number",
        "dispatch_type",
        "destination_address",
        "dispatch_id",
        "group_id",
        "channel_partner_id",
        "user_id",
        "consignee_id",
        "remarks",
        "dl_no",
        "dl_dob",
        "driver_aadhaar_no",
        "is_rc_verified",
        "is_dl_verified",
        "is_aadhaar_verified",
        "is_verification_done",
        "driver_id",
        "driver_email",
        "driver_mobile_no",
        "driver_videos_status",
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function photographs()
    {
        return $this->hasMany(Photograph::class, "cargo_id");
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class, "cargo_id");
    }

    public function checklistPhotos()
    {
        return $this->hasMany(ChecklistPhoto::class);
    }

    public function consignee(): BelongsTo
    {
        return $this->belongsTo(User::class, "consignee_id");
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, "driver_id");
    }

    public function videoTutorials()
    {
        return $this->belongsToMany(
            VideoTutorial::class,
            "cargo_detail_video",
        )->withTimestamps();
    }

    /**
     * Same visibility rule already used by CargoDetailController::index() -
     * extracted here so other endpoints (e.g. the per-trip video/test
     * checklist) can authorize against a single trip without duplicating
     * the role/group logic.
     */
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->is_admin == 1) {
            return $query;
        }

        if ($user->user_status == 1) {
            return $query->where("channel_partner_id", $user->channel_partner_id);
        }

        if (
            in_array($user->role, [
                "Insured's Dispatch Supervisor",
                "Insured's Representative",
            ])
        ) {
            return $query->where("group_id", $user->group_id);
        }

        if ($user->role == "Channel Partner") {
            return $query->where("channel_partner_id", $user->id);
        }

        if ($user->role == "Consignee") {
            return $query->where("consignee_id", $user->id);
        }

        return $query->where("group_id", $user->group_id);
    }
}
