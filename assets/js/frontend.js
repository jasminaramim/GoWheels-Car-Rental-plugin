(function () {
	'use strict';
	var cfg = window.gowheelsData || {};
	var t = cfg.i18n || {};

	function api(path, opts) {
		opts = opts || {};
		var headers = { 'Content-Type': 'application/json' };
		if (cfg.nonce) { headers['X-WP-Nonce'] = cfg.nonce; }
		return fetch(cfg.rest + path, {
			method: opts.method || 'GET',
			headers: headers,
			body: opts.body ? JSON.stringify(opts.body) : undefined,
			credentials: 'same-origin'
		}).then(function (r) {
			return r.json().then(function (d) {
				if (!r.ok) { throw new Error(d && d.message ? d.message : t.error); }
				return d;
			});
		});
	}

	function money(n) {
		return (cfg.symbol || '') + Number(n).toLocaleString(undefined, {
			minimumFractionDigits: cfg.decimals || 0,
			maximumFractionDigits: cfg.decimals || 0
		});
	}

	function showError(box, msg) {
		if (!box) { return; }
		box.textContent = msg || '';
		box.hidden = !msg;
		if (msg && box.scrollIntoView) { box.scrollIntoView({ block: 'center', behavior: 'smooth' }); }
	}

	/* ---------------- Booking form ---------------- */
	function initBooking(form) {
		var errBox = form.querySelector('.gw-form-error');
		var quoteBox = form.querySelector('.gw-quote');
		var submit = form.querySelector('button[type="submit"]');
		var timer = null;
		var seq = 0;

		function val(name) { return form.elements[name] ? form.elements[name].value : ''; }
		function extras() {
			return Array.prototype.map.call(form.querySelectorAll('input[name="extras[]"]:checked'), function (i) { return i.value; });
		}

		function renderQuote(q) {
			quoteBox.textContent = '';
			var table = document.createElement('table');
			table.className = 'gw-table gw-kv';
			q.lines.forEach(function (l) {
				var tr = table.insertRow();
				tr.insertCell().textContent = l.label;
				tr.insertCell().textContent = money(l.amount);
			});
			var tr = table.insertRow();
			tr.className = 'gw-total';
			tr.insertCell().textContent = t.total;
			tr.insertCell().textContent = money(q.grand_total);
			tr = table.insertRow();
			tr.insertCell().textContent = t.deposit;
			tr.insertCell().textContent = money(q.deposit_due);
			quoteBox.appendChild(table);
			if (q.available === false) {
				var p = document.createElement('p');
				p.className = 'gw-notice gw-error';
				p.textContent = t.unavail;
				quoteBox.appendChild(p);
			}
		}

		function refreshQuote() {
			if (!val('vehicle_id') || !val('pickup') || !val('return')) { quoteBox.textContent = ''; return; }
			var my = ++seq;
			var p = new URLSearchParams();
			p.set('vehicle_id', val('vehicle_id'));
			p.set('pickup', val('pickup'));
			p.set('return', val('return'));
			extras().forEach(function (e) { p.append('extras[]', e); });
			api('quote?' + p.toString()).then(function (q) {
				if (my !== seq) { return; }
				showError(errBox, '');
				renderQuote(q);
			}).catch(function (e) {
				if (my !== seq) { return; }
				quoteBox.textContent = '';
				showError(errBox, e.message);
			});
		}

		form.addEventListener('change', function () {
			clearTimeout(timer);
			timer = setTimeout(refreshQuote, 250);
		});
		refreshQuote();

		form.addEventListener('submit', function (ev) {
			ev.preventDefault();
			showError(errBox, '');
			if (!form.checkValidity()) { form.reportValidity(); return; }
			var pref = form.querySelector('input[name="payment_preference"]:checked');
			var body = {
				vehicle_id: val('vehicle_id'),
				pickup: val('pickup'),
				return: val('return'),
				pickup_location: val('pickup_location'),
				return_location: val('return_location'),
				extras: extras(),
				name: val('name'),
				email: val('email'),
				phone: val('phone'),
				notes: val('notes'),
				payment_preference: pref ? pref.value : 'cash',
				website: val('website')
			};
			submit.disabled = true;
			submit.textContent = t.sending;
			api('bookings', { method: 'POST', body: body }).then(function (res) {
				if (res.status_url) { window.location.href = res.status_url; }
			}).catch(function (e) {
				showError(errBox, e.message);
				submit.disabled = false;
				submit.textContent = t.submit;
			});
		});
	}

	/* ---------------- Status page ---------------- */
	function initStatus(box) {
		var ref = box.getAttribute('data-ref');
		var token = box.getAttribute('data-token');
		var errBox = box.querySelector('.gw-form-error');
		var base = 'bookings/' + encodeURIComponent(ref);
		var q = '?token=' + encodeURIComponent(token);

		box.addEventListener('click', function (ev) {
			var btn = ev.target.closest('button');
			if (!btn) { return; }
			showError(errBox, '');
			if (btn.hasAttribute('data-gw-cancel')) {
				if (!window.confirm(t.confirm)) { return; }
				btn.disabled = true;
				api(base + '/cancel' + q, { method: 'POST' }).then(function () {
					window.location.reload();
				}).catch(function (e) { btn.disabled = false; showError(errBox, e.message); });
			} else if (btn.hasAttribute('data-gw-pay')) {
				btn.disabled = true;
				api(base + '/payment' + q, { method: 'POST', body: { type: btn.getAttribute('data-gw-pay') } }).then(function (res) {
					if (res.url) { btn.textContent = t.redirect; window.location.href = res.url; }
				}).catch(function (e) { btn.disabled = false; showError(errBox, e.message); });
			}
		});
	}

	/* ---------------- Interactive Tabs (Fleet & Vehicle Details) ---------------- */
	function initTabs(container) {
		var nav = container.querySelector('.gw-tab-nav');
		if (!nav) { return; }
		var btns = container.querySelectorAll('.gw-tab-btn');
		var panels = container.querySelectorAll('.gw-tab-panel');

		nav.addEventListener('click', function (e) {
			var btn = e.target.closest('.gw-tab-btn');
			if (!btn) { return; }
			var targetTab = btn.getAttribute('data-gw-tab');
			if (!targetTab) { return; }

			Array.prototype.forEach.call(btns, function (b) {
				var active = (b === btn);
				b.classList.toggle('is-active', active);
				b.setAttribute('aria-selected', active ? 'true' : 'false');
			});

			Array.prototype.forEach.call(panels, function (p) {
				var matches = (p.id === 'gw-tab-panel-' + targetTab || p.id === 'gw-pane-' + targetTab);
				p.classList.toggle('is-active', matches);
				if (matches) {
					p.removeAttribute('hidden');
				} else {
					p.setAttribute('hidden', '');
				}
			});
		});
	}

	/* ---------------- Gallery Thumbnails Preview ---------------- */
	function initGallery(wrap) {
		var mainImg = wrap.querySelector('#gw-main-img, .gw-main-img, #gw-tabs-main-img');
		var thumbs = wrap.querySelectorAll('.gw-thumb-btn');
		if (!mainImg || !thumbs.length) { return; }

		mainImg.style.transition = 'opacity 0.2s ease';

		Array.prototype.forEach.call(thumbs, function (btn) {
			btn.addEventListener('click', function () {
				var full = btn.getAttribute('data-gw-full');
				if (full) {
					mainImg.style.opacity = '0.3';
					mainImg.src = full;
					setTimeout(function () { mainImg.style.opacity = '1'; }, 120);
					Array.prototype.forEach.call(thumbs, function (b) { b.classList.remove('is-active'); });
					btn.classList.add('is-active');
				}
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		Array.prototype.forEach.call(document.querySelectorAll('.gw-booking-form'), initBooking);
		Array.prototype.forEach.call(document.querySelectorAll('.gw-status'), initStatus);
		Array.prototype.forEach.call(document.querySelectorAll('.gw-tabs-container, .gw-details-tabs'), initTabs);
		Array.prototype.forEach.call(document.querySelectorAll('.gw-gallery-showcase, .gw-tabs-hero-card'), initGallery);
	});
})();
