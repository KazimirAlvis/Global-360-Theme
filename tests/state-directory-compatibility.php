<?php

require_once dirname( __DIR__ ) . '/inc/compatibility/platform-core.php';

function state_directory_expect( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
	echo "PASS: {$message}\n";
}

$states = array();
foreach ( range( 1, 50 ) as $number ) {
	$states[ sprintf( 'S%02d', $number ) ] = 'State ' . $number;
}
$states['CA'] = 'California';
$states['TX'] = 'Texas';
unset( $states['S49'], $states['S50'] );
$states['DC'] = 'District of Columbia';

$legacy_registry = new class( $states ) {
	private $states;
	public function __construct( $states ) { $this->states = $states; }
	public function all() { return $this->states; }
};

$public_states = global360_theme_public_states_from_registry( $legacy_registry );
state_directory_expect( 50 === count( $public_states ), 'legacy Core API produces exactly 50 public states' );
state_directory_expect( ! isset( $public_states['DC'] ), 'legacy Core API excludes DC from the public directory' );
state_directory_expect( isset( $public_states['CA'], $public_states['TX'] ), 'legitimate states remain available' );

$malformed_registry = new class() {
	public function all() { return null; }
};
state_directory_expect( array() === global360_theme_public_states_from_registry( $malformed_registry ), 'malformed registry data fails safely' );

echo "State directory compatibility tests passed.\n";
