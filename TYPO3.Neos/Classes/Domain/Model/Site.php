<?php
declare(ENCODING = 'utf-8');
namespace F3\TYPO3\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * @package TYPO3
 * @subpackage Domain
 * @version $Id$
 */

/**
 * Domain model of a site
 *
 * @package TYPO3
 * @subpackage Domain
 * @version $Id$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 * @entity
 */
class Site {

	/**
	 * Name of the site
	 * @var string
	 * @validate AlphaNumeric, StringLength(minimum = 1, maximum = 255)
	 */
	protected $name = 'Untitled Site';

	/**
	 * Roots of the site grouped by language and region (locale)
	 * @var array
	 */
	protected $siteRoots;

	/**
	 * @var \SplObjectStorage
	 */
	protected $domains;

	/**
	 * Constructs this site model
	 *
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function __construct() {
		$this->domains = new \SplObjectStorage;
	}

	/**
	 * Sets the name for this site
	 *
	 * @param string $name The site name
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the name of this site
	 *
	 * @return string The name
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the root node of this site's structure tree
	 *
	 * @param \F3\TYPO3\Domain\Model\Structure\ContentNode $siteRoot The content node acting as the root of the site
	 * @param \F3\FLOW3\Locale\Locale $locale Locale of the site's root node. If not specified, the given node is assumed to be mul-ZZ
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setSiteRootNode(\F3\TYPO3\Domain\Model\Structure\ContentNode $siteRoot, \F3\FLOW3\Locale\Locale $locale = NULL) {
		if ($locale !== NULL) {
			$this->siteRoots[$locale->getLanguage()][$locale->getRegion()] = $siteRoot;
		} else {
			$this->siteRoots['mul']['ZZ'] = $siteRoot;
		}
	}

	/**
	 * Returns the root node of this site
	 *
	 * @param \F3\TYPO3\Domain\Service\ContentContext $contentContext The current content context
	 * @return \F3\TYPO3\Domain\Model\Structure\ContentNode
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function getSiteRootNode(\F3\TYPO3\Domain\Service\ContentContext $contentContext) {
		$locale = $contentContext->getLocale();
		$language = ($locale !== NULL) ? $locale->getLanguage() : 'mul';
		$region = ($locale !== NULL) ? $locale->getRegion() : 'ZZ';

		if (isset($this->siteRoots[$language][$region])) {
			return $this->siteRoots[$language][$region];
		}
	}

	/**
	 * Adds a domain to this site
	 *
	 * @param \F3\TYPO3\Domain\Model\Configuration\Domain $domain The domain
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function addDomain(\F3\TYPO3\Domain\Model\Configuration\Domain $domain) {
		$this->domains->attach($domain);
	}

	/**
	 * Removes a domain from this site
	 *
	 * @param \F3\TYPO3\Domain\Model\Configuration\Domain $domain The domain to remove
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function removeDomain(\F3\TYPO3\Domain\Model\Configuration\Domain $domain) {
		if (!$this->domains->contains($domain)) throw new \F3\TYPO3\Domain\Exception\NoSuchDomain('Cannot remove unknown domain', 1241789218);
		$this->domains->detach($domain);
	}

	/**
	 * Returns the domains attached to this site
	 *
	 * @return \SplObjectStorage The domains which are attached to this site
	 */
	public function getDomains() {
		return clone $this->domains;
	}
}

?>