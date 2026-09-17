# Drop the container-number format validation on dispatch creation

ADR 0002 added a regex requiring `cargo_unit_serial_no` to look like an ISO-6346 container number (`^[A-Z]{4}[0-9]{4,7}$`) on dispatch creation, reasoning that a lot of existing data wasn't a valid container number and treating that as bad input worth rejecting. That assumption was wrong: per client clarification, this field is not always a container — a dispatch can just as well be a truck or another kind of unit, and `cargo_unit_serial_no` is expected to hold whatever identifier applies. The format wasn't legacy bad data to clean up; it's a field that's supposed to be free-form.

The regex is removed from `StoreCargoDetailRequest`. `required` and `unique:cargo_details,cargo_unit_serial_no` stay — ADR 0002's actual goal (stop the old upsert-on-duplicate-serial behavior) didn't depend on the format check and remains correct.

The container-number format check itself isn't deleted — `App\Support\UlipIdentifierValidation::isValidContainerNumber()` still exists and is still exactly right for its real job: gating whether the FASTag/container ULIP polling job (ADR 0003) attempts a container lookup at all. A dispatch's serial number either looks like a container and gets polled, or it doesn't and is silently skipped — that's a capability check, not an input constraint.

## Consequences

The [Final report](0004-final-report-straight-line-map-and-send-once.md)'s Container Tracking section is updated to match: it's omitted entirely (no heading, no "no data" placeholder) when a dispatch has no container tracking data, rather than showing "unavailable" as if every dispatch should have one. FASTag's section is unaffected — every dispatch has a vehicle, so "no FASTag data yet" is still a meaningful, temporary state worth surfacing there.
