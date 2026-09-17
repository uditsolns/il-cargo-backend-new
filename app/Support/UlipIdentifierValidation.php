<?php

namespace App\Support;

/**
 * The two identifier formats ULIP lookups need, used as pre-flight
 * validation before the FASTag/container polling job calls the proxy -
 * cargo_unit_serial_no/veh_reg_no are free-form on dispatch creation (not
 * every dispatch is an ISO container/has a plate in this exact shape), so
 * this only gates whether a *lookup* is attempted, not what's stored.
 * Patterns match ulip-apis's own request rules (ContainerTrackingRequest,
 * FastagRequest).
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
