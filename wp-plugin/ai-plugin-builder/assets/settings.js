/* global window, document, fetch */
( function () {
	'use strict';
	var cfg = window.AIWPB_SETTINGS || {};

	function escapeHtml( s ) {
		var d = document.createElement( 'div' );
		d.textContent = String( s == null ? '' : s );
		return d.innerHTML;
	}

	// ---- Test connection (runs on the builder service; result is also saved on the platform) ----
	function onTest( btn ) {
		var id = btn.getAttribute( 'data-id' );
		var cell = document.getElementById( 'aiwpb-test-' + id );
		btn.disabled = true;
		cell.innerHTML = '<span class="aiwpb-test">' + escapeHtml( cfg.testing ) + '</span>';
		fetch( cfg.root + '/platforms/' + encodeURIComponent( id ) + '/test', {
			method: 'POST',
			headers: { 'X-WP-Nonce': cfg.nonce },
			credentials: 'same-origin',
		} )
			.then( function ( res ) {
				return res.json().then( function ( data ) {
					if ( ! res.ok ) {
						throw new Error( data.message || ( 'HTTP ' + res.status ) );
					}
					return data;
				} );
			} )
			.then( function ( r ) {
				var html = '<span class="aiwpb-test ' + ( r.ok ? 'aiwpb-test--ok' : 'aiwpb-test--fail' ) + '">' +
					( r.ok ? '✔ ' : '✖ ' ) + escapeHtml( r.message ) + '</span>';
				if ( r.ms ) {
					html += '<br><small>' + escapeHtml( r.ms + ' ms' ) + '</small>';
				}
				if ( ! r.ok && r.models && r.models.length ) {
					html += '<details class="aiwpb-models"><summary>' + escapeHtml( cfg.models ) + '</summary><code>' +
						r.models.map( escapeHtml ).join( '</code> <code>' ) + '</code></details>';
				}
				cell.innerHTML = html;
			} )
			.catch( function ( err ) {
				cell.innerHTML = '<span class="aiwpb-test aiwpb-test--fail">✖ ' + escapeHtml( err.message ) + '</span>';
			} )
			.then( function () {
				btn.disabled = false;
			} );
	}

	// ---- Add/edit form: show the chosen type's standard endpoint as the placeholder ----
	function syncType() {
		var type = document.getElementById( 'aiwpb-p-type' );
		var base = document.getElementById( 'aiwpb-p-base' );
		if ( ! type || ! base ) {
			return;
		}
		var t = ( cfg.types || {} )[ type.value ] || {};
		base.placeholder = t.baseUrl || 'https://…/v1';
		base.required = ! t.baseUrl;
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.aiwpb-test-btn' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				onTest( btn );
			} );
		} );
		document.querySelectorAll( '.aiwpb-delete' ).forEach( function ( a ) {
			a.addEventListener( 'click', function ( e ) {
				if ( ! window.confirm( 'Delete this AI platform?' ) ) {
					e.preventDefault();
				}
			} );
		} );
		var type = document.getElementById( 'aiwpb-p-type' );
		if ( type ) {
			type.addEventListener( 'change', syncType );
			syncType();
		}
	} );
} )();
