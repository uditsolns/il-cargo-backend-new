# IL Cargo

Tracks cargo dispatches (trips) end-to-end: who they belong to, the drivers assigned to them, and the tutorial videos/tests that gate driver readiness.

## Language

### Customers, Groups & Policies

**Customer** (business term, as used by the client/stakeholders):
The insured company a dispatch is done on behalf of. In code this is the `Group` model (name, address, GST, `channel_partner_id`) — the entity `cargo_details.group_id` belongs to and the entity all existing visibility rules scope access by.
_Avoid_: The `Customer` Eloquent model/table — despite the name, it is unrelated to dispatches (no relationship to `CargoDetail` at all) and is only used for SOPs. Do not add policy/insurance fields there; it is not what stakeholders mean by "customer."

**Container number**:
The shipping container's identifier, in ISO-6346-style format (e.g. `HLBU8163708`), when the dispatch actually is a container shipment. Stored in `cargo_details.cargo_unit_serial_no` — the field name is legacy/misleading, and the value is **not always a container number**: the same field also holds truck/other unit identifiers for non-container dispatches, so it's required and globally unique on dispatch creation (rejected on a duplicate) but deliberately has no format validation. Whether a given dispatch's value looks like a real container number is only checked later, as a pre-flight gate before the FASTag/container ULIP polling job attempts a lookup (`App\Support\UlipIdentifierValidation::isValidContainerNumber()`) — a dispatch whose value doesn't match never gets container tracking data, and the Final report (below) simply omits that section rather than showing "no data" for a dispatch that was never a container shipment.
_Avoid_: "Cargo unit serial no" as user-facing language when the dispatch is a container shipment. Assuming every dispatch's `cargo_unit_serial_no` is a container number — it frequently isn't.

**Policy**:
A Group's insurance policy, identified by `policy_no` with a `policy_expiry_date`. A null expiry means no policy is on file yet and does not block anything; a dispatch can only be blocked from creation when the Group's policy has an explicit expiry date that has already passed.
_Avoid_: Treating "no policy on file" the same as "expired" — they are different states with different outcomes.

### Support

**Support ticket**:
A query or complaint raised by a user, tracked as subject/description/status through to resolution. Belongs to the raising user's Group, the same way a Trip does.
_Avoid_: "Query" as a separate concept from "ticket" — the client's phrasing ("Query & ticket ID") names one thing, not two: the ticket record itself, referenced by its ticket ID.

### Reporting

**Trip completion**:
A Trip is "completed" when `cargo_details.pending_servey == 1`. The column's own name and the (now-removed) reminder email it used to trigger both read as the opposite ("a survey is still pending") — resolved by client confirmation to mean completion, matching the job's actual name (`CargoCompleted`). Do not rename the column; it's used this way everywhere in the app already.
_Avoid_: Assuming "pending" in `pending_servey` means "not yet done" — that reading is wrong for this codebase.

**Inspection report**:
The on-demand PDF (`GET /cargo-details/{id}/report`, `InspectionReportMail`) — dispatch details, customer details, SOP checklist results, and a photo-GPS location map. Available any time, not tied to trip completion.
_Avoid_: Confusing this with the Final report below — they're separate PDFs from separate Mailables, sharing only the compliance-summary/location-map logic (`App\Mail\Concerns\BuildsInspectionReportData`).

**Final report**:
The completion PDF (`GET /cargo-details/{id}/final-report`, `FinalReportMail`) — everything the Inspection report has, plus a shipment route map (origin→destination) and FASTag/container tracking data. Emailed automatically, exactly once per trip, when the trip is marked completed (see `CargoCompleted` and `cargo_details.final_report_sent_at`).

### Video Tutorial & Driver Training

**Trip**:
A `CargoDetail` record — one dispatch/consignment, with its own vehicle, driver, and route.
_Avoid_: Cargo detail, dispatch, consignment (as user-facing language)

**Dispatcher**:
A `User` with `role = "Insured's Representative"`. Visibility is by `group_id`, not by whoever created the trip (`cargo_details.user_id`) — any dispatcher sharing the trip's group can view and manage it, including reviewing its driver's video/test progress, via the same rule `CargoDetail::index()` already uses. Note: that same group_id rule is also applied, unchanged, to `role = "Insured's Dispatch Supervisor"` and to any other non-admin/non-partner/non-consignee role — they get identical trip visibility by sharing the code path, not because they're considered "the dispatcher" persona. The dispatcher is also who runs the Assisted flow (below) for a driver with no phone — there is no separate staff persona for this.
_Avoid_: Creator, trip owner, staff

**Driver**:
A `User` with `role = Driver`, tied to trips via `driver_id`. Only sees tutorial videos assigned to their own trips — never other trip/dispatch data.

**Assisted flow**:
When a driver has no personal phone, the driver still performs every action themselves — selfie, watching, answering test questions — but on the dispatcher's device/session. There is no separate "staff" persona: the dispatcher is the one who facilitates this, using their own login. They do not act on the driver's behalf.
_Avoid_: "Staff", "staff member", "staff completes on driver's behalf", "staff answers for the driver"

**driver_videos_status**:
An advisory rollup on `CargoDetail` (`null` / `pending` / `completed`) summarizing whether the assigned driver has finished all required videos. It does not block dispatch or any other action.
