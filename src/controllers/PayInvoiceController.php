<?php
/**
 * @link      https://github.com/clearbold/craft-campaignmonitor-synch
 * @copyright Copyright (c) Clearbold, LLC
 *
 */

namespace clearbold\payinvoice\controllers;

use clearbold\payinvoice\PayInvoice;

use Craft;
use craft\web\Controller;
// use craft\elements\User;

/**
 * @author    Mark Reeves
 * @since     0.1.0
 */
class PayInvoiceController extends Controller
{

    // Protected Properties
    // =========================================================================

    protected $allowAnonymous = ['index', 'test', 'achRemove', 'achPay', 'achVerify', 'achAccount', 'pay'];

    public function actionTest()
    {
        PaymentService::test();
    }

    public function actionAchRemove()
    {
        $retVal = PayInvoice::getInstance()->payinvoice->achRemove();
        // $retVal = craft()->payment->achRemove();
        if ($retVal)
            return $this->redirect('pay/ach');
        else
            return $this->renderTemplate('pay/error');
    }

    public function actionAchPay()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $retVal = PayInvoice::getInstance()->payinvoice->achPay();
        // $retVal = craft()->payment->achPay();
        if ($retVal)
            return $this->redirectToPostedUrl();
        else
            return $this->renderTemplate('pay/error');
    }

    public function actionAchVerify()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $retVal = PayInvoice::getInstance()->payinvoice->verifyAchAccount();
        // $retVal = craft()->payment->verifyAchAccount();
        if ($retVal)
            return $this->redirectToPostedUrl();
        else
            return $this->renderTemplate('pay/error');
    }

    public function actionAchAccount()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $retVal = PayInvoice::getInstance()->payinvoice->checkAchAccount();
        // $retVal = craft()->payment->checkAchAccount();
        if ($retVal)
            return $this->redirectToPostedUrl();
        else
            return $this->renderTemplate('pay/error');
    }

    public function actionPay()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $retVal = PayInvoice::getInstance()->payinvoice->pay();

        if ($retVal)
            return $this->redirectToPostedUrl();
        else
            return $this->renderTemplate('pay/error');
    }

    public function actionIndex()
    {
        $this->renderTemplate('pay/error');
        // $this->requirePostRequest();
        // $request = Craft::$app->getRequest();

        // $listId = $request->getRequiredBodyParam('cmsynch-listId') ? Craft::$app->security->validateData($request->post('cmsynch-listId')) : null;

        // $listId = $request->getParam('cmsynch-listId');

        // $groups = json_decode($request->getParam('cmsynch-ruleGroups'));
        // $groupList = '';
        // foreach ($groups->groups as $group) {
        //     $groupList .= $group . ',';
        // }
        // unset($group);
        // $groupList = substr($groupList, 0, -1);

        // $criteria = json_decode($request->getParam('cmsynch-ruleCriteria'));
        // $since = json_decode($request->getParam('cmsynch-ruleSince'));
        // $mappings = json_decode($request->getParam('cmsynch-ruleFieldMappings'));

        // $query = User::find();
        // $queryCriteria = array();
        // foreach ($criteria as $key => $value) {
        //     $queryCriteria[$key] = "=$value";
        // }
        // if (strlen($since->since) > 0)
        //     $queryCriteria['dateUpdated'] = ">$since->since";

        // Craft::configure($query, $queryCriteria);
        // $users = $query->all();

        // $subscribers = array();
        // foreach($users as $user) {
        //     $additionalFields = array();
        //     foreach($mappings as $key => $value) {
        //         if ($value != 'EmailAddress' && $value != 'Name')
        //             $additionalFields[] = array(
        //                 'Key' => $value,
        //                 'Value' => $user->$key
        //             );
        //     }
        //     $email = $user->email;
        //     $fullName = $user->fullName;
        //     $subscribers[] = array(
        //         'EmailAddress' => $email,
        //         'Name' => $fullName,
        //         'CustomFields' => $additionalFields
        //     );
        // }
        // unset($user);

        // // Pass array of users to CM Service
        // // CM Service has a batch limit of 1,000 users per API call
        // // See https://www.campaignmonitor.com/api/subscribers/#importing-many-subscribers
        // $response = CmSynch::getInstance()->campaignmonitor->importSubscribers($listId, $subscribers);
        // if ($response['success'])
        //     Craft::$app->getSession()->setNotice($response['body']->TotalUniqueEmailsSubmitted . ' users synched');
        // else
        //     Craft::$app->getSession()->setError($response['reason']);
    }

    /**
     * Quick function to load some test email addresses.
     */
    public function actionTestMembers()
    {
        // Data generated by Mockaroo, https://mockaroo.com/
        // $data = json_decode('[
        //     {"first_name":"Sorcha","last_name":"Brandino","email":"sbrandino1@salon.com"},
        //     {"first_name":"Janeen","last_name":"Creber","email":"jcreber2@go.com"},
        //     {"first_name":"Christy","last_name":"Barkaway","email":"cbarkaway3@example.com"},
        //     {"first_name":"Darb","last_name":"Postle","email":"dpostle4@360.cn"},
        //     {"first_name":"Nealon","last_name":"Doctor","email":"ndoctor5@multiply.com"},
        //     {"first_name":"Jehu","last_name":"Burrill","email":"jburrill6@dailymail.co.uk"},
        //     {"first_name":"Jolynn","last_name":"Broske","email":"jbroske7@google.co.uk"},
        //     {"first_name":"Nelia","last_name":"Grimditch","email":"ngrimditch8@unc.edu"},
        //     {"first_name":"Clark","last_name":"Pavlenko","email":"cpavlenko9@bbc.co.uk"}
        // ]');

        // foreach ($data as $testMember) {
        //     $user = new User();
        //     $user->username = $testMember->email;
        //     $user->email = $testMember->email;
        //     $user->firstName = $testMember->first_name;
        //     $user->lastName = $testMember->last_name;

        //     Craft::$app->elements->saveElement($user);

        //     // Better way to fetch just-saved user?
        //     $user = User::find()
        //         ->email($testMember->email)
        //         ->one();

        //     Craft::$app->getUsers()->assignUserToGroups($user->id, [1]);

        //     // in case there is something wrong..
        //     $errors = $user->getErrors();
        // }

        // exit;
    }

}