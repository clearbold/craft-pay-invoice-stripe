<?php
/**
 * @author Mark Reeves, Clearbold, LLC <hello@clearbold.com>
 * @since 1.0
 */

namespace clearbold\payinvoice\services;

use Craft;
use craft\base\Component;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use clearbold\payinvoice\PayInvoice;
use clearbold\payinvoice\models\PayInvoice as PayInvoiceModel;
// use clearbold\payinvoice\records\PayInvoice;
use yii\base\Event;

/**
 * PayInvoiceService
 */
class PayInvoiceService extends Component
{

    // public function __construct()
    // {
    //     \Stripe\Stripe::setApiKey(craft()->config->get('stripeSecretKey', 'payment'));
    // }

    /**
     * @var string
     */
    private $stripePrivateKey;
    // private $memberStripeCustomerId = '';
    // private $memberStripeBankAccountId = '';

    private $member;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $settings = PayInvoice::$plugin->getSettings();
        $this->stripePrivateKey = $settings->getStripePrivateKey();
        \Stripe\Stripe::setApiKey($this->stripePrivateKey);

        // These aren't required for a credit card payment
        // $this->member = \Craft::$app->getUser()->getIdentity();
        // $this->memberStripeCustomerId = $this->member->getFieldValue('memberStripeCustomerId');
        // $this->memberStripeBankAccountId = $this->member->getFieldValue('memberStripeBankAccountId');
    }

    public function test()
    {

    }

    public function achRemove()
    {
        $member = \Craft::$app->getUser()->getIdentity();
        $memberStripeCustomerId = $member->getFieldValue('memberStripeCustomerId');
        $memberStripeBankAccountId = $member->getFieldValue('memberStripeBankAccountId');

        try {
            $customer = \Stripe\Customer::retrieve($memberStripeCustomerId);
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch(Exception $e) {
            return false;
        }

        if ( strlen($memberStripeBankAccountId) )
        {
            try {
                $customer->sources->retrieve($memberStripeBankAccountId)->delete();
            } catch (\Stripe\Error\Base $e) {
                return false;
            } catch(Exception $e) {
                return false;
            }
            $member->setFieldValue('memberStripeBankAccountId', '');
            $member->setFieldValue('memberStripeBankAccountLast4', '');
        }

        Craft::$app->elements->saveElement($member);

        return true;
    }

    public function checkAchAccount()
    {
        $member = \Craft::$app->getUser()->getIdentity();

        try {
            $customer = \Stripe\Customer::create(array(
                "source" => $_POST['stripeToken'],
                "description" => $_POST['accountname']
            ));
        } catch(\Stripe\Error\Card $e) {
            return false;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch(Exception $e) {
            return false;
        }

        $member->setFieldValue('memberStripeCustomerId', $customer->id);
        foreach ($customer->sources->data as $source)
        {
            if ($source['object'] == 'bank_account')
            {
                $member->setFieldValue('memberStripeBankAccountId', $source['id']);
            }
        }

        Craft::$app->elements->saveElement($member);

        return true;
    }

    public function verifyAchAccount()
    {
        $member = \Craft::$app->getUser()->getIdentity();
        $memberStripeCustomerId = $member->getFieldValue('memberStripeCustomerId');
        $memberStripeBankAccountId = $member->getFieldValue('memberStripeBankAccountId');

        try {
            $customer = \Stripe\Customer::retrieve($memberStripeCustomerId);
        } catch(\Stripe\Error\Card $e) {
            return false;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch(Exception $e) {
            return false;
        }

        if ( isset($customer)
            && isset($memberStripeBankAccountId)
            && strlen($memberStripeBankAccountId) )
            try {
                $bank_account = $customer->sources->retrieve($memberStripeBankAccountId);
            } catch (\Stripe\Error\Base $e) {
                return false;
            } catch(Exception $e) {
                return false;
            }
        else
            return false;

        if ($bank_account->status == 'verified')
        {
            // $member = craft()->userSession->getUser();
            // $member->getContent()->memberStripeBankAccountLast4 = $bank_account->last4;
            $member->setFieldValue('memberStripeBankAccountLast4', $bank_account->last4);
            Craft::$app->elements->saveElement($member);
        }
        else
            try {
                $bank_account->verify(array('amounts' => array(
                    str_replace('.','',$_POST['deposit1']),
                    str_replace('.','',$_POST['deposit2']),
                )));
                $member->setFieldValue('memberStripeBankAccountLast4', $bank_account->last4);
                Craft::$app->elements->saveElement($member);
            } catch(\Stripe\Error\Card $e) {
                return false;
            } catch (\Stripe\Error\Base $e) {
                return false;
            } catch(Exception $e) {
                return false;
            }

        return true;
    }

    public function achPay()
    {
        $member = \Craft::$app->getUser()->getIdentity();
        $memberStripeCustomerId = $member->getFieldValue('memberStripeCustomerId');
        $memberStripeBankAccountId = $member->getFieldValue('memberStripeBankAccountId');

        try {
            $customer = \Stripe\Customer::retrieve($memberStripeCustomerId);
        } catch(\Stripe\Error\Card $e) {
            return false;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch(Exception $e) {
            return false;
        }

        $email = $_POST['email'];
        $fullname = $_POST['fullname'];
        $invoice = $_POST['invoice'];

        try {
            \Stripe\Charge::create(array(
                "amount" => ($_POST['amount']*100),
                "currency" => "usd",
                "customer" => $customer->id,
                "source" => $memberStripeBankAccountId,
                "description" => "Charge for $fullname. ($email, invoice # $invoice)",
                "receipt_email" => $email,
                "metadata" => array("Invoice" => $_POST['invoice']),
                "statement_descriptor" => "MORINS.COM"
            ));
        } catch(\Stripe\Error\Card $e) {
            return false;
        } catch (\Stripe\Error\Base $e) {
            return false;
        } catch(Exception $e) {
            return false;
        }

        return true;
    }

    public function pay()
    {

        $email = $_POST['email'];
        $fullname = $_POST['fullname'];
        $invoice = $_POST['invoice'];

        // $markup = ceil($_POST['amount']*100*.03);
        $amount = $_POST['amount'];
        // $total = $amount*100 + $markup;
        $total = $amount*100;
        // echo $amount . '/' . $total . '/'; echo $markup; echo '/' . $total/100; exit;

        try {
            $response = \Stripe\Charge::create(array(
                "amount" => $total,
                "currency" => "usd",
                "source" => $_POST['stripeToken'], // obtained with Stripe.js
                "description" => "Charge for $fullname. ($email, invoice # $invoice)",
                "receipt_email" => $email,
                "metadata" => array("Invoice" => $_POST['invoice']),
                "statement_descriptor" => "MORINS.COM"
            ));
        } catch(\Stripe\Error\Card $e) {
            return false;
            var_dump($e); exit;
        } catch (\Stripe\Error\Base $e) {
            return false;
            var_dump($e); exit;
        } catch(Exception $e) {
            return false;
            var_dump($e); exit;
        }

        return true;
    }

}