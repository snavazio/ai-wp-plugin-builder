/* global window, document, fetch */
( function () {
	'use strict';
	var cfg = window.AIWPB || {};
	var GREETING = "Hi! Tell me what plugin you'd like to build — the gist is fine, and I'll ask a couple of questions to get it right.";

	var messages = []; // real conversation turns: { role: 'user'|'assistant', content }
	var currentJob = null;
	var pollTimer = null;
	var pollErrors = 0;
	var MAX_POLL_ERRORS = 12;
	var mode = 'new'; // 'new' | 'update'
	var lastUpdateSlug = null;

	function el( id ) {
		return document.getElementById( id );
	}
	function escapeHtml( s ) {
		var d = document.createElement( 'div' );
		d.textContent = String( s == null ? '' : s );
		return d.innerHTML;
	}

	// ---- chat rendering ----
	function bubble( role, content ) {
		return '<div class="aiwpb-msg aiwpb-msg--' + role + '"><div class="aiwpb-bubble">' +
			escapeHtml( content ).replace( /\n/g, '<br>' ) + '</div></div>';
	}
	function renderMessages() {
		var box = el( 'aiwpb-messages' );
		var html = bubble( 'assistant', GREETING );
		for ( var i = 0; i < messages.length; i++ ) {
			html += bubble( messages[ i ].role, messages[ i ].content );
		}
		box.innerHTML = html;
		box.scrollTop = box.scrollHeight;
	}
	function setChatBusy( busy ) {
		el( 'aiwpb-send' ).disabled = busy;
		el( 'aiwpb-input' ).disabled = busy;
		el( 'aiwpb-chat-spinner' ).classList.toggle( 'is-active', busy );
	}
	function setBuildBusy( busy ) {
		el( 'aiwpb-build' ).disabled = busy;
		el( 'aiwpb-spinner' ).classList.toggle( 'is-active', busy );
	}
	function setState( status ) {
		var badge = el( 'aiwpb-state' );
		badge.textContent = status;
		badge.className = 'aiwpb-badge aiwpb-badge--' + status;
	}

	// ---- api helper ----
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

	// ---- chat turn ----
	function onSend( e ) {
		if ( e ) {
			e.preventDefault();
		}
		var text = el( 'aiwpb-input' ).value.trim();
		if ( ! text ) {
			return;
		}
		if ( ! cfg.configured ) {
			el( 'aiwpb-config-warning' ).style.display = 'block';
			return;
		}
		messages.push( { role: 'user', content: text } );
		el( 'aiwpb-input' ).value = '';
		renderMessages();
		setChatBusy( true );
		api( '/chat', { method: 'POST', body: JSON.stringify( { messages: messages } ) } )
			.then( function ( data ) {
				messages.push( { role: 'assistant', content: data.reply || '(no reply)' } );
				renderMessages();
				el( 'aiwpb-build' ).disabled = false; // enough context to build once the assistant has replied
			} )
			.catch( function ( err ) {
				messages.push( { role: 'assistant', content: 'Error: ' + err.message } );
				renderMessages();
			} )
			.then( function () {
				setChatBusy( false );
				el( 'aiwpb-input' ).focus();
			} );
	}

	// ---- mode switching + ingest ----
	function setMode( m ) {
		mode = m;
		el( 'aiwpb-mode-new' ).className = 'button' + ( 'new' === m ? ' button-primary' : '' );
		el( 'aiwpb-mode-update' ).className = 'button' + ( 'update' === m ? ' button-primary' : '' );
		el( 'aiwpb-source' ).style.display = 'update' === m ? 'block' : 'none';
		el( 'aiwpb-build' ).textContent = 'update' === m ? 'Update plugin' : 'Build plugin';
		if ( 'update' === m ) {
			loadInstalled();
		}
	}
	function loadInstalled() {
		var sel = el( 'aiwpb-installed' );
		if ( sel.getAttribute( 'data-loaded' ) ) {
			return;
		}
		api( '/plugins', { method: 'GET' } ).then( function ( d ) {
			( d.plugins || [] ).forEach( function ( p ) {
				var o = document.createElement( 'option' );
				o.value = p.slug;
				o.textContent = p.name + ' (' + p.version + ')';
				sel.appendChild( o );
			} );
			sel.setAttribute( 'data-loaded', '1' );
		} ).catch( function () {} );
	}
	function readUploadB64() {
		return new Promise( function ( resolve, reject ) {
			var f = el( 'aiwpb-zip' ).files[ 0 ];
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
	function startUpdate() {
		if ( ! messages.length ) {
			window.alert( 'Describe the change in the chat first.' );
			return;
		}
		var srcType = ( document.querySelector( 'input[name="aiwpb-src"]:checked' ) || {} ).value || 'installed';
		var transcript = messages.map( function ( m ) {
			return ( 'user' === m.role ? 'User: ' : 'Assistant: ' ) + m.content;
		} ).join( '\n\n' );
		var payload = { spec: transcript, engine: el( 'aiwpb-engine' ).value, source: srcType };
		var prep;
		if ( 'installed' === srcType ) {
			var slug = el( 'aiwpb-installed' ).value;
			if ( ! slug ) {
				window.alert( 'Choose an installed plugin to update.' );
				return;
			}
			payload.slug = slug;
			prep = Promise.resolve();
		} else {
			prep = readUploadB64().then( function ( b64 ) {
				payload.zipB64 = b64;
			} );
		}
		setBuildBusy( true );
		el( 'aiwpb-status' ).style.display = 'block';
		el( 'aiwpb-result' ).style.display = 'none';
		el( 'aiwpb-install-result' ).innerHTML = '';
		pollErrors = 0;
		setState( 'queued' );
		el( 'aiwpb-log' ).textContent = 'Updating the plugin from your conversation…\n';
		prep.then( function () {
			return api( '/ingest', { method: 'POST', body: JSON.stringify( payload ) } );
		} ).then( function ( data ) {
			currentJob = data.jobId;
			schedulePoll();
		} ).catch( function ( err ) {
			setBuildBusy( false );
			setState( 'error' );
			el( 'aiwpb-log' ).textContent += '\nError: ' + err.message;
		} );
	}

	// ---- build from the conversation ----
	function onBuild( e ) {
		if ( e ) {
			e.preventDefault();
		}
		if ( 'update' === mode ) {
			startUpdate();
			return;
		}
		if ( ! messages.length ) {
			return;
		}
		startNewBuild();
	}

	function startNewBuild() {
		setBuildBusy( true );
		el( 'aiwpb-status' ).style.display = 'block';
		el( 'aiwpb-result' ).style.display = 'none';
		el( 'aiwpb-install-result' ).innerHTML = '';
		pollErrors = 0;
		setState( 'queued' );

		// Send the actual conversation transcript to the builder; its spec-writer structures it faithfully
		// (no lossy intermediate "distill" step, and it sees the user's own words).
		var transcript = messages.map( function ( m ) {
			return ( 'user' === m.role ? 'User: ' : 'Assistant: ' ) + m.content;
		} ).join( '\n\n' );
		var spec = 'The following is a conversation in which a user described the WordPress plugin they want. ' +
			'Build exactly what they asked for; use sensible WordPress defaults for anything left unspecified.\n\n' + transcript;
		el( 'aiwpb-log' ).textContent = 'Building from your conversation…\n';

		api( '/build', { method: 'POST', body: JSON.stringify( { spec: spec, engine: el( 'aiwpb-engine' ).value } ) } )
			.then( function ( data ) {
				currentJob = data.jobId;
				schedulePoll();
			} )
			.catch( function ( err ) {
				setBuildBusy( false );
				setState( 'error' );
				el( 'aiwpb-log' ).textContent += '\nError: ' + err.message;
			} );
	}

	// ---- build progress ----
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
				var log = el( 'aiwpb-log' );
				log.textContent = ( job.log || [] ).join( '\n' );
				log.scrollTop = log.scrollHeight;
				if ( 'done' === job.status ) {
					setBuildBusy( false );
					showResult( job );
				} else if ( 'error' === job.status ) {
					setBuildBusy( false );
					log.textContent += '\n✖ ' + ( job.error || 'Build failed.' );
				} else {
					schedulePoll();
				}
			} )
			.catch( function ( err ) {
				pollErrors++;
				if ( pollErrors <= MAX_POLL_ERRORS ) {
					setState( 'running' );
					schedulePoll();
					return;
				}
				setBuildBusy( false );
				setState( 'error' );
				el( 'aiwpb-log' ).textContent += '\nError: ' + err.message;
			} );
	}
	function showResult( job ) {
		var a = job.artifact || {};
		var name = a.pluginName || a.slug || 'Plugin';
		el( 'aiwpb-result' ).style.display = 'block';
		el( 'aiwpb-result-summary' ).textContent = name + ' — v' + ( a.version || '1.0.0' ) + ' · all 8 gates passed';
		el( 'aiwpb-result-provides' ).textContent = a.provides ? ( 'Provides — ' + a.provides ) : '';
		var dl = cfg.adminPost + '?action=aiwpb_download&job=' + encodeURIComponent( currentJob ) +
			'&_wpnonce=' + encodeURIComponent( cfg.downloadNonce );
		el( 'aiwpb-download' ).setAttribute( 'href', dl );
		if ( 'update' === mode ) {
			el( 'aiwpb-install' ).style.display = 'none';
			el( 'aiwpb-update-inplace' ).style.display = '';
			el( 'aiwpb-rollback' ).style.display = 'none';
			lastUpdateSlug = a.slug || null;
		} else {
			el( 'aiwpb-install' ).style.display = '';
			el( 'aiwpb-update-inplace' ).style.display = 'none';
			el( 'aiwpb-rollback' ).style.display = 'none';
		}
		// Keep the conversation open so the user can revise and rebuild.
		var note = 'Built ✅ ' + name + '.';
		if ( a.provides ) {
			note += ' It provides: ' + a.provides;
		}
		note += ' Want to change anything? Tell me what to adjust, then click "Build plugin" again.';
		messages.push( { role: 'assistant', content: note } );
		renderMessages();
		el( 'aiwpb-build' ).disabled = false;
		el( 'aiwpb-input' ).focus();
	}
	function onInstall( e ) {
		e.preventDefault();
		if ( ! currentJob ) {
			return;
		}
		el( 'aiwpb-install-result' ).innerHTML = escapeHtml( 'Installing…' );
		api( '/install', { method: 'POST', body: JSON.stringify( { jobId: currentJob } ) } )
			.then( function ( r ) {
				var css = r.activated ? 'notice-success' : 'notice-warning';
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice ' + css + '" style="padding:8px 12px;">' + escapeHtml( r.message || 'Installed.' ) + '</div>';
			} )
			.catch( function ( err ) {
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice notice-error" style="padding:8px 12px;">' + escapeHtml( err.message ) + '</div>';
			} );
	}
	function onUpdateInPlace( e ) {
		e.preventDefault();
		if ( ! currentJob ) {
			return;
		}
		el( 'aiwpb-install-result' ).innerHTML = escapeHtml( 'Updating in place…' );
		api( '/update', { method: 'POST', body: JSON.stringify( { jobId: currentJob } ) } )
			.then( function ( r ) {
				lastUpdateSlug = r.slug || lastUpdateSlug;
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice notice-success" style="padding:8px 12px;">' + escapeHtml( r.message || 'Updated.' ) + '</div>';
				if ( r.canRollback ) {
					el( 'aiwpb-rollback' ).style.display = '';
				}
			} )
			.catch( function ( err ) {
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice notice-error" style="padding:8px 12px;">' + escapeHtml( err.message ) + '</div>';
			} );
	}
	function onRollback( e ) {
		e.preventDefault();
		if ( ! lastUpdateSlug || ! window.confirm( 'Roll back to the previous version of this plugin?' ) ) {
			return;
		}
		el( 'aiwpb-install-result' ).innerHTML = escapeHtml( 'Rolling back…' );
		api( '/rollback', { method: 'POST', body: JSON.stringify( { slug: lastUpdateSlug } ) } )
			.then( function ( r ) {
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice notice-success" style="padding:8px 12px;">' + escapeHtml( r.message || 'Rolled back.' ) + '</div>';
			} )
			.catch( function ( err ) {
				el( 'aiwpb-install-result' ).innerHTML =
					'<div class="notice notice-error" style="padding:8px 12px;">' + escapeHtml( err.message ) + '</div>';
			} );
	}
	function onReset( e ) {
		if ( e ) {
			e.preventDefault();
		}
		messages = [];
		currentJob = null;
		window.clearTimeout( pollTimer );
		renderMessages();
		el( 'aiwpb-build' ).disabled = true;
		el( 'aiwpb-status' ).style.display = 'none';
		el( 'aiwpb-result' ).style.display = 'none';
		el( 'aiwpb-input' ).focus();
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( el( 'aiwpb-engine' ) && cfg.defaultEngine ) {
			el( 'aiwpb-engine' ).value = cfg.defaultEngine;
		}
		if ( ! cfg.configured && el( 'aiwpb-config-warning' ) ) {
			el( 'aiwpb-config-warning' ).style.display = 'block';
		}
		renderMessages();
		el( 'aiwpb-send' ).addEventListener( 'click', onSend );
		el( 'aiwpb-input' ).addEventListener( 'keydown', function ( ev ) {
			if ( 'Enter' === ev.key ) {
				onSend( ev );
			}
		} );
		el( 'aiwpb-build' ).addEventListener( 'click', onBuild );
		el( 'aiwpb-reset' ).addEventListener( 'click', onReset );
		el( 'aiwpb-install' ).addEventListener( 'click', onInstall );
		el( 'aiwpb-update-inplace' ).addEventListener( 'click', onUpdateInPlace );
		el( 'aiwpb-rollback' ).addEventListener( 'click', onRollback );
		el( 'aiwpb-mode-new' ).addEventListener( 'click', function () {
			setMode( 'new' );
		} );
		el( 'aiwpb-mode-update' ).addEventListener( 'click', function () {
			setMode( 'update' );
		} );
		el( 'aiwpb-input' ).focus();
	} );
} )();
