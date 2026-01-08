<?php
/**
 * @package     Wetterwarnungen
 * @author      Thomas Hunziker <admin@bakual.net>
 * @copyright   © 2026 - Thomas Hunziker
 * @license     https://www.gnu.org/licenses/gpl.html
 **/

use Bakual\Module\Wetterwarnungen\Site\Helper\DwdWetterwarnungenHelper;

defined('_JEXEC') or die;

/**
 * @var DwdWetterwarnungenHelper  $helper
 * @var \Joomla\Registry\Registry $params
 * @var stdClass                  $module
 **/

// Variablen direkt aus dem Dispatcher holen
$moduleId = 'dwd_wetterwarnungen_' . $module->id;

$helper->loadAssets($params);

// Escaping für JavaScript-Variablen
$jsLocationname = htmlspecialchars($params->get('locationname', 'Wächtersbach-Neudorf'), ENT_QUOTES, 'UTF-8');
$jsLocationname = str_replace("'", "\\'", $jsLocationname);
?>

<div class="mod-dwdwarn">
	<!-- Kartencontainer -->
	<div
			id="<?php echo $moduleId; ?>"
			style="width: <?php echo htmlspecialchars($params->get('width', '100%')); ?>;
					max-width: <?php echo htmlspecialchars($params->get('maxwidth', '900px')); ?>;
					height: <?php echo htmlspecialchars($params->get('height', '600px')); ?>;
					border: <?php echo (int) $params->get('borderwidth', 3); ?>px solid <?php echo htmlspecialchars($params->get('bordercolor')); ?>;"
	></div>
</div>

<script>
    (function () {
        'use strict';

        // Warten bis DOM und alle Scripts geladen sind
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap);
        } else {
            initMap();
        }

        function initMap() {
            // Prüfen ob Leaflet verfügbar ist
            if (typeof L === 'undefined') {
                console.error('Leaflet library not loaded');
                return;
            }

            // Konstanten und Variablen initialisieren
            const lat = <?php echo (float) $params->get('latitude', 50.264024); ?>;
            const lon = <?php echo (float) $params->get('longitude', 9.319105); ?>;
            const zoomf = <?php echo (int) $params->get('zoom', 10); ?>;
            const maxzoomf = <?php echo (int) $params->get('maxzoom', 18); ?>;
            const ortsname = '<?php echo $jsLocationname; ?>';
            const moduleId = '<?php echo $moduleId; ?>';

            // Leaflet-Kartenobjekt im referenzierten div erstellen
            const karte = L.map(moduleId, {
                center: [lat, lon],
                zoom: zoomf,
                zoomControl: <?php echo $params->get('enable_zoom_control', 1) ? 'true' : 'false'; ?>,
                dragging: <?php echo $params->get('enable_dragging', 1) ? 'true' : 'false'; ?>,
                attributionControl: true
            });

            // OSM-Hintergrundslayer definieren
            const osmlayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data: &copy; <a href="https://openstreetmap.org" target="_blank">OpenStreetMap</a> contributors',
                maxZoom: maxzoomf
            });

            // Warnungs-Layer vom DWD-Geoserver
            const warnlayer = L.tileLayer.betterWms("https://maps.dwd.de/geoserver/dwd/wms/", {
                layers: 'Warnungen_Gemeinden_vereinigt',
                format: 'image/png',
                transparent: true,
                opacity: <?php echo (float) $params->get('opacity_warnings', 0.6); ?>,
                attribution: 'Warndaten: &copy; <a href="https://www.dwd.de" target="_blank">DWD</a>'
            });

            // Layer mit neutraler Darstellung der Gemeinde-Warngebiete
            const gemeindelayer = L.tileLayer.wms("https://maps.dwd.de/geoserver/dwd/wms/", {
                layers: 'Warngebiete_Gemeinden',
                format: 'image/png',
                transparent: true,
                opacity: <?php echo (float) $params->get('opacity_communities', 0.4); ?>,
                attribution: 'Geobasisdaten Gemeinden: &copy; <a href="https://www.bkg.bund.de" target="_blank">BKG</a> 2015 (Daten verändert)'
            });

            // Layerlisten für die Layercontrol erstellen
            const baseLayers = {
                "OpenStreetMap": osmlayer.addTo(karte)
            };

            const overLayers = {};

			<?php if ($params->get('show_warnings', 1)): ?>
            overLayers["<span title='DWD Geoserver Warngebiete'>Warngebiete einblenden</span>"] = warnlayer.addTo(karte);
			<?php else: ?>
            overLayers["<span title='DWD Geoserver Warngebiete'>Warngebiete einblenden</span>"] = warnlayer;
			<?php endif; ?>

			<?php if ($params->get('show_communities', 0)): ?>
            overLayers["<span title='DWD Geoserver Gemeindegrenzen'>Gemeindegrenzen einblenden</span>"] = gemeindelayer.addTo(karte);
			<?php else: ?>
            overLayers["<span title='DWD Geoserver Gemeindegrenzen'>Gemeindegrenzen einblenden</span>"] = gemeindelayer;
			<?php endif; ?>

            // Layercontrol-Element erstellen und hinzufügen
            if (Object.keys(overLayers).length > 0) {
                L.control.layers(baseLayers, overLayers).addTo(karte);
            }

            // Marker mit Popup hinzufügen
			<?php if ($params->get('show_marker', 1)): ?>
            const marker = L.marker([lat, lon]).addTo(karte);
            const popuptext = '<b>' + ortsname + '</b><br>Wetterwarnungen<br>Deutscher Wetterdienst';
            marker.bindPopup(popuptext).openPopup();
			<?php endif; ?>
        }
    })();
</script>