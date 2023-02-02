<?php

/**
 * @author Ilja Neumann <ineumann@owncloud.com>
 *
 * @copyright Copyright (c) 2017, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Guests\Storage;

/**
 * read only mask for home storages
 */
class ReadOnlyJail extends DirMask {

	/**
	 * @param $path
	 * @return bool
	 */
	protected function checkPath($path): bool {
		if ($path === 'files') {
			return true;
		}
		
		if (preg_match('/^files\/Talk\/[a-zA-Z0-9äöüAÖÜß!\h\-\_\(\)\@\.]*\.[a-zA-Z0-9]*$/i', $path)) {
			return false;
		}

		return parent::checkPath($path);
	}
	
	/**
	 * @param string $path
	 * @return bool
	 */
	public function isCreatable($path) {
		if ($path === 'files/Talk') {
			return true;
		}

		return $this->getWrapperStorage()->isCreatable($path);
	}


	/**
	 * @param string $path
	 * @return bool
	 */
	public function isDeletable($path): bool {
		if (pathinfo($path, PATHINFO_EXTENSION) === 'part') {
			return true;
		}

		return $this->getWrapperStorage()->isDeletable($path);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function mkdir($path): bool {
		// Lift restrictions if files dir is created (at first login)
		if ($path === 'files') {
			return $this->storage->mkdir($path);
		} else {
			return parent::mkdir($path);
		}
	}
}
