# Dispatcher visibility scoped by trip's group, not by trip creator

Trips are visible for review (video/test progress) based on `cargo_details.group_id`, matching the existing `CargoDetail::index()` rule — not restricted to `cargo_details.user_id`, the literal creator. We considered creator-only scoping, since the trip does record a specific creator, but rejected it: it would just get worked around by shared logins whenever the creator is unavailable, and it would diverge from how trip visibility already works everywhere else in the app.
