<?php
/**
 * @package     Wetterwarnungen
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2026 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 **/

namespace Bakual\Module\Wetterwarnungen\Site\Field;

use Joomla\CMS\Form\Field\SqlField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * Dateformat Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class StationField extends SqlField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	public $type = 'Station';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput(): string
	{
		$db     = $this->getDatabase();
		$prefix = $db->getPrefix();
		$tables = $db->getTableList();

		if (!in_array($prefix . 'dwd_wetter_sites', $tables))
		{
			return '<span class="alert alert-warning">' . Text::_('MOD_DWD_WETTERWARNUNGEN_WEATHERMODULE_NOT_FOUND') . '</span>';
		}

		return parent::getInput();
	}
}
