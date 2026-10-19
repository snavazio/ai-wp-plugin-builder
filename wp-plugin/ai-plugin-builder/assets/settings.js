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
				// Show the platform's models after every test: collapsed when the test passed, open when it failed
				// (so a wrong model name can be fixed by picking one from the list).
				if ( r.models && r.models.length ) {
					html += '<details class="aiwpb-models"' + ( r.ok ? '' : ' open' ) + '><summary>' + escapeHtml( cfg.models ) +
						' (' + r.models.length + ')</summary>' + r.models.map( function ( m ) {
							var price = r.prices && r.prices[ m ] ? ' <span class="aiwpb-dim">' + escapeHtml( r.prices[ m ] ) + '</span>' : '';
							return '<div><code>' + escapeHtml( m ) + '</code>' + price + '</div>';
						} ).join( '' ) + '</details>';
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

	// ---- Add/edit form: Model dropdown filled from the platform's own model list ----
	var modelSeq = 0;
	function formVal( id ) {
		var f = document.getElementById( id );
		return f ? f.value : '';
	}
	function setModelStatus( text, bad ) {
		var st = document.getElementById( 'aiwpb-p-model-status' );
		if ( st ) {
			st.textContent = text;
			st.className = 'description' + ( bad ? ' aiwpb-test--fail' : '' );
		}
	}
	/** Swap the model control between a <select> and a plain text box, keeping its value. */
	function modelControl( asSelect ) {
		var cur = document.getElementById( 'aiwpb-p-model' );
		if ( ! cur || ( 'SELECT' === cur.tagName ) === asSelect ) {
			return cur;
		}
		var next = document.createElement( asSelect ? 'select' : 'input' );
		next.id = 'aiwpb-p-model';
		next.name = 'model';
		next.className = 'regular-text';
		next.setAttribute( 'data-current', cur.getAttribute( 'data-current' ) || '' );
		if ( ! asSelect ) {
			next.type = 'text';
			next.value = cur.value || cur.getAttribute( 'data-current' ) || '';
			next.placeholder = 'e.g. claude-sonnet-5-5, qwen3:30b';
		}
		cur.parentNode.replaceChild( next, cur );
		return next;
	}
	function loadModels() {
		var ctl = document.getElementById( 'aiwpb-p-model' );
		if ( ! ctl ) {
			return;
		}
		// Remember the user's choice so a reload keeps it selected.
		if ( ctl.value ) {
			ctl.setAttribute( 'data-current', ctl.value );
		}
		var seq = ++modelSeq;
		var spin = document.getElementById( 'aiwpb-p-model-spinner' );
		if ( spin ) {
			spin.classList.add( 'is-active' );
		}
		setModelStatus( 'Loading models…', false );
		var idField = document.querySelector( 'form input[name="id"]' );
		fetch( cfg.root + '/platforms/models', {
			method: 'POST',
			headers: { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' },
			credentials: 'same-origin',
			body: JSON.stringify( {
				id: idField ? idField.value : '',
				type: formVal( 'aiwpb-p-type' ),
				base_url: formVal( 'aiwpb-p-base' ),
				api_key: formVal( 'aiwpb-p-key' ),
			} ),
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
				if ( seq !== modelSeq ) {
					return; // a newer request superseded this one
				}
				var models = ( r.models || [] ).slice().sort();
				if ( ! models.length ) {
					modelControl( false );
					setModelStatus( ( r.message || 'No models were returned.' ) + ' Type the model name instead.', true );
					return;
				}
				var sel = modelControl( true );
				var current = sel.getAttribute( 'data-current' ) || '';
				var html = '';
				if ( ! current ) {
					html += '<option value="">— choose a model —</option>';
				} else if ( models.indexOf( current ) === -1 ) {
					html += '<option value="' + escapeHtml( current ) + '">' + escapeHtml( current + ' (not offered by this platform)' ) + '</option>';
				}
				var prices = r.prices || {};
				models.forEach( function ( m ) {
					html += '<option value="' + escapeHtml( m ) + '">' + escapeHtml( m + ( prices[ m ] ? ' — ' + prices[ m ] : '' ) ) + '</option>';
				} );
				sel.innerHTML = html;
				sel.value = current;
				var priced = models.filter( function ( m ) {
					return prices[ m ];
				} ).length;
				setModelStatus( models.length + ' model' + ( 1 === models.length ? '' : 's' ) + ' available on this platform.' +
					( priced ? ' Prices are input / output per 1M tokens (list prices).' : ' This platform does not publish prices through its API.' ), false );
			} )
			.catch( function ( err ) {
				if ( seq !== modelSeq ) {
					return;
				}
				modelControl( false );
				setModelStatus( 'Could not load models (' + err.message + '). Type the model name instead.', true );
			} )
			.then( function () {
				if ( spin && seq === modelSeq ) {
					spin.classList.remove( 'is-active' );
				}
			} );
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
			type.addEventListener( 'change', function () {
				syncType();
				// A different platform type has different models: drop the old choice.
				var ctl = document.getElementById( 'aiwpb-p-model' );
				if ( ctl ) {
					ctl.value = '';
					ctl.setAttribute( 'data-current', '' );
				}
				loadModels();
			} );
			syncType();
		}
		[ 'aiwpb-p-base', 'aiwpb-p-key' ].forEach( function ( id ) {
			var f = document.getElementById( id );
			if ( f ) {
				f.addEventListener( 'change', loadModels );
			}
		} );
		var ghTest = document.getElementById( 'aiwpb-gh-test' );
		if ( ghTest ) {
			ghTest.addEventListener( 'click', function () {
				var out = document.getElementById( 'aiwpb-gh-test-result' );
				ghTest.disabled = true;
				out.className = 'aiwpb-test';
				out.textContent = cfg.testing;
				fetch( cfg.root + '/github/test', {
					method: 'POST',
					headers: { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' },
					credentials: 'same-origin',
					body: JSON.stringify( { token: formVal( 'aiwpb-gh-token' ) } ),
				} )
					.then( function ( res ) {
						return res.json();
					} )
					.then( function ( r ) {
						out.className = 'aiwpb-test ' + ( r.ok ? 'aiwpb-test--ok' : 'aiwpb-test--fail' );
						out.textContent = ( r.ok ? '✔ ' : '✖ ' ) + ( r.message || '' );
					} )
					.catch( function ( e ) {
						out.className = 'aiwpb-test aiwpb-test--fail';
						out.textContent = '✖ ' + e.message;
					} )
					.then( function () {
						ghTest.disabled = false;
					} );
			} );
		}
		var refresh = document.getElementById( 'aiwpb-p-model-load' );
		if ( refresh ) {
			refresh.addEventListener( 'click', loadModels );
			loadModels();
		}
	} );
} )();
