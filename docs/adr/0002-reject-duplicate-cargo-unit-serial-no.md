# Reject duplicate cargo unit serial no instead of upserting

Dispatch creation previously kept `cargo_unit_serial_no` as the key of an `updateOrCreate`, so resubmitting an existing serial no silently updated the prior dispatch rather than creating a new one — and the field had no format or uniqueness validation at all. We now require `cargo_unit_serial_no` on creation, validate it against the ISO-6346 container-number format, and reject creation outright (422) when it already exists on another dispatch, replacing the upsert-on-resubmit behavior entirely. This is what the client asked for explicitly ("do not allow dispatch creation with duplicate cargo unit serial no"), and a lot of existing data isn't even a valid container number, so the old silent-merge behavior was masking bad input rather than preventing it.

## Consequences

Legacy dispatches with invalid or duplicate serial numbers are left untouched — this validation only applies going forward, at creation. Any workflow that relied on resubmitting the same serial no to update an existing dispatch must use the dedicated update endpoint instead.
