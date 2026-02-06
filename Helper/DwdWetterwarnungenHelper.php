<?php
/**
 * @package     Wetterwarnungen
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2026 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 **/

namespace Bakual\Module\Wetterwarnungen\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

/**
 * Helper for mod_dwd_Wetterwarnungen
 *
 * @since  1.0.0
 */
class DwdWetterwarnungenHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	public function getStation($station): array
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('`title`, `lat`, `long`');
		$query->from('#__dwd_wetter_sites');
		$query->where('`id` = ' . $db->quote($station));
		$db->setQuery($query);

		$result = $db->loadAssoc();

		return $result;
	}
}