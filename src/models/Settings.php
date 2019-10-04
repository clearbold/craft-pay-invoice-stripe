<?php
/**
 * @link      https://github.com/clearbold/craft-pay-invoice-stripe
 * @copyright Copyright (c) Clearbold, LLC
 *
 * ...
 *
 */

namespace clearbold\payinvoice\models;

use clearbold\payinvoice\PayInvoice;

use Craft;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

/**
 * @author    Mark Reeves, Clearbold, LLC <hello@clearbold.com>
 * @since     0.2.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $stripeJsUrl = null;
    /**
     * @var string
     */
    public $stripePublishableKey = null;
    /**
     * @var string
     */
    public $stripePrivateKey = null;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => [
                    'stripeJsUrl',
                    'stripePublishableKey',
                    'stripePrivateKey',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stripeJsUrl'], 'string'],
            [['stripeJsUrl'], 'required'],
            [['stripePublishableKey'], 'string'],
            [['stripePublishableKey'], 'required'],
            [['stripePrivateKey'], 'string'],
            [['stripePrivateKey'], 'required'],
        ];
    }

    /**
     * Retrieve parsed API Key
     *
     * @return string
     */
    public function getStripeJsUrl(): string
    {
        return Craft::parseEnv($this->stripeJsUrl);
    }

    /**
     * Retrieve parse Client Id
     *
     * @return string
     */
    public function getStripePublishableKey(): string
    {
        return Craft::parseEnv($this->stripePublishableKey);
    }

    /**
     * Retrieve parse Client Id
     *
     * @return string
     */
    public function getStripePrivateKey(): string
    {
        return Craft::parseEnv($this->stripePrivateKey);
    }
}