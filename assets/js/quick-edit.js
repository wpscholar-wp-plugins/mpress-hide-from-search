/* global inlineEditPost */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		if ( typeof inlineEditPost === 'undefined' ) {
			return;
		}

		var wpInlineEdit = inlineEditPost.edit;

		inlineEditPost.edit = function ( id ) {
			wpInlineEdit.apply( this, arguments );

			var postId = 0;
			if ( typeof id === 'object' ) {
				postId = parseInt( this.getId( id ), 10 );
			} else {
				postId = parseInt( id, 10 );
			}

			if ( postId <= 0 ) {
				return;
			}

			var postRow    = $( '#post-' + postId );
			var editRow    = $( '#edit-' + postId );
			var columnData = postRow.find( '.column-hide_from_search span' );

			var hideFromWP      = columnData.data( 'hide-from-search-wp' );
			var hideFromEngines = columnData.data( 'hide-from-search-engines' );

			editRow.find( 'input[name="_hide_from_search_wp"]' ).prop( 'checked', 1 === hideFromWP );
			editRow.find( 'input[name="_hide_from_search_engines"]' ).prop( 'checked', 1 === hideFromEngines );
		};
	} );
} )( jQuery );
