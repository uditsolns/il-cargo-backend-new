# New Requirements — API Guide

Base URL: `https://ilcargocare.com/ilcargo-backend-staging/public/api/v1`. Every endpoint below requires header `Authorization: Bearer <token>` (Sanctum) and `Accept: application/json`.

No token / expired token, **any** endpoint, `401`:
```json
{ "message": "Unauthenticated." }
```

**Visibility rule, used throughout**: unless stated otherwise, a regular user only sees rows tied to their own `group_id`; **Admin** (`is_admin == 1`) and **Ground Surveyor** (`role == "Ground Surveyor"`) see everything. "Not visible" always means `404`, never `403`.

Every JSON block below is a real captured response, field-for-field. Where a value differs per request (IDs, timestamps) it's shown as an actual example value, not a placeholder.

---

## 1. Dashboard

### `GET /dashboard`

Query params:

| Param | Type | Required | Notes |
|---|---|---|---|
| `period` | string | no, default `last_7_days` | `last_7_days` \| `month` \| `quarter` \| `year` \| `custom` |
| `year` | integer, 4 digits | if `period` is `month`/`quarter`/`year` | e.g. `2025` |
| `month` | integer 1–12 | if `period=month` | |
| `quarter` | integer 1–4 | if `period=quarter` | 1=Jan-Mar, 2=Apr-Jun, 3=Jul-Sep, 4=Oct-Dec |
| `from_date`, `to_date` | date `Y-m-d` | if `period=custom` | `to_date >= from_date` |

`period=month&year=2025&month=8` returns August 2025, regardless of today's date. Sending `from_date`/`to_date` with no `period` is treated as `custom`.

**Success `200`** (`GET /dashboard`, default period):
```json
{
  "success": true,
  "data": {
    "counts": { "customers": 21, "users": 40, "dispatches": 222, "apis": 3 },
    "api_usage_by_type": { "rc": 0, "dl": 0, "aadhaar": 0 },
    "graphs": {
      "users": [
        { "date": "2026-09-02", "count": 0 },
        { "date": "2026-09-09", "count": 5 }
      ],
      "customers": [
        { "date": "2026-09-02", "count": 0 },
        { "date": "2026-09-09", "count": 3 }
      ],
      "cargo_details": [
        { "date": "2026-09-02", "count": 0 },
        { "date": "2026-09-09", "count": 1 }
      ],
      "invoice_value": [
        { "date": "2026-09-02", "value": 0 },
        { "date": "2026-09-09", "value": 0 }
      ]
    },
    "inspection_summary": { "total_inspections": 0, "compliance_data": [] }
  },
  "filters": { "from_date": "2026-09-02", "to_date": "2026-09-09" }
}
```
(`graphs.*` normally has one entry per day in the range — 8 entries for the default 7-day window; trimmed here to the two with non-zero counts.)

`counts` is always the all-time total — it never changes with `period`. Everything else (`graphs`, `inspection_summary`, `api_usage_by_type`) is filtered to the resolved range. `api_usage_by_type` counts RC/DL verification calls and sums both Aadhaar OTP steps into one `aadhaar` number.

**Error `422`** — invalid `period` value (`GET /dashboard?period=bogus`):
```json
{ "message": "The given data was invalid.", "errors": { "period": ["The selected period is invalid."] } }
```

**Error `422`** — `period=month` with no `month`/`year` (`GET /dashboard?period=month`):
```json
{ "message": "The given data was invalid.", "errors": { "year": ["The year field is required when period is month, quarter, or year."], "month": ["The month field is required when period is month."] } }
```

---

## 2. Support tickets

`/support-tickets` (CRUD) + one extra endpoint. Group-scoped per the visibility rule.

### `POST /support-tickets`

| Field | Type | Rules |
|---|---|---|
| `subject` | string | required, max 255 |
| `description` | string | nullable |
| `cargo_detail_id` | integer | nullable, must exist in `cargo_details` |

`user_id`/`group_id` come from the logged-in user — never send them. `status` always starts `open`.

**Success `201`**:
```json
{
  "support_ticket": {
    "id": 4, "ticket_id": "TCK-000004", "subject": "Broken login",
    "description": "Cannot log in since yesterday", "status": "open",
    "cargo_detail_id": 389, "group_id": 120, "user_id": 195,
    "created_at": "2026-09-09T13:54:03.000000Z", "updated_at": "2026-09-09T13:54:03.000000Z", "deleted_at": null
  }
}
```

**Error `422`** — missing `subject`:
```json
{ "error": { "subject": ["The subject field is required."] } }
```

### `GET /support-tickets`

Query: `status` (`open`/`in_progress`/`resolved`/`closed`, optional), `per_page` (default 15).

**Success `200`**:
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 4, "ticket_id": "TCK-000004", "subject": "Broken login", "description": "Cannot log in since yesterday",
      "status": "open", "cargo_detail_id": 389, "group_id": 120, "user_id": 195,
      "created_at": "2026-09-09T13:54:03.000000Z", "updated_at": "2026-09-09T13:54:03.000000Z", "deleted_at": null,
      "raiser": { "id": 195, "name": "Jane Dispatcher" },
      "cargo_detail": { "id": 389, "dispatch_id": "DISP-PFPRA9GK" }
    }
  ],
  "first_page_url": "https://.../support-tickets?page=1", "from": 1, "last_page": 1,
  "last_page_url": "https://.../support-tickets?page=1",
  "links": [
    { "url": null, "label": "&laquo; Previous", "active": false },
    { "url": "https://.../support-tickets?page=1", "label": "1", "active": true },
    { "url": null, "label": "Next &raquo;", "active": false }
  ],
  "next_page_url": null, "path": "https://.../support-tickets", "per_page": 15, "prev_page_url": null, "to": 1, "total": 1
}
```

### `GET /support-tickets/{id}`

**Success `200`**: same object as one `data[]` row above, wrapped: `{"support_ticket": {...}}`.

**Error `404`** — not visible to you (wrong group) or doesn't exist:
```json
{ "message": "Support ticket not found." }
```

### `PUT /support-tickets/{id}`

| Field | Type | Rules |
|---|---|---|
| `subject` | string | sometimes, required if present, max 255 |
| `description` | string | nullable |
| `status` | string | sometimes, required if present, one of `open`/`in_progress`/`resolved`/`closed` |

`cargo_detail_id`/`user_id`/`group_id` cannot be changed here. Any group member can update, not just the raiser.

**Success `200`**:
```json
{
  "support_ticket": {
    "id": 4, "ticket_id": "TCK-000004", "subject": "Broken login", "description": "Cannot log in since yesterday",
    "status": "resolved", "cargo_detail_id": 389, "group_id": 120, "user_id": 195,
    "created_at": "2026-09-09T13:54:03.000000Z", "updated_at": "2026-09-09T13:54:06.000000Z", "deleted_at": null
  }
}
```

**Error `422`** — invalid `status`:
```json
{ "error": { "status": ["The selected status is invalid."] } }
```

**Error `404`**: same shape as `GET /support-tickets/{id}`'s 404 above.

### `DELETE /support-tickets/{id}`

**Success `200`**:
```json
{ "message": "Support ticket deleted." }
```
Soft-delete. **Error `404`**: same shape as above.

### `GET /support-tickets/pending-count`

**Success `200`**:
```json
{ "pending_count": 3 }
```
Counts `open` + `in_progress` only, scoped like everything else (group total, or global for Admin/Ground Surveyor).

---

## 3. Customer policy (on Groups)

Two new fields on the existing Group endpoints.

### `POST /groups`

Adds to the existing form: `policy_no` (string, nullable), `policy_expiry_date` (date `Y-m-d`, nullable).

**Success `201`**:
```json
{
  "group": {
    "name": "Acme Logistics", "address": "123 Main St", "city": "Mumbai", "gst": "27ABCDE1234F1Z5",
    "photo": null, "sop": null, "additional_emails": null,
    "policy_no": "POL-1001", "policy_expiry_date": "2027-06-30",
    "updated_at": "2026-09-09T13:56:54.000000Z", "created_at": "2026-09-09T13:56:54.000000Z", "id": 123
  }
}
```

**Error `422`** — missing required existing fields (unchanged validation, shown for the error shape):
```json
{ "error": { "address": ["The address field is required."], "city": ["The city field is required."], "gst": ["The gst field is required."] } }
```

### `GET /groups/{id}`

**Success `200`**:
```json
{
  "group": {
    "id": 123, "name": "Acme Logistics", "address": "123 Main St", "city": "Mumbai", "gst": "27ABCDE1234F1Z5",
    "policy_no": "POL-1001", "policy_expiry_date": "2027-06-30",
    "created_at": "2026-09-09T13:56:54.000000Z", "updated_at": "2026-09-09T13:56:54.000000Z",
    "photo": null, "sop": null, "parent_user_id": null, "additional_emails": null, "deleted_at": null, "channel_partner_id": null
  }
}
```

### `POST /groups/{id}` (update)

⚠️ **Response shape differs from create/show — not wrapped in `{"group": ...}`, the object is returned bare:**
```json
{
  "id": 123, "name": "Acme Logistics", "address": "456 New St", "city": "Mumbai", "gst": "27ABCDE1234F1Z5",
  "policy_no": "POL-1001", "policy_expiry_date": "2028-01-01",
  "created_at": "2026-09-09T13:56:54.000000Z", "updated_at": "2026-09-09T13:56:55.000000Z",
  "photo": null, "sop": null, "parent_user_id": null, "additional_emails": null, "deleted_at": null, "channel_partner_id": null
}
```

`policy_expiry_date: null` means unrestricted — see §4. That's the state of every existing Group today.

---

## 4. Dispatch creation

### `POST /cargo-details`

Same endpoint/multipart form as before. New/changed validation only:

| Field | Rules |
|---|---|
| `cargo_unit_serial_no` | required, globally unique across all dispatches — **no format requirement**: it isn't always an ISO container number (could be a truck or other unit), so the value is free-form |
| `group_id` | required, must exist — **except** Channel Partner accounts, unchanged for them |
| `estimated_date_of_arrival` | required, date, must be `>= date_transit` |

**Success `201`**:
```json
{
  "cargo_detail": {
    "cargo_unit_serial_no": "MSCU7654321", "veh_reg_no": "MH12AB1234", "invoice": null, "driver_lic_no": null,
    "packing_list": null, "veh_fitness_cert": null, "serial_no": null, "invoice_value": null,
    "dispatch_lat": null, "dispatch_long": null, "destination_long": null, "destination_lat": null,
    "value_add": null, "date_transit": "2026-09-01", "estimated_date_of_arrival": "2026-09-10",
    "veh_carrying_capacity": null, "pending_servey": null, "address": null, "dispatch_type": null,
    "flat_track_number": null, "destination_pin": null, "origin_pin": null, "group_id": "120",
    "destination_address": null, "dispatch_id": "JA001090926", "user_id": 195, "channel_partner_id": null,
    "consignee_id": null, "remarks": null, "dl_no": null, "dl_dob": null, "driver_aadhaar_no": null,
    "is_rc_verified": false, "is_dl_verified": false, "is_aadhaar_verified": false, "is_verification_done": false,
    "driver_id": null, "driver_email": null, "driver_mobile_no": null,
    "updated_at": "2026-09-09T13:57:17.000000Z", "created_at": "2026-09-09T13:57:17.000000Z", "id": 390
  }
}
```

**Error `422`** — duplicate serial no:
```json
{ "message": "The given data was invalid.", "errors": { "cargo_unit_serial_no": ["This cargo unit serial no has already been used on another dispatch."] } }
```

**Error `422`** — missing `group_id`:
```json
{ "message": "The given data was invalid.", "errors": { "group_id": ["The group id field is required."] } }
```

**Error `422`** — expired policy on the resolved group:
```json
{ "message": "The given data was invalid.", "errors": { "group_id": ["This customer's policy has expired; dispatch creation is blocked."] } }
```

**Error `422`** — missing `estimated_date_of_arrival`:
```json
{ "message": "The given data was invalid.", "errors": { "estimated_date_of_arrival": ["The estimated date of arrival field is required."] } }
```

**Error `422`** — EDA before `date_transit`:
```json
{ "message": "The given data was invalid.", "errors": { "estimated_date_of_arrival": ["The estimated date of arrival must be a date after or equal to date transit."] } }
```

---

## 5. Dispatch list & detail

### `GET /cargo-details/{id}`

**Success `200`** — full real example (own group, with driver + video + creator all populated):
```json
{
  "cargo_details": {
    "id": 389, "veh_reg_no": "BK97ZY1193", "cargo_unit_serial_no": "MSCU1234567", "driver_lic_no": null,
    "veh_fitness_cert": null, "veh_carrying_capacity": null, "invoice": null, "packing_list": null, "serial_no": null,
    "invoice_value": null, "dispatch_lat": null, "dispatch_long": null, "destination_long": null, "destination_lat": null,
    "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
    "value_add": null, "date_transit": "2026-09-09", "estimated_date_of_arrival": null, "pending_servey": 0,
    "address": null, "dispatch_type": null, "flat_track_number": null, "destination_pin": null, "origin_pin": null,
    "destination_address": null, "remarks": null, "dl_no": null, "dl_dob": null, "driver_aadhaar_no": null,
    "is_rc_verified": 0, "is_dl_verified": 0, "is_aadhaar_verified": 0, "is_verification_done": 0,
    "user_id": 195, "deleted_at": null, "group_id": 120, "dispatch_id": "DISP-PFPRA9GK",
    "channel_partner_id": null, "consignee_id": null, "driver_id": 197, "driver_email": null, "driver_mobile_no": null,
    "driver_videos_status": "pending",
    "video_tutorials": [
      {
        "id": 18, "title": "Load Securing 101", "description": "How to secure cargo",
        "video_url": "https://cdn.example.com/videos/load-securing.mp4",
        "status": "not_started", "watched_at": null, "selfie_url": null, "is_assisted": false, "assisted_by": null,
        "test": { "pass_percentage": 50, "attempts_count": 0, "best_score_percent": null, "passed": false, "last_attempt_at": null }
      }
    ],
    "photographs": [], "checklists": [],
    "group": {
      "id": 120, "name": "ExGroupA", "address": "84120 Crist Garden Apt. 480\nStaceystad, SC 28170", "city": "South Junechester",
      "gst": "79BEWGYWTYSHG", "policy_no": null, "policy_expiry_date": null,
      "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
      "photo": null, "sop": null, "parent_user_id": null, "additional_emails": null, "deleted_at": null, "channel_partner_id": null, "phases": []
    },
    "driver": {
      "id": 197, "group_id": null, "email": "sam.driver@example.com", "email_verified_at": "2026-09-09T13:53:19.000000Z",
      "role": "Driver", "video_status": "pending", "phone": "9876543210",
      "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
      "name": "Sam Driver", "is_admin": 0, "channel_partner_id": null, "user_status": null
    },
    "creator": {
      "id": 195, "group_id": 120, "email": "hills.adah@example.com", "email_verified_at": "2026-09-09T13:53:19.000000Z",
      "role": "Insured's Representative", "video_status": null, "phone": "3596689610",
      "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
      "name": "Jane Dispatcher", "is_admin": 0, "channel_partner_id": null, "user_status": null
    }
  }
}
```
`test` is `null` on a `video_tutorials[]` entry if that video has no test attached. `creator`/`driver` are `null` if none is set.

**Error `404`** — dispatch exists but isn't in your group (or genuinely doesn't exist):
```json
{ "message": "No query results for model [App\\Models\\CargoDetail] 389" }
```
Previously this endpoint had **no** access check at all — any authenticated user could fetch any dispatch by ID.

### `GET /cargo-details`

Same per-row shape as above, wrapped in the standard paginator (§2's shape), plus `consignee` on each row:
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 390, "veh_reg_no": "MH12AB1234", "cargo_unit_serial_no": "MSCU7654321",
      "date_transit": "2026-09-01", "estimated_date_of_arrival": "2026-09-10", "pending_servey": null,
      "group_id": 120, "dispatch_id": "JA001090926", "driver_id": null, "driver_videos_status": null,
      "video_tutorials": [], "photographs": [], "checklists": [],
      "group": { "id": 120, "name": "ExGroupA", "policy_no": null, "policy_expiry_date": null },
      "consignee": null, "driver": null,
      "creator": { "id": 195, "name": "Jane Dispatcher", "role": "Insured's Representative", "video_status": null }
    }
  ],
  "per_page": 15, "total": 2, "last_page": 1
}
```
(`group`/`creator` trimmed here for space — every field shown in §5's single-record example is present on every list row too; the full untrimmed field list is identical.)

---

## 6. Ground Surveyor role

`role == "Ground Surveyor"` gets the same unrestricted visibility as Admin — customers, dispatches, dashboard. No new endpoint, no other restrictions.

---

## 7. Drivers list + video assistance

### `GET /drivers`

| Param | Behavior |
|---|---|
| `name`, `email`, `mobile` | partial match against name / email / phone |
| `group_id` | Admin/Ground Surveyor only; must exist in `groups` |
| `video_status` | `pending` or `completed`; anything else → `422` |
| `per_page` | default 15, max 100 |

**Success `200`**:
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 197, "group_id": null, "email": "sam.driver@example.com", "email_verified_at": "2026-09-09T13:53:19.000000Z",
      "role": "Driver", "video_status": "pending", "phone": "9876543210",
      "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
      "name": "Sam Driver", "is_admin": 0, "channel_partner_id": null, "user_status": null
    }
  ],
  "first_page_url": "https://.../drivers?page=1", "from": 1, "last_page": 1, "last_page_url": "https://.../drivers?page=1",
  "next_page_url": null, "path": "https://.../drivers", "per_page": 15, "prev_page_url": null, "to": 1, "total": 1
}
```
`video_status` is a stored column (`users.video_status`), kept in sync automatically — filtering/paginating on it is always a plain indexed query. States: `null` (no video applicable to any of the driver's trips), `pending` (at least one not done), `completed` (all done).

**Error `422`** — invalid `video_status`:
```json
{ "message": "The given data was invalid.", "errors": { "video_status": ["The selected video status is invalid."] } }
```

### `GET /drivers/{driver}/pending-videos`

**Success `200`**:
```json
{
  "pending_video_tutorials": [
    {
      "id": 18, "title": "Load Securing 101", "description": "How to secure cargo",
      "video_url": "https://cdn.example.com/videos/load-securing.mp4",
      "status": "not_started", "video_watch_record_id": null
    }
  ]
}
```

**Error `404`** — driver not visible to you, or `{driver}` isn't a Driver-role user:
```json
{ "message": "Driver not found." }
```

### `POST /drivers/{driver}/assisted-videos/{videoTutorial}/selfie`

Multipart form: `selfie` (image, required), `latitude`/`longitude` (nullable numbers).

**Success `201`**:
```json
{
  "video_watch_record": {
    "driver_id": 197, "video_tutorial_id": 18, "first_required_by_cargo_id": null,
    "selfie_path": "1788962312hUjEt2anT5Z3sFzYbw4vGSNrIIWuTlAGGS4ZsNH0.png",
    "selfie_captured_at": "2026-09-09T13:58:32.000000Z", "latitude": null, "longitude": null,
    "started_at": "2026-09-09T13:58:32.000000Z", "status": "in_progress",
    "is_assisted": true, "assisted_by_user_id": 195,
    "updated_at": "2026-09-09T13:58:32.000000Z", "created_at": "2026-09-09T13:58:32.000000Z", "id": 13,
    "selfie_url": "https://ilcargocare.com/dashboard/storage/1788962312hUjEt2anT5Z3sFzYbw4vGSNrIIWuTlAGGS4ZsNH0.png"
  }
}
```

**Error `422`** — video not applicable to any of the driver's trips:
```json
{ "message": "This video is not assigned to any of this driver's trips." }
```

**Error `404`**: same shape as `pending-videos`'s 404 above (driver not visible / not a driver).

### `POST /drivers/{driver}/assisted-videos/{videoTutorial}/complete`

No body.

**Success `200`**:
```json
{
  "video_watch_record": {
    "id": 13, "driver_id": 197, "video_tutorial_id": 18,
    "first_required_by_cargo_id": null, "selfie_path": "1788962312hUjEt2anT5Z3sFzYbw4vGSNrIIWuTlAGGS4ZsNH0.png",
    "selfie_captured_at": "2026-09-09T13:58:32.000000Z", "latitude": null, "longitude": null,
    "started_at": "2026-09-09T13:58:32.000000Z", "watched_at": "2026-09-09T13:58:32.000000Z", "completed_at": null,
    "status": "watched", "is_assisted": true, "assisted_by_user_id": 195,
    "created_at": "2026-09-09T13:58:32.000000Z", "updated_at": "2026-09-09T13:58:32.000000Z",
    "selfie_url": "https://ilcargocare.com/dashboard/storage/1788962312hUjEt2anT5Z3sFzYbw4vGSNrIIWuTlAGGS4ZsNH0.png"
  }
}
```
`status` is `watched` if the video has a test (shown above), or `completed` directly if it doesn't.

### `GET /drivers/{driver}/assisted-videos/{videoTutorial}/test`

**Success `200`**:
```json
{
  "video_test": {
    "id": 9, "video_tutorial_id": 18, "pass_percentage": 50, "is_active": true, "created_by": null,
    "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
    "questions": [
      {
        "id": 11, "video_test_id": 9, "question_text": "What must you check before securing a load?", "position": 0,
        "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z",
        "options": [
          { "id": 39, "video_test_question_id": 11, "option_text": "Straps are rated for the load weight", "position": 0, "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z" },
          { "id": 40, "video_test_question_id": 11, "option_text": "The truck has fuel", "position": 1, "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z" },
          { "id": 41, "video_test_question_id": 11, "option_text": "The radio works", "position": 2, "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z" },
          { "id": 42, "video_test_question_id": 11, "option_text": "Its daytime", "position": 3, "created_at": "2026-09-09T13:53:19.000000Z", "updated_at": "2026-09-09T13:53:19.000000Z" }
        ]
      }
    ]
  }
}
```
Note: `is_correct` is never present on any option here — not `false`, the key is absent entirely. `404` if the video has no test.

### `POST /drivers/{driver}/assisted-videos/{videoTutorial}/test`

Body: `{"answers": {"<question_id>": <option_id>, ...}}` — one entry per question, using the real IDs from the GET above.

**Error `422`** — `answers` missing/empty:
```json
{ "error": { "answers": ["The answers field is required."] } }
```

**Error `422`** — right count of answers, but keyed to a question ID not in this test:
```json
{ "error": "Every question must be answered." }
```
(Note this one is a bare string under `error`, not an object — different from the validation-error shape directly above it.)

**Success `201`** — all correct:
```json
{
  "video_test_attempt": {
    "id": 10, "video_watch_record_id": 13, "video_test_id": 9,
    "score_percent": 100, "passed": true, "submitted_at": "2026-09-09T13:59:00.000000Z",
    "created_at": "2026-09-09T13:59:00.000000Z", "updated_at": "2026-09-09T13:59:01.000000Z",
    "answers": [
      { "id": 14, "video_test_attempt_id": 10, "video_test_question_id": 11, "selected_option_id": 39, "is_correct": true, "created_at": "2026-09-09T13:59:00.000000Z", "updated_at": "2026-09-09T13:59:00.000000Z" }
    ]
  }
}
```
`passed` requires `score_percent` **strictly greater than** `pass_percentage` — scoring exactly equal to the threshold fails. On fail, the watch record resets to `not_started` (selfie/timestamps cleared) — redo selfie → complete before retrying.

**Error `422`** — submitting again after already passing:
```json
{ "message": "This test has already been passed." }
```

---

## 8. Final report

`GET /cargo-details/{id}/final-report` — everything the existing on-demand report (`GET /cargo-details/{id}/report`) has, plus a shipment route map (origin→destination, straight connecting line) and any FASTag/container tracking data collected for the dispatch. Same access control as `GET /cargo-details/{id}` (§5) — `404` if not in your group — unlike the older `/report` endpoint, which currently has none.

Response is a raw PDF stream (`Content-Type: application/pdf`), not JSON — there is no JSON success shape for this endpoint. Route and FASTag sections show a plain "unavailable"/"no data" message when that data doesn't exist, rather than failing the request. The Container Tracking section is different: since `cargo_unit_serial_no` isn't always a container (see §4), that section is omitted entirely — no heading, no "no data" message — whenever no container tracking data has been collected for the dispatch, rather than implying every dispatch should have one.

This same PDF is also what gets emailed automatically, **once**, to a dispatch's group's `additional_emails`, when the trip is marked completed (`pending_servey` flips to `1`). Previously, completion triggered a bare text-only reminder email with no report attached at all, and — since it re-checked every minute with no "already sent" tracking, looking only at the single latest completed dispatch — would have either spammed the same email every minute forever or silently skipped every dispatch that wasn't the most recent one. Both are fixed: `cargo_details.final_report_sent_at` gates the send to exactly once, and every newly-completed dispatch is processed, not just the latest.

---

## 9. Error shape reference

| Shape | Seen on |
|---|---|
| `{"message": "The given data was invalid.", "errors": {"<field>": ["..."]}}` | `cargo-details` create, `dashboard` |
| `{"error": {"<field>": ["..."]}}` | `groups`, `support-tickets` (note singular `error`, not nested under `message`) |
| `{"error": "<string>"}` | The one hand-rolled "every question must be answered" check |
| `{"message": "<string>"}` | Business-rule rejections (`abort()`) and `404`s |
| `{"message": "Unauthenticated."}` | Missing/invalid token, any endpoint |

This environment runs with debug mode on, so a raw error response here also includes `exception`/`file`/`trace` keys — **those are stripped in the examples above** because production (`APP_DEBUG=false`) never sends them; only `message`/`errors`/`error` are real for the frontend to rely on.

---

## 10. Quick reference

| Method | Path |
|---|---|
| GET | `/dashboard` |
| GET | `/cargo-details/{id}/final-report` |
| GET | `/support-tickets` |
| POST | `/support-tickets` |
| GET | `/support-tickets/{id}` |
| PUT | `/support-tickets/{id}` |
| DELETE | `/support-tickets/{id}` |
| GET | `/support-tickets/pending-count` |
| POST | `/groups` |
| GET | `/groups/{id}` |
| POST | `/groups/{id}` |
| POST | `/cargo-details` |
| GET | `/cargo-details` |
| GET | `/cargo-details/{id}` |
| GET | `/drivers` |
| GET | `/drivers/{driver}/pending-videos` |
| POST | `/drivers/{driver}/assisted-videos/{videoTutorial}/selfie` |
| POST | `/drivers/{driver}/assisted-videos/{videoTutorial}/complete` |
| GET | `/drivers/{driver}/assisted-videos/{videoTutorial}/test` |
| POST | `/drivers/{driver}/assisted-videos/{videoTutorial}/test` |

Container/FASTag data is collected by a background job every 6 hours into `fastag_transactions`/`container_tracking_events` — there is no read endpoint for it yet.
