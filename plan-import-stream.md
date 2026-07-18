# Plan: Real-time Import Progress via Server-Sent Events

## Why SSE over WebSockets
SSE is unidirectional (server → client), works over plain HTTP, and PHP supports it natively with `flush()`. No extra server, no dependencies. Perfect fit for a one-way progress stream.

---

## Backend changes

**`Kindle3Importer::Process()`**
- Accept an optional callable `$onProgress` parameter
- Call it after each highlight is processed: `$onProgress($imported, $skipped, $total)`

**New `api/src/features/File/FileStreamApiControl.php`**
- Sets SSE headers (`Content-Type: text/event-stream`, `Cache-Control: no-cache`, `X-Accel-Buffering: no`)
- Sets `max_execution_time` to 0
- Calls `ImportControl::ProcessFile($file, $onProgress)` where `$onProgress` flushes an SSE event:
  ```
  event: progress
  data: {"processed":12,"total":340,"imported":11,"skipped":1}
  ```
- On completion, sends a final event:
  ```
  event: done
  data: {"highlight_count":280,"skipped":60,"execution_time":1420}
  ```
- On error, sends:
  ```
  event: error
  data: {"message":"..."}
  ```

**`DunedinApi.php`**
- Add `GET /files/:id/import/stream` → `FileStreamApiControl::Stream()`
- This endpoint **both triggers and streams** the import — no separate POST needed for the stream flow
- Keep `POST /files/:id/import` as a silent (non-streaming) fallback for API clients

---

## Frontend changes

**`FileModal.vue`**
- Replace the `POST` fetch in `runImport()` with an `EventSource` connection to `GET /files/:id/import/stream` (passing the auth token as a query param, since `EventSource` doesn't support custom headers)
- On `progress` event: update a live counter display — highlights processed, skipped, out of total
- On `done` event: close the `EventSource`, update `selectedImport`, emit `imported`
- On `error` event: close the `EventSource`, show the error message
- Show a progress bar or live count during streaming: `"Processing… 42 / 340 highlights"`

**`AuthControl` (PHP)**
- Accept token from `?token=` query param as a fallback to the `Authorization` header, since `EventSource` cannot set headers

---

## Key decisions to revisit
- Whether to keep `POST /files/:id/import` working in parallel or deprecate it in favour of the stream
- Whether the total highlight count is known upfront (it is — we parse the full file first, then loop) or estimated
- Whether to buffer and send progress every N highlights instead of every single one (avoids flooding for large files)
