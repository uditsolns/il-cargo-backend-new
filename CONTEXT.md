# IL Cargo

Tracks cargo dispatches (trips) end-to-end: who they belong to, the drivers assigned to them, and the tutorial videos/tests that gate driver readiness.

## Language

### Customers, Groups & Policies

**Customer** (business term, as used by the client/stakeholders):
The insured company a dispatch is done on behalf of. In code this is the `Group` model (name, address, GST, `channel_partner_id`) — the entity `cargo_details.group_id` belongs to and the entity all existing visibility rules scope access by.
_Avoid_: The `Customer` Eloquent model/table — despite the name, it is unrelated to dispatches (no relationship to `CargoDetail` at all) and is only used for SOPs. Do not add policy/insurance fields there; it is not what stakeholders mean by "customer."

**Container number**:
The shipping container's identifier, in ISO-6346-style format (e.g. `HLBU8163708`). Stored in `cargo_details.cargo_unit_serial_no` — the field name is legacy/misleading, but this is what it holds for container-type dispatches. Required and format-validated on dispatch creation, and globally unique across all dispatches (creation is rejected on a duplicate — not the pre-existing silent-upsert behavior).
_Avoid_: "Cargo unit serial no" as user-facing language when the dispatch is a container shipment.

**Policy**:
A Group's insurance policy, identified by `policy_no` with a `policy_expiry_date`. A null expiry means no policy is on file yet and does not block anything; a dispatch can only be blocked from creation when the Group's policy has an explicit expiry date that has already passed.
_Avoid_: Treating "no policy on file" the same as "expired" — they are different states with different outcomes.

### Support

**Support ticket**:
A query or complaint raised by a user, tracked as subject/description/status through to resolution. Belongs to the raising user's Group, the same way a Trip does.
_Avoid_: "Query" as a separate concept from "ticket" — the client's phrasing ("Query & ticket ID") names one thing, not two: the ticket record itself, referenced by its ticket ID.

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
