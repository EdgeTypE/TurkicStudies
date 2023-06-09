<?php

namespace OOUI;

/**
 * Element with named flags that can be added, removed, listed and checked.
 *
 * A flag, when set, adds a CSS class on the `$element` by combining `oo-ui-flaggedElement-` with
 * the flag name. Flags are primarily useful for styling.
 *
 * @abstract
 */
trait FlaggedElement {
	/**
	 * Flags.
	 *
	 * @var bool[]
	 * @phan-var array<string,bool>
	 */
	protected $flags = [];

	/**
	 * @var Element
	 */
	protected $flagged;

	/**
	 * @param array $config Configuration options
	 *      - string|string[] $config['flags'] Flags describing importance and functionality, e.g.
	 *          'primary', 'safe', 'progressive', or 'destructive'.
	 */
	public function initializeFlaggedElement( array $config = [] ) {
		// Properties
		$this->flagged = $config['flagged'] ?? $this;

		// Initialization
		$this->setFlags( $config['flags'] ?? null );

		$this->registerConfigCallback( function ( &$config ) {
			if ( $this->flags ) {
				$config['flags'] = $this->getFlags();
			}
		} );
	}

	/**
	 * Check if a flag is set.
	 *
	 * @param string $flag Name of flag
	 * @return bool Has flag
	 */
	public function hasFlag( $flag ) {
		return isset( $this->flags[$flag] );
	}

	/**
	 * Get the names of all flags set.
	 *
	 * @return string[] Flag names
	 */
	public function getFlags() {
		return array_keys( $this->flags );
	}

	/**
	 * Clear all flags.
	 *
	 * @return $this
	 */
	public function clearFlags() {
		$remove = [];
		$classPrefix = 'oo-ui-flaggedElement-';

		foreach ( $this->flags as $flag => $value ) {
			$remove[] = $classPrefix . $flag;
		}

		$this->flagged->removeClasses( $remove );
		$this->flags = [];

		return $this;
	}

	/**
	 * Add one or more flags.
	 *
	 * @param string|array $flags One or more flags to add, or an array keyed by flag name
	 *   containing boolean set/remove instructions.
	 * @return $this
	 */
	public function setFlags( $flags ) {
		$add = [];
		$remove = [];
		$classPrefix = 'oo-ui-flaggedElement-';

		if ( is_string( $flags ) ) {
			// Set
			if ( !isset( $this->flags[$flags] ) ) {
				$this->flags[$flags] = true;
				$add[] = $classPrefix . $flags;
			}
		} elseif ( is_array( $flags ) ) {
			foreach ( $flags as $key => $value ) {
				if ( is_numeric( $key ) ) {
					// Set
					if ( !isset( $this->flags[$value] ) ) {
						$this->flags[$value] = true;
						$add[] = $classPrefix . $value;
					}
				} else {
					if ( $value ) {
						// Set
						if ( !isset( $this->flags[$key] ) ) {
							$this->flags[$key] = true;
							$add[] = $classPrefix . $key;
						}
					} else {
						// Remove
						if ( isset( $this->flags[$key] ) ) {
							unset( $this->flags[$key] );
							$remove[] = $classPrefix . $key;
						}
					}
				}
			}
		}

		$this->flagged
			->addClasses( $add )
			->removeClasses( $remove );

		return $this;
	}

	/**
	 * @param callable $func
	 */
	abstract public function registerConfigCallback( callable $func );
}
