<?php

include '.tk/RoboFileBase.php';

class RoboFile extends RoboFileBase {

	public function directoriesStructure() {
		return array( 'assets', 'includes', 'languages' );
	}

	public function fileStructure() {
		return array( 'all-in-one-invite-codes.php', 'composer.json', 'license.txt', 'loco.xml', 'readme.txt' );
	}

	/**
	 * @return array List of relative paths from the root folder of the plugin
	 */
	public function cleanPhpDirectories() {
		return array(  'includes/resources/freemius' );
	}

	public function pluginMainFile() {
		return 'all-in-one-invite-codes';
	}

	public function pluginFreemiusId() {
		return 3322;
	}

	public function minifyImagesDirectories() {
		return array();
	}

	public function minifyAssetsDirectories() {
		return array( 'assets' );
	}

	/**
	 * @return array Pair list of sass source directory and css target directory
	 */
	public function sassSourceTarget() {
		return array( array( 'scss/source' => 'assets/css' ) );
	}

	/**
	 * @return string Relative paths from the root folder of the plugin
	 */
	public function sassLibraryDirectory() {
		return 'scss/library';
	}
}
