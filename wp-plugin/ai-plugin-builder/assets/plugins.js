/* global window, document, fetch */
/**
 * My Plugins screen: list, add/import, edit fields, delete, view versions, and Fix / Change / Refactor /
 * Update edits through a short chat. All data goes through the aiwpb/v1/registry REST routes.
 */
( function () {
	'use strict';
	var cfg = window.AIWPB_PLUGINS || {};

	var STATUSES = [
		[ 'draft', 'Draft' ],
		[ 'in-review', 'In review' ],
		[ 'delivered', 'Delivered' ],
		[ 'live', 'Live' ],
		[ 'archived', 'Archived' ],
	];
	var KINDS = {
		fix: { label: 'Fix', what: 'a bug fix', placeholder: 'What goes wrong, and what should happen instead?' },
		change: { label: 'Change', what: 'a change or new feature', placeholder: 'What should be added or behave differently?' },
		refactor: { label: 'Refactor', what: 'a refactor (no behaviour change)', placeholder: 'What should be cleaned up? Behaviour stays the same.' },
		update: { label: 'Update', what: 'an update (the AI decides if it is a fix or a change)', placeholder: 'Describe the update.' },
	};

	var plugins = [];
	var current = null; // full record of the plugin open in the detail panel
	var edit = null; // { kind, messages, jobId, timer, errors }

	// ---------- helpers ----------
	function el( id ) {
		return document.getElementById( id );
	}
	function esc( s ) {
		var d = document.createElement( 'div' );
		d.textContent = String( s == null ? '' : s );
		return d.innerHTML;
	}
	function statusLabel( s ) {
		for ( var i = 0; i < STATUSES.length; i++ ) {
			if ( STATUSES[ i ][ 0 ] === s ) {
				return STATUSES[ i ][ 1 ];
			}
		}
		return s || '';
	}
	function date( iso ) {
		if ( ! iso ) {
			return '';
		}
		var d = new Date( iso );
		return isNaN( d.getTime() ) ? '' : d.toLocaleString();
	}
	/** Only http(s) links are rendered (escaping alone does not stop a javascript: URL). */
	function safeUrl( u ) {
		return /^https?:\/\//i.test( String( u || '' ) );
	}
	function money( n ) {
		return n ? '$' + Number( n ).toFixed( 2 ) : '$0';
	}
	function api( path, opts ) {
		return fetch( cfg.root + path, Object.assign( {
			headers: { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' },
			credentials: 'same-origin',
		}, opts || {} ) ).then( function ( res ) {
			return res.json().catch( function () {
				return {};
			} ).then( function ( data ) {
				if ( ! res.ok ) {
					throw new Error( data.message || data.error || ( 'HTTP ' + res.status ) );
				}
				return data;
			} );
		} );
	}
	function notice( text, type ) {
		var box = el( 'aiwpb-notice' );
		box.innerHTML = text ? '<div class="notice notice-' + ( type || 'success' ) + '"><p>' + esc( text ) + '</p></div>' : '';
	}
	function spinner( on ) {
		el( 'aiwpb-list-spinner' ).classList.toggle( 'is-active', on );
	}
	function statusOptions( selected, withAll ) {
		var html = withAll ? '<option value="">All statuses</option>' : '';
		STATUSES.forEach( function ( s ) {
			html += '<option value="' + s[ 0 ] + '"' + ( s[ 0 ] === selected ? ' selected' : '' ) + '>' + s[ 1 ] + '</option>';
		} );
		return html;
	}
	function downloadUrl( slug, versionId ) {
		return cfg.adminPost + '?action=aiwpb_download_version&slug=' + encodeURIComponent( slug ) +
			'&version=' + encodeURIComponent( versionId ) + '&_wpnonce=' + encodeURIComponent( cfg.downloadNonce );
	}
	function readFileB64( input ) {
		return new Promise( function ( resolve, reject ) {
			var f = input.files[ 0 ];
			if ( ! f ) {
				reject( new Error( 'Choose a .zip to upload.' ) );
				return;
			}
			var r = new window.FileReader();
			r.onload = function () {
				var s = String( r.result );
				resolve( s.substring( s.indexOf( ',' ) + 1 ) );
			};
			r.onerror = function () {
				reject( new Error( 'Could not read the file.' ) );
			};
			r.readAsDataURL( f );
		} );
	}

	// ---------- list ----------
	function load() {
		spinner( true );
		return api( '/registry', { method: 'GET' } ).then( function ( d ) {
			plugins = d.plugins || [];
			renderTable();
		} ).catch( function ( e ) {
			el( 'aiwpb-table' ).innerHTML = '<p>' + esc( 'Could not load the list: ' + e.message ) + '</p>';
		} ).then( function () {
			spinner( false );
		} );
	}

	function renderTable() {
		var q = el( 'aiwpb-search' ).value.trim().toLowerCase();
		var st = el( 'aiwpb-status-filter' ).value;
		var rows = plugins.filter( function ( p ) {
			if ( st && p.status !== st ) {
				return false;
			}
			if ( ! q ) {
				return true;
			}
			return [ p.name, p.slug, p.client, p.notes, p.liveUrl ].join( ' ' ).toLowerCase().indexOf( q ) !== -1;
		} );
		if ( ! plugins.length ) {
			el( 'aiwpb-table' ).innerHTML = '<p>No plugins yet. Build one on the AI Plugin Builder screen, or click <strong>Add plugin</strong> to import one.</p>';
			return;
		}
		var html = '<table class="widefat striped aiwpb-list"><thead><tr>' +
			'<th>Plugin</th><th>Client</th><th>Status</th><th>Version</th><th>Last change</th><th>AI</th>' +
			'<th>Total cost</th><th>Audit</th><th>On this site</th><th>Publish</th><th>Actions</th></tr></thead><tbody>';
		rows.forEach( function ( p ) {
			var c = p.current || {};
			var installed = p.installedVersion ? 'v' + esc( p.installedVersion ) + ( p.installedVersion !== c.version ? ' <span class="aiwpb-dim">(differs)</span>' : '' ) : '—';
			var audit = null === c.auditFindings || undefined === c.auditFindings ? '<span class="aiwpb-dim">n/a</span>' : esc( c.auditFindings ) + ' finding' + ( 1 === c.auditFindings ? '' : 's' );
			html += '<tr>' +
				'<td><strong><a href="#" class="aiwpb-view" data-slug="' + esc( p.slug ) + '">' + esc( p.name ) + '</a></strong><br><code>' + esc( p.slug ) + '</code>' +
				( safeUrl( p.liveUrl ) ? '<br><a href="' + esc( p.liveUrl ) + '" target="_blank" rel="noopener noreferrer">' + esc( p.liveUrl ) + '</a>' : '' ) + '</td>' +
				'<td>' + esc( p.client || '—' ) + '</td>' +
				'<td><span class="aiwpb-status aiwpb-status--' + esc( p.status ) + '">' + esc( statusLabel( p.status ) ) + '</span></td>' +
				'<td>v' + esc( c.version || '?' ) + '<br><span class="aiwpb-dim">' + esc( p.versionsCount ) + ' version' + ( 1 === p.versionsCount ? '' : 's' ) + '</span></td>' +
				'<td>' + esc( c.kind || '' ) + '<br><span class="aiwpb-dim">' + esc( date( c.createdAt ) ) + '</span></td>' +
				'<td>' + esc( c.ai || '—' ) + '</td>' +
				'<td>' + esc( money( p.totalCostUsd ) ) + '</td>' +
				'<td>' + audit + '</td>' +
				'<td>' + installed + '</td>' +
				'<td>' + publishCell( p ) + '</td>' +
				'<td class="aiwpb-actions">' +
				'<button type="button" class="button button-small aiwpb-view" data-slug="' + esc( p.slug ) + '">View</button> ' +
				'<button type="button" class="button button-small aiwpb-edit-fields" data-slug="' + esc( p.slug ) + '">Edit</button> ' +
				'<button type="button" class="button button-small button-link-delete aiwpb-delete" data-slug="' + esc( p.slug ) + '">Delete</button>' +
				'</td></tr>';
		} );
		html += '</tbody></table>';
		if ( ! rows.length ) {
			html = '<p>No plugins match.</p>';
		}
		el( 'aiwpb-table' ).innerHTML = html;
	}

	function publishCell( p ) {
		if ( p.github && safeUrl( p.github.url ) ) {
			return '<a href="' + esc( p.github.url ) + '" target="_blank" rel="noopener noreferrer">GitHub</a> ' +
				'<span class="aiwpb-dim">' + esc( p.github.lastTag ) + ( p.github.private ? ' · private' : ' · public' ) + '</span>';
		}
		if ( p.doNotPublish ) {
			return '<span class="aiwpb-lock" title="Marked do-not-publish">Do not publish</span>';
		}
		return '<span class="aiwpb-dim">not published</span>';
	}

	// ---------- add / import ----------
	function renderAdd() {
		var opts = '<option value="">— choose a plugin —</option>';
		( cfg.installed || [] ).forEach( function ( p ) {
			opts += '<option value="' + esc( p.slug ) + '">' + esc( p.name + ' (' + p.version + ')' ) + '</option>';
		} );
		el( 'aiwpb-add' ).innerHTML =
			'<h2>Add a plugin to My Plugins</h2>' +
			'<p><label><input type="radio" name="aiwpb-add-src" value="installed" checked> Installed on this site: </label>' +
			'<select id="aiwpb-add-slug">' + opts + '</select></p>' +
			'<p><label><input type="radio" name="aiwpb-add-src" value="upload"> Upload a .zip: </label>' +
			'<input type="file" id="aiwpb-add-zip" accept=".zip,application/zip"></p>' +
			fieldsForm( { status: 'draft' }, 'add' ) +
			'<p><button type="button" class="button button-primary" id="aiwpb-add-go">Add plugin</button> ' +
			'<button type="button" class="button" id="aiwpb-add-cancel">Cancel</button> <span class="spinner" id="aiwpb-add-spinner"></span></p>';
		el( 'aiwpb-add-go' ).addEventListener( 'click', doAdd );
		el( 'aiwpb-add-cancel' ).addEventListener( 'click', function () {
			el( 'aiwpb-add' ).style.display = 'none';
		} );
	}
	function fieldsForm( p, prefix ) {
		return '<table class="form-table" role="presentation"><tbody>' +
			( 'add' === prefix ? '' : '<tr><th><label for="' + prefix + '-name">Name</label></th><td><input type="text" class="regular-text" id="' + prefix + '-name" value="' + esc( p.name ) + '"></td></tr>' ) +
			'<tr><th><label for="' + prefix + '-client">Client</label></th><td><input type="text" class="regular-text" id="' + prefix + '-client" value="' + esc( p.client ) + '"></td></tr>' +
			'<tr><th><label for="' + prefix + '-status">Status</label></th><td><select id="' + prefix + '-status">' + statusOptions( p.status, false ) + '</select></td></tr>' +
			'<tr><th><label for="' + prefix + '-live">Live site URL</label></th><td><input type="url" class="regular-text" id="' + prefix + '-live" placeholder="https://…" value="' + esc( p.liveUrl ) + '"></td></tr>' +
			'<tr><th><label for="' + prefix + '-notes">Notes</label></th><td><textarea class="large-text" rows="4" id="' + prefix + '-notes">' + esc( p.notes ) + '</textarea></td></tr>' +
			'<tr><th>Publishing</th><td><label><input type="checkbox" id="' + prefix + '-dnp"' + ( p.doNotPublish ? ' checked' : '' ) + '> Do not publish (e.g. client or personal data)</label></td></tr>' +
			'</tbody></table>';
	}
	function readFields( prefix ) {
		var out = {
			client: el( prefix + '-client' ).value,
			status: el( prefix + '-status' ).value,
			liveUrl: el( prefix + '-live' ).value,
			notes: el( prefix + '-notes' ).value,
			doNotPublish: el( prefix + '-dnp' ).checked,
		};
		if ( el( prefix + '-name' ) ) {
			out.name = el( prefix + '-name' ).value;
		}
		return out;
	}
	function doAdd() {
		var src = ( document.querySelector( 'input[name="aiwpb-add-src"]:checked' ) || {} ).value || 'installed';
		var body = readFields( 'add' );
		body.source = src;
		var prep;
		if ( 'installed' === src ) {
			body.slug = el( 'aiwpb-add-slug' ).value;
			if ( ! body.slug ) {
				notice( 'Choose an installed plugin to add.', 'error' );
				return;
			}
			prep = Promise.resolve();
		} else {
			prep = readFileB64( el( 'aiwpb-add-zip' ) ).then( function ( b64 ) {
				body.zipB64 = b64;
			} );
		}
		el( 'aiwpb-add-spinner' ).classList.add( 'is-active' );
		prep.then( function () {
			return api( '/registry', { method: 'POST', body: JSON.stringify( body ) } );
		} ).then( function ( d ) {
			notice( 'Added ' + ( d.plugin ? d.plugin.name : 'the plugin' ) + ' to My Plugins.' );
			el( 'aiwpb-add' ).style.display = 'none';
			return load();
		} ).catch( function ( e ) {
			notice( e.message, 'error' );
		} ).then( function () {
			el( 'aiwpb-add-spinner' ).classList.remove( 'is-active' );
		} );
	}

	// ---------- delete ----------
	function doDelete( slug ) {
		var p = plugins.filter( function ( x ) {
			return x.slug === slug;
		} )[ 0 ];
		var name = p ? p.name : slug;
		if ( ! window.confirm( 'Remove "' + name + '" and all its stored versions from My Plugins?\n\nThis does not uninstall it from this site. It cannot be undone.' ) ) {
			return;
		}
		api( '/registry/' + encodeURIComponent( slug ), { method: 'DELETE' } ).then( function () {
			notice( 'Removed ' + name + ' from My Plugins.' );
			if ( current && current.slug === slug ) {
				closeDetail();
			}
			return load();
		} ).catch( function ( e ) {
			notice( e.message, 'error' );
		} );
	}

	// ---------- detail ----------
	function openDetail( slug, focus ) {
		return api( '/registry/' + encodeURIComponent( slug ), { method: 'GET' } ).then( function ( d ) {
			current = d.plugin;
			renderDetail( focus );
			el( 'aiwpb-detail' ).scrollIntoView( { behavior: 'smooth', block: 'start' } );
		} ).catch( function ( e ) {
			notice( e.message, 'error' );
		} );
	}
	function closeDetail() {
		stopPolling();
		current = null;
		edit = null;
		el( 'aiwpb-detail' ).style.display = 'none';
		el( 'aiwpb-detail' ).innerHTML = '';
	}
	function renderDetail( focus ) {
		var p = current;
		var cur = p.versions.filter( function ( v ) {
			return v.id === p.currentVersionId;
		} )[ 0 ] || p.versions[ p.versions.length - 1 ] || {};
		var html = '<div class="aiwpb-detail-head"><h2>' + esc( p.name ) + ' <span class="aiwpb-dim">v' + esc( cur.version ) + '</span></h2>' +
			'<button type="button" class="button-link" id="aiwpb-close">Close</button></div>' +
			'<p><code>' + esc( p.slug ) + '</code> · <span class="aiwpb-status aiwpb-status--' + esc( p.status ) + '">' + esc( statusLabel( p.status ) ) + '</span>' +
			( p.client ? ' · Client: ' + esc( p.client ) : '' ) +
			( p.installedVersion ? ' · On this site: v' + esc( p.installedVersion ) : ' · Not installed on this site' ) +
			( p.doNotPublish ? ' · <span class="aiwpb-lock">Do not publish</span>' : '' ) + '</p>' +
			( cur.provides ? '<p class="aiwpb-provides">' + esc( cur.provides ) + '</p>' : '' ) +
			( p.notes ? '<div class="aiwpb-notes"><strong>Notes</strong><br>' + esc( p.notes ).replace( /\n/g, '<br>' ) + '</div>' : '' ) +
			'<div class="aiwpb-edit-buttons">';
		Object.keys( KINDS ).forEach( function ( k ) {
			html += '<button type="button" class="button button-primary aiwpb-kind" data-kind="' + k + '">' + KINDS[ k ].label + '</button> ';
		} );
		var gh = cfg.github || {};
		var pubBlock = p.doNotPublish ? 'Marked do-not-publish (untick it under Edit details to allow publishing).' : ( gh.ready ? '' : 'No GitHub token saved yet (Settings → GitHub publishing).' );
		html += '<button type="button" class="button aiwpb-publish-gh"' + ( pubBlock ? ' disabled title="' + esc( pubBlock ) + '"' : '' ) + '>Publish v' + esc( cur.version ) + ' to GitHub</button> ';
		html += '<button type="button" class="button aiwpb-edit-fields" data-slug="' + esc( p.slug ) + '">Edit details</button>' +
			'<span class="aiwpb-dim"> Edits use: ' + esc( cfg.editAiLabel || 'no AI set' ) + ' (<a href="' + esc( cfg.settingsUrl ) + '">change</a>)</span></div>' +
			( pubBlock ? '<p class="aiwpb-dim">Publishing: ' + esc( pubBlock ) + '</p>' : '' ) +
			githubSection( p ) +
			'<div id="aiwpb-fields-panel"></div><div id="aiwpb-edit-panel"></div><div id="aiwpb-publish-panel"></div>' +
			'<h3>Versions</h3><table class="widefat striped aiwpb-versions"><thead><tr><th>#</th><th>Version</th><th>Kind</th><th>When</th><th>AI</th><th>Cost</th><th>Audit</th><th>What was asked</th><th>Actions</th></tr></thead><tbody>';
		p.versions.slice().reverse().forEach( function ( v ) {
			var isCur = v.id === p.currentVersionId;
			html += '<tr' + ( isCur ? ' class="aiwpb-current"' : '' ) + '><td>' + esc( v.id ) + '</td>' +
				'<td>v' + esc( v.version ) + ( isCur ? ' <span class="aiwpb-badge aiwpb-badge--done">current</span>' : '' ) + '</td>' +
				'<td>' + esc( v.kind ) + '</td><td>' + esc( date( v.createdAt ) ) + '</td><td>' + esc( v.ai || '—' ) + '</td>' +
				'<td>' + esc( money( v.costUsd ) ) + '</td>' +
				'<td>' + ( null === v.auditFindings || undefined === v.auditFindings ? 'n/a' : esc( v.auditFindings ) ) + '</td>' +
				'<td class="aiwpb-desc" title="' + esc( v.description ) + '">' + esc( String( v.description || '' ).slice( 0, 140 ) ) + ( String( v.description || '' ).length > 140 ? '…' : '' ) + '</td>' +
				'<td class="aiwpb-actions">' +
				'<a class="button button-small" href="' + esc( downloadUrl( p.slug, v.id ) ) + '">Download</a> ' +
				( v.reportFile ? '<button type="button" class="button button-small aiwpb-report" data-id="' + esc( v.id ) + '">Report</button> ' : '' ) +
				( isCur ? '' : '<button type="button" class="button button-small aiwpb-restore" data-id="' + esc( v.id ) + '">Make current</button> ' ) +
				'<button type="button" class="button button-small aiwpb-install" data-id="' + esc( v.id ) + '">Install here</button>' +
				'</td></tr>';
		} );
		html += '</tbody></table><div id="aiwpb-report"></div><div id="aiwpb-install-result"></div>';
		if ( p.activity && p.activity.length ) {
			html += '<h3>Activity</h3><ul class="aiwpb-activity">';
			p.activity.slice().reverse().slice( 0, 15 ).forEach( function ( a ) {
				html += '<li class="' + ( a.ok ? '' : 'aiwpb-bad' ) + '">' + esc( date( a.at ) ) + ' — ' + esc( a.message ) + '</li>';
			} );
			html += '</ul>';
		}
		var box = el( 'aiwpb-detail' );
		box.innerHTML = html;
		box.style.display = 'block';
		el( 'aiwpb-close' ).addEventListener( 'click', closeDetail );
		if ( 'fields' === focus ) {
			showFields();
		}
	}

	function githubSection( p ) {
		var g = p.published && p.published.github;
		if ( ! g || ! safeUrl( g.url ) ) {
			return '';
		}
		var html = '<div class="aiwpb-subcard"><strong>GitHub:</strong> <a href="' + esc( g.url ) + '" target="_blank" rel="noopener noreferrer">' + esc( g.owner + '/' + g.repo ) + '</a>' +
			' <span class="aiwpb-dim">(' + ( g.private ? 'private' : 'public' ) + ')</span><ul class="aiwpb-activity">';
		( g.releases || [] ).slice().reverse().forEach( function ( r ) {
			html += '<li>' + esc( r.tag ) + ' — ' + ( safeUrl( r.releaseUrl ) ? '<a href="' + esc( r.releaseUrl ) + '" target="_blank" rel="noopener noreferrer">release</a>' : '' ) +
				' <span class="aiwpb-dim">' + esc( date( r.at ) ) + ' · commit ' + esc( String( r.commit || '' ).slice( 0, 7 ) ) + '</span></li>';
		} );
		return html + '</ul></div>';
	}

	// ---------- publish to GitHub ----------
	var pub = null; // { jobId, timer, errors }
	function startPublish() {
		var cur = current.versions.filter( function ( v ) {
			return v.id === current.currentVersionId;
		} )[ 0 ] || {};
		var gh = cfg.github || {};
		var already = current.published && current.published.github;
		var msg = 'Publish ' + current.name + ' v' + cur.version + ' to GitHub?\n\n' +
			( already ? '• Repository: ' + already.owner + '/' + already.repo + ' (exists, ' + ( already.private ? 'private' : 'PUBLIC' ) + ')\n' :
				'• Creates a ' + ( gh.private ? 'PRIVATE' : 'PUBLIC' ) + ' repository ' + gh.owner + '/' + current.slug + '\n' ) +
			'• Re-runs every check first; stops if anything fails\n' +
			'• Commits the source to main, tags v' + cur.version + ', and creates a release with the .zip';
		if ( ! window.confirm( msg ) ) {
			return;
		}
		el( 'aiwpb-publish-panel' ).innerHTML = '<div class="aiwpb-subcard"><h3>Publishing to GitHub <span id="aiwpb-pub-state" class="aiwpb-badge"></span></h3><pre class="aiwpb-log" id="aiwpb-pub-log">Starting…</pre><div id="aiwpb-pub-result"></div></div>';
		setPubState( 'queued' );
		api( '/registry/' + encodeURIComponent( current.slug ) + '/publish', { method: 'POST', body: JSON.stringify( { versionId: cur.id } ) } )
			.then( function ( d ) {
				pub = { jobId: d.jobId, timer: null, errors: 0, slug: current.slug };
				pollPub();
			} )
			.catch( function ( e ) {
				setPubState( 'error' );
				el( 'aiwpb-pub-log' ).textContent = 'Error: ' + e.message;
			} );
	}
	function setPubState( s ) {
		var b = el( 'aiwpb-pub-state' );
		if ( b ) {
			b.textContent = s;
			b.className = 'aiwpb-badge aiwpb-badge--' + s;
		}
	}
	function pollPub() {
		if ( pub ) {
			window.clearTimeout( pub.timer );
			pub.timer = window.setTimeout( pubTick, 2000 );
		}
	}
	function pubTick() {
		if ( ! pub ) {
			return;
		}
		api( '/jobs/' + pub.jobId, { method: 'GET' } ).then( function ( job ) {
			pub.errors = 0;
			setPubState( job.status );
			var log = el( 'aiwpb-pub-log' );
			if ( log ) {
				log.textContent = ( job.log || [] ).join( '\n' );
				log.scrollTop = log.scrollHeight;
			}
			if ( 'done' === job.status ) {
				var g = ( job.artifact || {} ).github || {};
				var slug = pub.slug;
				pub = null;
				load();
				openDetail( slug ).then( function () {
					notice( 'Published ' + ( g.tag || '' ) + ' to GitHub: ' + ( g.releaseUrl || '' ) );
				} );
			} else if ( 'error' === job.status ) {
				if ( log ) {
					log.textContent += '\n✖ ' + ( job.error || 'Publishing failed.' ) + '\nNothing was published if the checks failed.';
				}
				pub = null;
			} else {
				pollPub();
			}
		} ).catch( function () {
			if ( pub && ++pub.errors <= 12 ) {
				pollPub();
			}
		} );
	}

	// ---------- edit details (CRM fields) ----------
	function showFields() {
		el( 'aiwpb-fields-panel' ).innerHTML = '<div class="aiwpb-subcard"><h3>Edit details</h3>' + fieldsForm( current, 'f' ) +
			'<p><button type="button" class="button button-primary" id="aiwpb-fields-save">Save</button> ' +
			'<button type="button" class="button" id="aiwpb-fields-cancel">Cancel</button></p></div>';
		el( 'aiwpb-fields-save' ).addEventListener( 'click', function () {
			api( '/registry/' + encodeURIComponent( current.slug ) + '/fields', { method: 'POST', body: JSON.stringify( readFields( 'f' ) ) } )
				.then( function () {
					notice( 'Saved.' );
					load();
					return openDetail( current.slug );
				} )
				.catch( function ( e ) {
					notice( e.message, 'error' );
				} );
		} );
		el( 'aiwpb-fields-cancel' ).addEventListener( 'click', function () {
			el( 'aiwpb-fields-panel' ).innerHTML = '';
		} );
	}

	// ---------- versions: report / restore / install ----------
	function showReport( id ) {
		el( 'aiwpb-report' ).innerHTML = '<p>Loading report…</p>';
		api( '/registry/' + encodeURIComponent( current.slug ) + '/versions/' + encodeURIComponent( id ) + '/report', { method: 'GET' } )
			.then( function ( d ) {
				el( 'aiwpb-report' ).innerHTML = '<h3>Report for version #' + esc( id ) + ' <button type="button" class="button-link" id="aiwpb-report-close">close</button></h3><pre class="aiwpb-log">' + esc( d.markdown ) + '</pre>';
				el( 'aiwpb-report-close' ).addEventListener( 'click', function () {
					el( 'aiwpb-report' ).innerHTML = '';
				} );
			} )
			.catch( function ( e ) {
				el( 'aiwpb-report' ).innerHTML = '<p>' + esc( e.message ) + '</p>';
			} );
	}
	function doRestore( id ) {
		if ( ! window.confirm( 'Make version #' + id + ' the current version? New edits will start from it. (Nothing is installed on this site.)' ) ) {
			return;
		}
		api( '/registry/' + encodeURIComponent( current.slug ) + '/restore', { method: 'POST', body: JSON.stringify( { versionId: id } ) } )
			.then( function () {
				notice( 'Version #' + id + ' is now current.' );
				load();
				return openDetail( current.slug );
			} )
			.catch( function ( e ) {
				notice( e.message, 'error' );
			} );
	}
	function doInstall( id ) {
		var v = current.versions.filter( function ( x ) {
			return x.id === id;
		} )[ 0 ] || {};
		var what = current.installedVersion ? 'replace the installed v' + current.installedVersion + ' with v' + v.version + ' (a backup is kept for roll-back)' : 'install and activate v' + v.version;
		if ( ! window.confirm( 'On this live site, ' + what + '?' ) ) {
			return;
		}
		el( 'aiwpb-install-result' ).innerHTML = '<p>Installing…</p>';
		api( '/registry/' + encodeURIComponent( current.slug ) + '/install', { method: 'POST', body: JSON.stringify( { versionId: id } ) } )
			.then( function ( r ) {
				el( 'aiwpb-install-result' ).innerHTML = '<div class="notice notice-success"><p>' + esc( r.message ) + '</p></div>';
				load();
				return openDetail( current.slug );
			} )
			.catch( function ( e ) {
				el( 'aiwpb-install-result' ).innerHTML = '<div class="notice notice-error"><p>' + esc( e.message ) + '</p></div>';
			} );
	}

	// ---------- edits: chat, start, progress ----------
	function bubble( role, content ) {
		return '<div class="aiwpb-msg aiwpb-msg--' + role + '"><div class="aiwpb-bubble">' + esc( content ).replace( /\n/g, '<br>' ) + '</div></div>';
	}
	function openEdit( kind ) {
		stopPolling();
		edit = { kind: kind, messages: [], jobId: null, timer: null, errors: 0 };
		var k = KINDS[ kind ];
		el( 'aiwpb-edit-panel' ).innerHTML = '<div class="aiwpb-subcard"><h3>' + esc( k.label ) + ': ' + esc( current.name ) + '</h3>' +
			'<p class="description">Describe ' + esc( k.what ) + '. The assistant may ask a question or two; click <strong>Start ' + esc( k.label.toLowerCase() ) + '</strong> when it is clear. ' +
			'The result is re-checked against every gate and saved as a new version — nothing changes on this site until you install it.</p>' +
			'<div class="aiwpb-messages aiwpb-messages--small" id="aiwpb-edit-messages"></div>' +
			'<div class="aiwpb-chat-input"><input type="text" class="large-text" id="aiwpb-edit-input" placeholder="' + esc( k.placeholder ) + '" autocomplete="off">' +
			'<button type="button" class="button" id="aiwpb-edit-send">Send</button><span class="spinner" id="aiwpb-edit-chat-spinner"></span></div>' +
			'<p><button type="button" class="button button-primary" id="aiwpb-edit-start" disabled>Start ' + esc( k.label.toLowerCase() ) + '</button> ' +
			'<button type="button" class="button" id="aiwpb-edit-cancel">Cancel</button></p>' +
			'<div id="aiwpb-edit-status" style="display:none;"><h4>Progress <span id="aiwpb-edit-state" class="aiwpb-badge"></span></h4><pre class="aiwpb-log" id="aiwpb-edit-log"></pre></div>' +
			'<div id="aiwpb-edit-result"></div></div>';
		renderEditMessages();
		el( 'aiwpb-edit-send' ).addEventListener( 'click', sendEdit );
		el( 'aiwpb-edit-input' ).addEventListener( 'keydown', function ( ev ) {
			if ( 'Enter' === ev.key ) {
				sendEdit();
			}
		} );
		el( 'aiwpb-edit-start' ).addEventListener( 'click', startEdit );
		el( 'aiwpb-edit-cancel' ).addEventListener( 'click', function () {
			stopPolling();
			edit = null;
			el( 'aiwpb-edit-panel' ).innerHTML = '';
		} );
		el( 'aiwpb-edit-input' ).focus();
	}
	function renderEditMessages() {
		var html = bubble( 'assistant', 'What ' + KINDS[ edit.kind ].what + ' would you like for ' + current.name + '?' );
		edit.messages.forEach( function ( m ) {
			html += bubble( m.role, m.content );
		} );
		var box = el( 'aiwpb-edit-messages' );
		box.innerHTML = html;
		box.scrollTop = box.scrollHeight;
	}
	function editContext() {
		var cur = current.versions.filter( function ( v ) {
			return v.id === current.currentVersionId;
		} )[ 0 ] || {};
		return 'Plugin: ' + current.name + ' (' + current.slug + ') v' + ( cur.version || '?' ) + '\n' +
			'What it provides: ' + ( cur.provides || 'unknown' ) + '\n' +
			'Edit requested: ' + KINDS[ edit.kind ].what;
	}
	function sendEdit() {
		var text = el( 'aiwpb-edit-input' ).value.trim();
		if ( ! text || ! edit ) {
			return;
		}
		edit.messages.push( { role: 'user', content: text } );
		el( 'aiwpb-edit-input' ).value = '';
		renderEditMessages();
		el( 'aiwpb-edit-start' ).disabled = false;
		el( 'aiwpb-edit-chat-spinner' ).classList.add( 'is-active' );
		api( '/chat', { method: 'POST', body: JSON.stringify( { messages: edit.messages, platform: cfg.editPlatform, mode: 'edit', context: editContext() } ) } )
			.then( function ( d ) {
				edit.messages.push( { role: 'assistant', content: d.reply || '(no reply)' } );
			} )
			.catch( function ( e ) {
				edit.messages.push( { role: 'assistant', content: 'Error: ' + e.message } );
			} )
			.then( function () {
				if ( edit ) {
					renderEditMessages();
					el( 'aiwpb-edit-chat-spinner' ).classList.remove( 'is-active' );
					el( 'aiwpb-edit-input' ).focus();
				}
			} );
	}
	function startEdit() {
		if ( ! edit || ! edit.messages.length ) {
			return;
		}
		var request = edit.messages.map( function ( m ) {
			return ( 'user' === m.role ? 'User: ' : 'Assistant: ' ) + m.content;
		} ).join( '\n\n' );
		el( 'aiwpb-edit-start' ).disabled = true;
		el( 'aiwpb-edit-status' ).style.display = 'block';
		el( 'aiwpb-edit-log' ).textContent = 'Starting…';
		setEditState( 'queued' );
		api( '/registry/' + encodeURIComponent( current.slug ) + '/edit', { method: 'POST', body: JSON.stringify( { kind: edit.kind, request: request } ) } )
			.then( function ( d ) {
				edit.jobId = d.jobId;
				poll();
			} )
			.catch( function ( e ) {
				setEditState( 'error' );
				el( 'aiwpb-edit-log' ).textContent += '\nError: ' + e.message;
				el( 'aiwpb-edit-start' ).disabled = false;
			} );
	}
	function setEditState( s ) {
		var b = el( 'aiwpb-edit-state' );
		if ( b ) {
			b.textContent = s;
			b.className = 'aiwpb-badge aiwpb-badge--' + s;
		}
	}
	function stopPolling() {
		if ( edit && edit.timer ) {
			window.clearTimeout( edit.timer );
			edit.timer = null;
		}
	}
	function poll() {
		stopPolling();
		edit.timer = window.setTimeout( tick, 2000 );
	}
	function tick() {
		if ( ! edit || ! edit.jobId ) {
			return;
		}
		var slug = current.slug;
		api( '/jobs/' + edit.jobId, { method: 'GET' } ).then( function ( job ) {
			if ( ! edit ) {
				return;
			}
			edit.errors = 0;
			setEditState( job.status );
			var log = el( 'aiwpb-edit-log' );
			log.textContent = ( job.log || [] ).join( '\n' );
			log.scrollTop = log.scrollHeight;
			if ( 'done' === job.status ) {
				var a = job.artifact || {};
				el( 'aiwpb-edit-result' ).innerHTML = '<div class="notice notice-success"><p>' + esc( KINDS[ edit.kind ].label + ' done: v' + ( a.version || '?' ) + ' is saved as the current version. Use "Install here" on it below to put it on this site.' ) + '</p></div>';
				load();
				openDetail( slug ).then( function () {
					if ( edit ) {
						edit = null;
					}
				} );
			} else if ( 'error' === job.status ) {
				log.textContent += '\n✖ ' + ( job.error || 'The edit did not pass.' ) + '\nNothing was saved; the current version is unchanged.';
				el( 'aiwpb-edit-start' ).disabled = false;
			} else {
				poll();
			}
		} ).catch( function ( e ) {
			if ( ! edit ) {
				return;
			}
			edit.errors++;
			if ( edit.errors <= 12 ) {
				poll();
				return;
			}
			setEditState( 'error' );
			el( 'aiwpb-edit-log' ).textContent += '\nError: ' + e.message;
		} );
	}

	// ---------- wiring ----------
	document.addEventListener( 'DOMContentLoaded', function () {
		if ( ! cfg.configured ) {
			notice( 'The builder service is not configured yet — set its URL and key under AI Plugin Builder → Settings.', 'warning' );
		}
		el( 'aiwpb-status-filter' ).innerHTML = statusOptions( '', true );
		el( 'aiwpb-search' ).addEventListener( 'input', renderTable );
		el( 'aiwpb-status-filter' ).addEventListener( 'change', renderTable );
		el( 'aiwpb-add-toggle' ).addEventListener( 'click', function () {
			var box = el( 'aiwpb-add' );
			if ( 'none' === box.style.display ) {
				renderAdd();
				box.style.display = 'block';
			} else {
				box.style.display = 'none';
			}
		} );
		// Delegated clicks for the table and the detail panel.
		document.addEventListener( 'click', function ( e ) {
			var t = e.target.closest ? e.target.closest( 'button, a' ) : null;
			if ( ! t ) {
				return;
			}
			if ( t.classList.contains( 'aiwpb-view' ) ) {
				e.preventDefault();
				openDetail( t.getAttribute( 'data-slug' ) );
			} else if ( t.classList.contains( 'aiwpb-edit-fields' ) ) {
				e.preventDefault();
				var slug = t.getAttribute( 'data-slug' );
				if ( current && current.slug === slug ) {
					showFields();
				} else {
					openDetail( slug, 'fields' );
				}
			} else if ( t.classList.contains( 'aiwpb-delete' ) ) {
				doDelete( t.getAttribute( 'data-slug' ) );
			} else if ( t.classList.contains( 'aiwpb-publish-gh' ) ) {
				startPublish();
			} else if ( t.classList.contains( 'aiwpb-kind' ) ) {
				openEdit( t.getAttribute( 'data-kind' ) );
			} else if ( t.classList.contains( 'aiwpb-report' ) ) {
				showReport( Number( t.getAttribute( 'data-id' ) ) );
			} else if ( t.classList.contains( 'aiwpb-restore' ) ) {
				doRestore( Number( t.getAttribute( 'data-id' ) ) );
			} else if ( t.classList.contains( 'aiwpb-install' ) ) {
				doInstall( Number( t.getAttribute( 'data-id' ) ) );
			}
		} );
		load();
	} );
} )();
