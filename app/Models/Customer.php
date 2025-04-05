<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        "name",
        "email",
        "phone_number"
    ];

    protected array $searchableFields = ["name", "email", "phone_number"];

    public function sops(): HasMany {
        return $this->hasMany(Sop::class);
    }
}
