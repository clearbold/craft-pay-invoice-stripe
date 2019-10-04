<?php
/**
 * @link      https://github.com/clearbold/craft-campaignmonitor-lists
 * @copyright Copyright (c) Clearbold, LLC
 */

namespace clearbold\payinvoice\variables;
use clearbold\payinvoice\PayInvoice;
use Craft;

/**
 * @author    Mark Reeves
 * @since     0.1
 */
class PayInvoiceVariable
{
    /**
     * @var string
     */
    private $stripeJsUrl;
    /**
     * @var string
     */
    private $stripePublishableKey;

    public function stripeJsUrl()
    {
        $settings = PayInvoice::$plugin->getSettings();
        return $settings->getStripeJsUrl();
    }

    public function stripePublishableKey()
    {
        $settings = PayInvoice::$plugin->getSettings();
        return $settings->getStripePublishableKey();
    }

    public function achVerified()
    {
        // Look up whether current logged-in user has a verified flag set
        $member = \Craft::$app->getUser()->getIdentity();
        try {
            if ( strlen($member->getFieldValue('memberStripeBankAccountLast4')) > 0 )
                return true;
            else
                return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function cardMonths()
    {
        $monthsArray = array();
        for ($i = 1; $i <= 12; $i++)
        {
            $dateObj   = \DateTime::createFromFormat('!m', $i);
            $monthNum = date('m', mktime(0, 0, 0, $i, 10));
            $monthName = date('F', mktime(0, 0, 0, $i, 10));
            $currentMonth = false;
            if (date('m') == $monthNum)
                $currentMonth = true;
            $monthsArray[] = array(
                'monthNum' => (string)$monthNum,
                'monthName' => $monthName,
                'currentMonth' => $currentMonth
            );
        }
        return $monthsArray;
    }

    public function cardYears()
    {
        $startYear = date("Y");
        $yearsArray = array();
        for ($i = 0; $i < 20; $i++)
        {
            $yearsArray[] = $startYear + $i;
        }
        return $yearsArray;
    }
}
