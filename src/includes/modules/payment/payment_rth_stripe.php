<?php

/**
 * Stripe integration for modified
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @author  Jay Trees <stripe@grandels.email>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName
 */

declare(strict_types=1);

use RobinTheHood\ModifiedStdModule\Classes\Configuration;
use RobinTheHood\Stripe\Classes\{Order, Session, Constants, PaymentModule, Repository, StripeService};
use Stripe\WebhookEndpoint;

class payment_rth_stripe extends PaymentModule
{
    public const VERSION = '0.1.0';
    public const NAME    = Constants::MODULE_PAYMENT_NAME;

    /**
     * Redirect URL after click on the "Buy Button" on step 3 (checkout_confirmation.php)
     * Because we set $tmpOrders true, checkout_process.php first creates a temp Order
     *
     * @var string $form_action_url
     */
    public $form_action_url = '/rth_stripe.php?action=checkout';

    /**
     * If $tmpOrders is true, checkout_process.php creates a temp Order.
     *
     * @var bool $tmpOrders
     */
    public $tmpOrders = true;

    /**
     * // TODO: Make this configurable via the module settings
     * If $tmpOrders is true, checkout_process.php creates a temp Order with statusId $tmpStatus
     *
     * @var int $tmpStatus
     */
    public $tmpStatus = 6; // StatusId 6 is a default modified status 'pending payment'

    /**
     * Configuration keys which are automatically added/removed on
     * install/remove
     *
     * $keys is already used by the StdModule, so we need to use a different
     * variable.
     *
     * @var array
     */
    public static array $configurationKeys = [
        'API_SANDBOX_KEY',
        'API_SANDBOX_SECRET',
        'API_LIVE_KEY',
        'API_LIVE_SECRET',
        'API_LIVE_ENDPOINT_SECRET',
    ];

    public function __construct()
    {
        parent::__construct(self::NAME);
        $this->checkForUpdate(true);
        $this->addKeys(self::$configurationKeys);

        $config = new Configuration(self::NAME);

        $stripeService = StripeService::createFromConfig($config);

        if ($stripeService->hasWebhookEndpoint()) {
            $buttonText = 'Stripe Webhook entfernen';
            $this->addAction('disconnect', $buttonText);
        } else {
            $buttonText = 'Stripe Webhook hinzufügen';
            $this->addAction('connect', $buttonText);
        }
    }

    public function invokeConnect()
    {
        // TODO: Register Webhook Endpoint
        // https://stripe.com/docs/webhooks/go-live

        $config = new Configuration(self::NAME);

        $stripeService = StripeService::createFromConfig($config);

        if ($stripeService->hasWebhookEndpoint()) {
            return;
        }

        \Stripe\Stripe::setApiKey($config->apiSandboxSecret);

        $domain = HTTPS_SERVER;

        try {
            $endpoint = WebhookEndpoint::create([
                'url'            => $domain . '/rth_stripe?action=receiveHook',
                'enabled_events' => [
                    'charge.failed',
                    'charge.succeeded',
                ],
            ]);
        } catch (Exception $e) {
            $this->addMessage($e->getMessage(), self::MESSAGE_ERROR);
        }
    }

    public function install(): void
    {
        parent::install();

        /**
         * Namespaces are encoded in base64 since the backward slashes will
         * otherwise be removed before saving. The `setFunction` method
         * will decode the namespaces and forward all data.
         *
         * @see PaymentModule::setFunction()
         */
        $setFunctionField                      = self::class . '::setFunction(\'%s\',';
        $setFunctionFieldapiSandboxKey         = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxKey'));
        $setFunctionFieldapiSandboxSecret      = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxSecret'));
        $setFunctionFieldapiLiveKey            = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveKey'));
        $setFunctionFieldapiLiveSecret         = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveSecret'));
        $setFunctionFieldapiLiveEndpointSecret = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveEndPointSecret'));

        $this->addConfiguration('API_SANDBOX_KEY', '', 6, 1, $setFunctionFieldapiSandboxKey);
        $this->addConfiguration('API_SANDBOX_SECRET', '', 6, 1, $setFunctionFieldapiSandboxSecret);
        $this->addConfiguration('API_LIVE_KEY', '', 6, 1, $setFunctionFieldapiLiveKey);
        $this->addConfiguration('API_LIVE_SECRET', '', 6, 1, $setFunctionFieldapiLiveSecret);
        $this->addConfiguration('API_LIVE_ENDPOINT_SECRET', '', 6, 1, $setFunctionFieldapiLiveEndpointSecret);

        $repo = new Repository();
        $repo->createRthStripePhpSession();
    }

    public function remove(): void
    {
        parent::remove();

        foreach (self::$configurationKeys as $key) {
            $this->deleteConfiguration($key);
        }
    }

    protected function updateSteps(): int
    {
        $currentVersion = $this->getVersion();

        if (!$currentVersion) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites PaymentModule::selection()
     *
     * Displays the Stripe payment option at checkout step 2 (checkout_payment.php)
     * @link https://docs.module-loader.de/references/module-classes/concrete/payment/#selection
     *
     * @return array (SelectionArray)
     */
    public function selection(): array
    {
        $selectionArray = [
            'id'          => $this->code,
            'module'      => 'Stripe (RobinTheHood)',
            'description' => 'Zahle mit Stripe'
        ];

        return $selectionArray;
    }

    /**
     * // TODO: Because we are switching to temporary orders, this method is no longer necessary in this form and
     * // TODO: can be revised.
     *
     * {@inheritdoc}
     *
     * Overwrites PaymentModule::process_button()
     *
     * This method is called in checkout_confirmation.php to display a button next to the "Buy Now" button. At this
     * point we save the order in the session, because in the next step rth_stripe.php we no longer have easy access
     * to the order. We can make life easier for ourselves if we already save the order in the session right now.
     *
     * @link https://docs.module-loader.de/module-payment/#process_button
     */
    public function process_button(): string
    {
        // global $order;

        // $rthOrder = new Order($order);

        // $session = new Session();
        // $session->setOrder($rthOrder);

        return '';
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites PaymentModule::payment_action()
     *
     * This method is only called when checkout_process.php creates a temporary order. The method is used by
     * checkout_process.php after creating the order and before notifying the customer If we make a redirect in the
     * method, the customer will not be notified for the time being. However, the Order Status History noted that the
     * customer was notified. We need to correct the entry in Order Status History here.
     *
     * At this point we save the order in the session, because in the next step rth_stripe.php we no longer have easy
     * access to the order. We can make life easier for ourselves if we already save the order in the session right now.
     *
     * @link https://docs.module-loader.de/module-payment/#payment_action
     */
    public function payment_action(): void
    {
        // Hopefully a temporary modified order obj that modified creates for us and stores in the database.
        global $order;

        $orderId = $_SESSION['tmp_oID'] ?? 0;
        if (!$orderId) {
            trigger_error('No temporary Order created');
        }

        // We use our Order class, because so we can wrap the $orderId and a modified Order in one object. A temp
        // modified Order Object has no orderId.
        $rthOrder = new Order($orderId, $order);

        $session = new Session();
        $session->setOrder($rthOrder);

        // TODO: Correct the entry in Order Status History, see also method description.

        xtc_redirect($this->form_action_url);
    }

    public function after_process(): void
    {
        global $order;
        global $xtPrice;
        global $language;
        global $insert_id; // This must be the id of the new order

        // NOTE: After the order is created, we could double-check that the order was paid by getting Stripe's
        // NOTE: CheckoutSession and looking at the payment_status field.
        // NOTE: https://stripe.com/docs/api/checkout/sessions/object#checkout_session_object-payment_status
    }

    // private function hasWebhookEndpoint(): bool
    // {
    //     $config = new Configuration(self::NAME);

    //     try {
    //         \Stripe\Stripe::setApiKey($config->apiSandboxSecret);
    //         $endpoints = WebhookEndpoint::all();
    //     } catch (Exception $e) {
    //         return false;
    //     }

    //     if (!$endpoints['data']) {
    //         return false;
    //     }

    //     return true;
    // }
}
