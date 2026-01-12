<?php
/**
 * @package     Wetterwarnungen
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2026 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

namespace Bakual\Module\Wetterwarnungen\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_dwd_wetterwarnungen
 *
 * @since  1.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Returns the layout data.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	protected function getLayoutData(): array
	{
		$data = parent::getLayoutData();

		$data['helper'] = $this->getHelperFactory()->getHelper('DwdWetterwarnungenHelper');

		if ($data['params']->get('select_station'))
		{
			$id      = $data['params']->get('station');
			$station = $data['helper']->getStation($id);
			$data['params']->set('latitude', $station['lat']);
			$data['params']->set('longitude', $station['long']);
			$data['params']->set('locationname', $station['title']);
		}

		return $data;
	}
}