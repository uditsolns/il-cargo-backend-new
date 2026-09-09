<?php

namespace App\Http\Requests;

use App\Models\Group;
use App\Support\UlipIdentifierValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCargoDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        // Channel-partner-flagged accounts don't reliably resolve a
        // group_id today (CargoDetailController::store()'s own branching
        // leaves it null/optional for them) - untouched deliberately, per
        // explicit instruction not to change that flow yet. Every other
        // role must resolve a real group, since the policy-expiry check
        // below depends on it.
        $isChannelPartnerFlow = $user
            && ($user->role === "Channel Partner" || $user->user_status == 1);

        return [
            "cargo_unit_serial_no" => [
                "required",
                "string",
                "regex:" . UlipIdentifierValidation::CONTAINER_NUMBER_REGEX,
                "unique:cargo_details,cargo_unit_serial_no",
            ],
            "group_id" => $isChannelPartnerFlow
                ? ["nullable", "exists:groups,id"]
                : ["required", "exists:groups,id"],
            "estimated_date_of_arrival" => [
                "required",
                "date",
                "after_or_equal:date_transit",
            ],
            "driver_name" => "nullable|string",
            "driver_email" => "nullable|email",
            "driver_mobile_no" => "nullable|string",
            "video_tutorial_ids" => "nullable|array",
            "video_tutorial_ids.*" => "exists:video_tutorials,id",
        ];
    }

    public function messages(): array
    {
        return [
            "cargo_unit_serial_no.regex" => "The cargo unit serial no must be a valid container number (e.g. HLBU8163708).",
            "cargo_unit_serial_no.unique" => "This cargo unit serial no has already been used on another dispatch.",
        ];
    }

    /**
     * Block dispatch creation when the resolved group's policy has an
     * explicit, past expiry date. A null expiry (no policy on file yet)
     * does not block anything - see Group::isPolicyExpired().
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has("group_id")) {
                return;
            }

            $group = Group::find($this->input("group_id"));

            if ($group && $group->isPolicyExpired()) {
                $validator->errors()->add(
                    "group_id",
                    "This customer's policy has expired; dispatch creation is blocked.",
                );
            }
        });
    }
}
