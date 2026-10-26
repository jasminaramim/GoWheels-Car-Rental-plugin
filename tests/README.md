# GoWheels test plan

Run these on a staging site before release.

1. **Concurrency** – fire two simultaneous `POST /wp-json/gowheels/v1/bookings` requests for the same vehicle and overlapping dates (e.g. `ab -n 2 -c 2` or two browser tabs). Exactly one must succeed (HTTP 201); the other returns 409 `gowheels_unavailable`.
2. **Hold expiry** – set hold to 1 minute, submit a request, wait for cron (or run `wp cron event run gowheels_expire_holds`), confirm the status becomes `expired` and the car shows as available again.
3. **Approval re-check** – create two overlapping requests after the first hold expired; approve one, then try approving the second: must be blocked.
4. **Pricing** – 3 days × 3000 + extra 500 + 20% deposit → total 9,500, deposit 1,900.
5. **Stripe sandbox** – success, failure, and the same webhook event delivered twice (payment must be credited once). Tamper the body: webhook must return 400.
6. **Authorization** – anonymous `GET /bookings/{ref}` without token → 404; `/admin/*` without capability → 401/403.
7. **Uninstall** – with "Delete all data" off, deleting the plugin must keep tables.
