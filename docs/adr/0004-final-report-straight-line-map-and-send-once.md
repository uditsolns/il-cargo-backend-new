# Final report: straight-line route map, sent exactly once

The final report's shipment route map draws a straight line between the dispatch's raw origin/destination coordinates using the Static Maps API's own `path` parameter, rather than fetching a road-following polyline from Google's Routes API (the current replacement for the now-deprecated legacy Directions API) first. For "instantly legible: this cargo went from A to B" in a compliance PDF, a straight connecting line with two clearly labeled/colored markers communicates that at a glance at least as well as a road-accurate route, without a second API dependency, an extra network call, or the added failure mode of a routing call failing independently of the map image call.

Separately, `CargoCompleted` (the job that emails this report) previously ran every minute against `pending_servey = 1` with no "already sent" tracking and only looked at the single latest matching row — meaning it would have either re-sent the same email every minute forever, or silently skipped every dispatch that wasn't currently the most recent one. `cargo_details.final_report_sent_at` is set once the email is sent (or once a dispatch with no configured recipient emails has been considered, so it isn't re-checked forever either), and the job now processes every unsent completed dispatch on each run instead of just the latest.

## Consequences

The route map will look identical for a long, winding actual route and a short direct one between the same two endpoints — acceptable here since the goal is confirming origin/destination, not visualizing the actual path taken. If a dispatch's `final_report_sent_at` is ever cleared (e.g. by direct DB edit), the next run will re-send its final report.
