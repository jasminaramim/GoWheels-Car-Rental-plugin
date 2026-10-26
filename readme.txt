=== GoWheels Car Rental ===
Contributors: jasmin
Tags: car rental, booking, fleet, deposit, elementor
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Car rental management for a single company: fleet, availability search, admin-approved bookings, deposits, cash and online payments.

== Description ==

* Vehicles (custom post type) with gallery, specs, pricing, maintenance blocks
* Locations, buffer time, minimum rental, late-return grace
* Booking requests with temporary hold, admin approve/reject/cancel, calendar
* Double-booking protection (per-vehicle lock + transaction + re-check on approval)
* Server-side pricing, tax, discount, configurable deposit, balance tracking
* Cash/manual payment recording, refunds, Stripe Checkout with verified webhooks
* Shortcodes and Elementor widgets
* REST API under /wp-json/gowheels/v1/

== Shortcodes ==

* [gowheels_search]
* [gowheels_cars limit="12" columns="3"]
* [gowheels_car id="123"]
* [gowheels_booking]
* [gowheels_booking_status]
* [gowheels_my_bookings]

== Installation ==

1. Upload the plugin and activate it. Pages are created automatically.
2. GoWheels → Locations: add pickup/return locations.
3. GoWheels → Vehicles: add cars with price and photos.
4. GoWheels → Settings: currency, deposit, hold time, payment gateway.

== Changelog ==

= 1.0.0 =
* Initial release.
