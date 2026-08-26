# Video Tutorial & Driver Training

Tracks tutorial videos assigned to trips, drivers' progress watching them, and the MCQ comprehension tests that gate that progress.

## Language

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
