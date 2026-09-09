<?php

namespace App\Support;

/**
 * The two identifier formats ULIP lookups need - shared between dispatch
 * creation (container number format/uniqueness) and the FASTag/container
 * polling job (pre-flight validation before calling the proxy), so the two
 * can never drift apart. Patterns match ulip-apis's own request rules
 * (ContainerTrackingRequest, FastagRequest).
 */
class UlipIdentifierValidation
{
    public const CONTAINER_NUMBER_REGEX = '/^[A-Z]{4}[0-9]{4,7}$/';

    public const VEHICLE_NUMBER_REGEX = '/^[A-Z0-9]{5,11}$|^[A-Z0-9]{17,20}$/';

    public static function isValidContainerNumber(?string $value): bool
    {
        return $value !== null && preg_match(self::CONTAINER_NUMBER_REGEX, $value) === 1;
    }

    public static function isValidVehicleNumber(?string $value): bool
    {
        return $value !== null && preg_match(self::VEHICLE_NUMBER_REGEX, $value) === 1;
    }
}
