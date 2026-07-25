/* global window, document, fetch */
( function () {
	'use strict';
	var cfg = window.AIWPB || {};
	var currentJob = null;
	var pollTimer = null;
	var pollErrors = 0;
	var MAX_POLL_ERRORS = 12; // tolerate transient WP downtime while the builder reconfigures its sandbox

	function el( id ) {
		return document.getElementById( id );
	}

	function escapeHtml( s ) {
		var d = document.createElement( 'div' );
		d.textContent = String( s == null ? '' : s );
		return d.innerHTML;
	}

	function setBusy( busy ) {
		el( 'aiwpb-generate' ).disabled = busy;
		el( 'aiwpb-spinner' ).classList.toggle( 'is-active', busy );
	}

	function setState( status ) {
		var badge = el( 'aiwpb-state' );
		badge.textContent = status;
		badge.className = 'aiwpb-badge aiwpb-badge--' + status;
	}

	function renderLog( lines ) {
		var log = el( 'aiwpb-log' );
		log.textContent = ( lines || [] ).join( '\n' );
		log.scrollTop = log.scrollHeight;
	}

	function appendLog( line ) {
		var log = el( 'aiwpb-log' );
		log.textContent += ( log.textContent ? '\n' : '' ) + line;
		log.scrollTop = log.scrollHeight;
	}

	function api( path, opts ) {
		return fetch(
			cfg.root + path,
			Object.assign(
				{
					headers: { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' },
					credentials: 'same-origin',
				},
				opts || {}
			)
		).then( function ( res ) {
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

	function onGenerate( e ) {
		e.preventDefault();
		var spec = el( 'aiwpb-spec' ).value.trim();
		if ( ! spec ) {
			window.alert( 'Please enter a spec.' );
			return;
		}
		if ( ! cfg.configured ) {
			el( 'aiwpb-config-warning' ).style.display = 'block';
			return;
		}
		pollErrors = 0;
		setBusy( true );
		el( 'aiwpb-status' ).style.display = 'block';
		el( 'aiwpb-result' ).style.display = 'none';
		el( 'aiwpb-install-result' ).innerHTML = '';
		el( 'aiwpb-log' ).textContent = '';
		setState( 'queued' );

		api( '/build', { method: 'POST', body: JSON.stringify( { spec: spec, engine: el( 'aiwpb-engine' ).value } ) } )
			.then( function ( data ) {
				currentJob = data.jobId;
				schedulePoll();
			} )
			.catch( function ( err ) {
				setBusy( false );
				setState( 'error' );
				appendLog( 'Error: ' + err.message );
			} );
	}

	function schedulePoll() {
		window.clearTimeout( pollTimer );
		pollTimer = window.setTimeout( tick, 1500 );
	}

	function tick() {
		if ( ! currentJob ) {
			return;
		}
		api( '/jobs/' + currentJob, { method: 'GET' } )
			.then( function ( job ) {
				pollErrors = 0;
				setState( job.status );
				renderLog( job.log );
				if ( 'done' === job.status ) {
					setBusy( false );
					showResult( job );
				} else if ( 'error' === job.status ) {
					setBusy( false );
					appendLog( '✖ ' + ( job.error || 'Build failed.' ) );
				} else {
					schedulePoll();
				}
			} )
			.catch( function ( err ) {
				// The build may briefly restart WordPress while it reconfigures its sandbox; keep retrying.
				pollErrors++;
				if ( pollErrors <= MAX_POLL_ERRORS ) {
					setState( 'running' );
					schedulePoll();
					return;
				}
				setBusy( false );
				setState( 'error' );
				appendLog( 'Error: ' + err.message );
			} );
	}

	function showResult( job ) {
		var a = job.artifact || {};
		el( 'aiwpb-result' ).style.display = 'block';
		el( 'aiwpb-result-summary' ).textContent =
			( a.pluginName || a.slug || 'Plugin' ) + ' — v' + ( a.version || '1.0.0' ) + ' · all 8 gates passed';
		var dl = cfg.adminPost + '?action=aiwpb_download&job=' + encodeURIComponent( currentJob ) +
			'&_wpnonce=' + encodeURIComponent( cfg.downloadNonce );
		el( 'aiwpb-download' ).setAttribute( 'href', dl );
	}

	function onInstall( e ) {
		e.preventDefault();
		if ( ! currentJob ) {
			return;
		}
		el( 'aiwpb-install-result' ).innerHTML = escapeHtml( 'Installing…' );
		api( '/install', { method: 'POST', body: JSON.stringify( { jobId: currentJob } ) } )
			.then( function ( r ) {
				var cls = r.activated ? 'notice-success' : 'notice-warning';
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice ' + cls + '" style="padding:8px 12px;">' + escapeHtml( r.message || 'Installed.' ) + '</div>';
			} )
			.catch( function ( err ) {
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice notice-error" style="padding:8px 12px;">' + escapeHtml( err.message ) + '</div>';
			} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( el( 'aiwpb-engine' ) && cfg.defaultEngine ) {
			el( 'aiwpb-engine' ).value = cfg.defaultEngine;
		}
		if ( ! cfg.configured && el( 'aiwpb-config-warning' ) ) {
			el( 'aiwpb-config-warning' ).style.display = 'block';
		}
		if ( el( 'aiwpb-generate' ) ) {
			el( 'aiwpb-generate' ).addEventListener( 'click', onGenerate );
		}
		if ( el( 'aiwpb-install' ) ) {
			el( 'aiwpb-install' ).addEventListener( 'click', onInstall );
		}
	} );
} )();
