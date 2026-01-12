<?php
/**
 * @package     Wetterwarnungen
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2026 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
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

	/**
	 * Load required CSS and JavaScript assets
	 *
	 * @param   Registry  $params  The configuration
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 * @since   1.0.0
	 */
	public function loadAssets(Registry $params): void
	{
		$document = Factory::getApplication()->getDocument();

		// Leaflet CSS - direkt als Stylesheet hinzufügen
		$document->addStyleSheet(Uri::root() . 'media/mod_dwd_wetterwarnungen/css/leaflet.css');

		// jQuery (falls nicht schon geladen)
		HTMLHelper::_('jquery.framework');

		// Leaflet JS - direkt als Script hinzufügen
		$document->addScript(Uri::root() . 'media/mod_dwd_wetterwarnungen/js/leaflet.js');

		// BetterWMS - direkt als Script hinzufügen
		$document->addScript(Uri::root() . 'media/mod_dwd_wetterwarnungen/js/leaflet-betterwms.js');

		// Auto-Refresh Meta-Tag hinzufügen
		if ($interval = $params->get('refresh_interval', 300))
		{
			$document->addCustomTag(
				'<meta http-equiv="refresh" content="' . $interval . '">'
			);
		}
	}

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