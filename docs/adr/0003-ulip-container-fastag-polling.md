# Poll ulip-apis on a schedule for container/FASTag data

Container and FASTag data come from India's government ULIP APIs, which require a whitelisted India-based server IP. A sibling Laravel project, `ulip-apis`, already exists as a shared proxy other internal projects call for this (its own lack of consumer-side authentication is tracked separately and is out of scope for il-cargo). We poll `ulip-apis` on a 6-hour schedule for dispatches still in progress (`pending_servey` null/0), rather than calling the government API directly (blocked by the IP requirement) or fetching on demand at request time. We validate `cargo_unit_serial_no` locally against the container-number format before calling the container endpoint, to avoid wasting calls on invalid legacy data. Results are appended as deduplicated history records rather than overwritten as a single latest-snapshot, since both FASTag transactions and container milestones are inherently historical events, not current-state values.

## Consequences

A dispatch's container/FASTag data lags reality by up to 6 hours rather than being real-time, and il-cargo's data freshness now depends on `ulip-apis`'s uptime (and eventually its own security fix).
