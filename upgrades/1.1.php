<?php

global $wpdb;

$rows = $wpdb->query(
	$wpdb->prepare(
		"UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s",
		'_hide_from_search_wp',
		'_mpress_hide_from_search'
	)
);
