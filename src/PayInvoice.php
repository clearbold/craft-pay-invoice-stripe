<?php
/**
 * @link      https://github.com/clearbold/craft-darksky-weather
 * @copyright Copyright (c) Clearbold, LLC
 */

namespace clearbold\payinvoice;

use clearbold\payinvoice\models\Settings;
use clearbold\payinvoice\services\PayInvoiceService;
use clearbold\payinvoice\variables\PayInvoiceVariable;

use Craft;
use craft\base\Plugin;
use clearbold\payinvoice\twig\TwigExtensions;
use craft\web\twig\variables\CraftVariable;
use yii\base\Event;

/**
 * Darksky
 *
 * @author Mark Reeves, Clearbold, LLC <hello@clearbold.com>
 * @since 0.1
 */
class PayInvoice extends Plugin
{
    public $hasCpSettings = true;
    public static $plugin;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'payinvoice' => PayInvoiceService::class,
        ]);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('payinvoice', PayInvoiceVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'payinvoice',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    protected function createSettingsModel()
    {
        return new \clearbold\payinvoice\models\Settings();
    }

    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('payinvoice/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}
