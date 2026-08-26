# Video Tutorial & Driver Training — Frontend API Guide

Everything a frontend needs to build against the video-tutorial + MCQ-test feature: who can call what, request/response shapes, validation, and the status lifecycle that drives the UI.

Base URL: `https://ilcargocare.com/ilcargo-backend-new/public/api/v1` — every path below is relative to this (e.g. `GET /video-tutorials` means `GET https://ilcargocare.com/ilcargo-backend-new/public/api/v1/video-tutorials`). All endpoints require `Authorization: Bearer <token>` (Laravel Sanctum), obtained from the existing `POST /login`.

---

## 1. Roles at a glance

| Persona | How the backend identifies them | What they do here |
|---|---|---|
| **Admin** | `user.is_admin == 1` | Authors videos + tests, sees fleet-wide reports |
| **Dispatcher** | `user.role == "Insured's Representative"` | Assigns videos to a trip, reviews that trip's checklist, and — when a driver has no phone — hands the driver the dispatcher's own device for the assisted watch/test flow (§5). There's no separate "staff" role for this; it's the same dispatcher. |
| **Driver** | `user.role == "Driver"`, linked via `cargo_details.driver_id` | Watches videos, takes tests |

> ⚠️ **Enforcement gap, by design of the current backend**: only the four reporting endpoints in §6 actually check `is_admin` server-side. The video-tutorial CRUD endpoints (§2) and the dispatcher checklist (§5) are reachable by any authenticated user — the backend relies on the frontend to only expose those screens to the right role, except where a table below says otherwise. Don't build the UI assuming the API blocks the wrong persona.

---

## 2. Admin: authoring videos + tests

Resource: `/video-tutorials` (standard REST resource).

### `GET /video-tutorials`
List, newest first. Each item includes `video_test` **without** its questions (use `GET /video-tutorials/{id}` for that).

```json
{
  "video_tutorials": [
    {
      "id": 12,
      "title": "Load securing procedure",
      "description": "...",
      "video_url": "https://...",
      "is_active": true,
      "created_by": 3,
      "created_at": "...", "updated_at": "...",
      "video_test": { "id": 5, "video_tutorial_id": 12, "pass_percentage": 50, "is_active": true, "created_by": 3, "created_at": "...", "updated_at": "..." }
    }
  ]
}
```

`video_test` is `null` if the video has no test attached.

### `GET /video-tutorials/{video_tutorial}`
Same as one item above, but `video_test` is fully expanded with `questions[].options[]`, **and each option includes `is_correct`** (admin-only visibility — see §7).

### `POST /video-tutorials` — create (with optional test)

| Field | Type | Rules |
|---|---|---|
| `title` | string | required |
| `description` | string | nullable |
| `video_url` | string (URL) | required |
| `is_active` | boolean | optional, default `true` |
| `test` | object | optional — omit entirely for a video with no comprehension check |
| `test.pass_percentage` | integer 1–100 | optional, default `50` |
| `test.questions` | array, min 1 | required if `test` is present |
| `test.questions[].question_text` | string | required |
| `test.questions[].options` | array, **exactly 4** | required |
| `test.questions[].options[].option_text` | string | required |
| `test.questions[].options[].is_correct` | boolean | required — **exactly one `true` per question**, or the whole request is rejected |

```json
{
  "title": "Load securing procedure",
  "video_url": "https://cdn.example.com/videos/load-securing.mp4",
  "test": {
    "pass_percentage": 60,
    "questions": [
      {
        "question_text": "What must you check before securing a load?",
        "options": [
          { "option_text": "Straps are rated for the load weight", "is_correct": true },
          { "option_text": "The truck has fuel", "is_correct": false },
          { "option_text": "The radio works", "is_correct": false },
          { "option_text": "It's daytime", "is_correct": false }
        ]
      }
    ]
  }
}
```

Response `201`, same shape as `GET /video-tutorials/{id}` (answer keys included). Response `422` on validation failure — see §8 for the error shape, including the custom "exactly one correct option" and "exactly 4 options" checks.

### `PUT/PATCH /video-tutorials/{video_tutorial}` — update

Same field rules as create, all `sometimes`. **The `test` field has three distinct behaviors — this is the one gotcha to get right:**

| What you send | What happens |
|---|---|
| `test` key omitted entirely | Existing test (if any) is left untouched |
| `"test": null` (or `{}`, or `questions: []`) | Existing test is **deleted** |
| `"test": { "questions": [...] }` | Existing test is **wholesale replaced** — all old questions/options are dropped and recreated from what you sent. There's no partial/diff update of individual questions; always send the complete question set. |

Response `200`, same shape as create.

### `DELETE /video-tutorials/{video_tutorial}`
`204 No Content`. **Destructive**: cascades to the video's test, every driver's watch records for that video, and all historical test attempts. There's no soft-delete or archive — confirm before wiring a delete button to this.

---

## 3. Trip setup: assigning videos to a trip

Not a new endpoint — the existing trip (`CargoDetail`) create/update endpoints accept a `video_tutorial_ids` array:

- `POST /cargo-details` and `POST/PUT /cargo-details/{id}`
- Body: `"video_tutorial_ids": [12, 14]` (each ID validated against `video_tutorials` — including on update, where this validation previously existed but was never actually enforced due to a missing check; it's enforced now)
- This is what makes a video "pending" for the trip's driver, and is what the dispatcher checklist (§6) and driver's pending list (§4) key off of.
- A driver with no account yet is auto-provisioned from `driver_name` / `driver_email` / `driver_mobile_no` on the same request (existing behavior, unchanged).

**The main trip listing also carries this data now**: `GET /cargo-details` (the paginated `index()` used everywhere else in the app) includes a `video_tutorials` array on every trip in the response, in the **exact same shape** as the per-trip checklist in §6 (same `status`, `test` block, etc. — both are built from one shared mapping function, so they can't drift apart again). If your trip-list screen wants to show a quick training-status indicator per row, this is already there — no extra request needed.

---

## 4. Driver: watch + test flow

Base: `/driver/...`. Every endpoint here acts on `Auth::user()` as the driver — there's no `{driver}` param.

### Status lifecycle (drives the UI)

```
not_started ──(selfie)──► in_progress ──(complete)──► watched ──(test: fail)──► not_started  [must rewatch]
                                              │                      │
                                    (no test attached)          (test: pass)
                                              ▼                      ▼
                                          completed              completed
```

- A **failed** test attempt resets the record all the way back to `not_started` — selfie, geolocation, and watch timestamps are all cleared. The driver must go through selfie → complete again before they can retake the test. There is no "retake without rewatching."
- A video with **no test attached** goes straight from `in_progress` to `completed` on the `complete` call — nothing changes there from before this feature existed.
- Attempt history (`video_test_attempt`) is preserved across rewatch cycles even though the watch record itself resets — so a driver's full pass/fail history for a video is never lost.

### `GET /driver/videos/pending`
Every video assigned to one of the driver's trips that isn't done yet — **one row per video**, not split by action type. `status` tells the frontend what to render:

```json
{
  "video_tutorials": [
    { "id": 12, "title": "...", "description": "...", "video_url": "...", "status": "not_started", "video_watch_record_id": null },
    { "id": 14, "title": "...", "description": "...", "video_url": "...", "status": "in_progress", "video_watch_record_id": 87 },
    { "id": 16, "title": "...", "description": "...", "video_url": "...", "status": "watched", "video_watch_record_id": 88 }
  ]
}
```

| `status` | What the frontend does |
|---|---|
| `not_started` / `in_progress` | Show the video (selfie → watch → complete, §4 below) |
| `watched` | Show the test (fetch questions → submit answers, §4 below) |

A video guaranteed to have reached `watched` has a test attached — `completeWatch()` sends a test-less video straight to `completed`, which drops it off this list entirely. So `status` alone is enough to decide watch-vs-test; there's no separate flag for "does this video have a test." A video reappears here (back at `not_started`) after a failed test attempt, since failing resets the whole watch record (see the lifecycle above). Once a video is fully done (`completed`), it disappears from this list.

### `POST /driver/videos/{videoTutorial}/selfie`
Starts (or restarts, after a failed test) the watch record.

| Field | Type | Rules |
|---|---|---|
| `selfie` | file (image) | required, multipart/form-data |
| `latitude` | number | nullable |
| `longitude` | number | nullable |

Response `201`:
```json
{
  "video_watch_record": {
    "id": 88, "driver_id": 40, "video_tutorial_id": 12,
    "status": "in_progress",
    "selfie_path": "driver_selfies/...", "selfie_url": "https://.../driver_selfies/...",
    "selfie_captured_at": "...", "latitude": null, "longitude": null,
    "started_at": "...", "watched_at": null, "completed_at": null,
    "is_assisted": false, "assisted_by_user_id": null,
    "first_required_by_cargo_id": 5,
    "created_at": "...", "updated_at": "..."
  }
}
```
`403 {"error": "This video is not assigned to you."}` if the video isn't on any of the driver's trips.

### `POST /driver/videos/{videoTutorial}/complete`
No body. Response `200`, same `video_watch_record` shape. `status` becomes `"watched"` if the video has a test, `"completed"` if it doesn't. `404` if there's no `in_progress` record to complete (selfie wasn't called first, or it was already completed).

### `GET /driver/videos/{videoTutorial}/test`
Fetch the questions to render. **Options never include `is_correct`** — see §7.

```json
{
  "video_test": {
    "id": 5, "video_tutorial_id": 12, "pass_percentage": 60,
    "questions": [
      {
        "id": 20, "question_text": "What must you check before securing a load?",
        "options": [
          { "id": 80, "option_text": "Straps are rated for the load weight" },
          { "id": 81, "option_text": "The truck has fuel" },
          { "id": 82, "option_text": "The radio works" },
          { "id": 83, "option_text": "It's daytime" }
        ]
      }
    ]
  }
}
```
`403` if the video isn't assigned to the driver. `404` if the video has no test.

### `POST /driver/videos/{videoTutorial}/test` — submit answers

Body is an **object keyed by question ID**, not an array:

```json
{ "answers": { "20": 80, "21": 85 } }
```

| Rule | Detail |
|---|---|
| `answers` | required, must have **exactly** as many entries as the test has questions |
| `answers.*` | each value must be an existing `video_test_question_options.id` |
| Coverage | every question ID in the test must appear as a key, or `422 {"error": "Every question must be answered."}` |

Preconditions (both return `422`, `{"message": "..."}` — see §8):
- Record isn't at `status == "watched"` → `"Watch the video before taking its test."`
- Record is already `"completed"` → `"This test has already been passed."`

Response `201`:
```json
{
  "video_test_attempt": {
    "id": 200, "video_watch_record_id": 88, "video_test_id": 5,
    "score_percent": "50.00", "passed": false, "submitted_at": "...",
    "created_at": "...", "updated_at": "...",
    "answers": [
      { "id": 300, "video_test_attempt_id": 200, "video_test_question_id": 20, "selected_option_id": 80, "is_correct": true, "created_at": "...", "updated_at": "..." },
      { "id": 301, "video_test_attempt_id": 200, "video_test_question_id": 21, "selected_option_id": 86, "is_correct": false, "created_at": "...", "updated_at": "..." }
    ]
  }
}
```

- `passed` is `true` only when `score_percent` is **strictly greater than** `pass_percentage` (a tie does not pass — 50% on a 50%-threshold test fails).
- Each answer's `is_correct` tells you whether *that submission* was right — the correct option itself is still never returned, even here.
- On pass: the watch record flips to `completed`; on fail: it resets to `not_started` (see the lifecycle diagram) and this same endpoint will 422 with `"Watch the video before taking its test."` until the driver redoes selfie → complete.

---

## 5. Assisted flow (driver has no personal phone)

There is no separate "staff" role. This is the **dispatcher**, using their own device/login to hand the flow to the driver — the driver still performs every action themselves (selfie, watching, answering questions); the dispatcher is not answering on the driver's behalf (see §7 for why this distinction matters). Identical shapes to §4, but routes are scoped to a specific trip instead of relying on `Auth::user()`:

```
POST /cargo-details/{cargoDetail}/assisted-videos/{videoTutorial}/selfie
POST /cargo-details/{cargoDetail}/assisted-videos/{videoTutorial}/complete
GET  /cargo-details/{cargoDetail}/assisted-videos/{videoTutorial}/test
POST /cargo-details/{cargoDetail}/assisted-videos/{videoTutorial}/test
```

Request/response bodies are exactly as in §4. The authenticated user for these calls is the **dispatcher** (their session holds the token), not the driver — the driver's identity comes from `cargoDetail.driver_id`.

Extra preconditions, both `422 {"message": "..."}`:
- Trip has no driver assigned → `"This trip has no driver assigned."`
- Video isn't assigned to this trip → `"This video is not assigned to this trip."`

The resulting `video_watch_record` has `is_assisted: true` and `assisted_by_user_id` set to the dispatcher.

> Note: the backend doesn't currently check that the caller is actually a dispatcher (`role == "Insured's Representative"`) before allowing these actions — any authenticated user's token works. Same unenforced-role pattern as §2; flag if you want that tightened.

---

## 6. Dispatcher: per-trip checklist

### `GET /cargo-details/{cargoDetail}/video-tutorials`

Authorized the same way trips are listed elsewhere in the app: admins see any trip, others need to share the trip's `group_id` (see §1). `403 {"message": "You do not have access to this trip."}` otherwise.

```json
{
  "driver_videos_status": "pending",
  "video_tutorials": [
    {
      "id": 12, "title": "...", "description": "...", "video_url": "...",
      "status": "watched",
      "watched_at": "...",
      "selfie_url": "https://...",
      "is_assisted": false,
      "assisted_by": null,
      "test": {
        "pass_percentage": 60,
        "attempts_count": 2,
        "best_score_percent": "100.00",
        "passed": true,
        "last_attempt_at": "..."
      }
    }
  ]
}
```

- `test` is `null` for a video with no test attached.
- `driver_videos_status` (top-level) is the same **advisory-only** rollup as before — `null` (no videos assigned), `pending`, or `completed`. It does not block dispatch or anything else; there's no hard gate tied to it yet.
- `attempts_count` / `best_score_percent` reflect the video's **entire** attempt history, even across rewatch cycles caused by earlier failures.

---

## 7. Answer-key exposure — the one security rule to preserve

`is_correct` on an option is **hidden by default** at the model level. It only appears when an admin endpoint explicitly asks for it (video-tutorial CRUD in §2). Every driver/assisted/dispatcher-facing endpoint in this doc omits the key entirely — it's not `null`, the key is simply absent from the JSON. If you ever see a new endpoint returning test data to a driver, check that it doesn't leak this field.

---

## 8. Error shapes — inconsistent across endpoints, handle both

This predates the test feature and wasn't changed as part of it, but it affects every endpoint above, so: **check for both `error` and `message` in a failed response.**

| Shape | When | Example |
|---|---|---|
| `{"error": {"<field>": ["<msg>"]}}` | Laravel validator failures (`title` required, bad `video_url`, wrong option count, etc.) | `{"error": {"title": ["The title field is required."]}}` |
| `{"error": "<string>"}` | A handful of hand-rolled checks (e.g. `DriverVideoController::selfie`'s applicability check) | `{"error": "This video is not assigned to you."}` |
| `{"message": "<string>"}` | Everything using `abort()`/`abort_if()`/`abort_unless()` — most of the business-rule guards in this feature (test-not-watched, already-passed, trip-not-visible, etc.) | `{"message": "Watch the video before taking its test."}` |
| `{"message": "No query results for model [App\\Models\\X]."}` | Route-model-binding or `firstOrFail()` miss (bad ID, or no test/record exists yet) | 404 |

---

## 9. Quick reference

| Method | Path | Who | Purpose |
|---|---|---|---|
| GET | `/video-tutorials` | Admin (unenforced) | List videos |
| POST | `/video-tutorials` | Admin (unenforced) | Create video (+ optional test) |
| GET | `/video-tutorials/{video_tutorial}` | Admin (unenforced) | Full detail incl. answer keys |
| PUT/PATCH | `/video-tutorials/{video_tutorial}` | Admin (unenforced) | Update video/test |
| DELETE | `/video-tutorials/{video_tutorial}` | Admin (unenforced) | Delete (cascades everything) |
| GET | `/driver/videos/pending` | Driver | One list, `status` per video says watch-or-test |
| POST | `/driver/videos/{videoTutorial}/selfie` | Driver | Start/restart watching |
| POST | `/driver/videos/{videoTutorial}/complete` | Driver | Finish watching |
| GET | `/driver/videos/{videoTutorial}/test` | Driver | Fetch questions |
| POST | `/driver/videos/{videoTutorial}/test` | Driver | Submit answers |
| POST/GET | `/cargo-details/{cargoDetail}/assisted-videos/{videoTutorial}/...` | Dispatcher (hands device to driver) | Same 4 actions, trip-scoped |
| GET | `/cargo-details/{cargoDetail}/video-tutorials` | Dispatcher/Admin | Per-trip checklist |
| GET | `/video-watch-records` | **Admin only (enforced)** | Global watch audit log |
| GET | `/drivers/{user}/video-watch-records` | **Admin only (enforced)** | One driver's full history |
| GET | `/video-tutorials/{videoTutorial}/watch-records` | **Admin only (enforced)** | Per-video watch adoption |
| GET | `/video-tutorials/{videoTutorial}/test-attempts` | **Admin only (enforced)** | Per-video pass/fail report |
