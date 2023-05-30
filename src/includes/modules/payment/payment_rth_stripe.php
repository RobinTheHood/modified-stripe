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
use RobinTheHood\Stripe\Classes\{Order, Session, Constants, PaymentModule};
use Stripe\WebhookEndpoint;

class payment_rth_stripe extends PaymentModule
{
    public const VERSION = '0.1.0';
    public const NAME    = Constants::MODULE_PAYMENT_NAME;

    /**
     * Redirect URL after click on the "Buy Button" on step 3 (checkout_confirmation.php)
     *
     * @var string $form_action_url
     */
    public $form_action_url = '/rth_stripe.php?action=checkout';

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
    ];

    public function __construct()
    {
        parent::__construct(self::NAME);
        $this->checkForUpdate(true);
        $this->addKeys(self::$configurationKeys);

        if ($this->hasWebhookEndpoint()) {
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

        if ($this->hasWebhookEndpoint()) {
            return;
        }

        $config = new Configuration(self::NAME);

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
        $setFunctionField                 = self::class . '::setFunction(\'%s\',';
        $setFunctionFieldapiSandboxKey    = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxKey'));
        $setFunctionFieldapiSandboxSecret = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxSecret'));
        $setFunctionFieldapiLiveKey       = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveKey'));
        $setFunctionFieldapiLiveSecret    = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveSecret'));

        $this->addConfiguration('API_SANDBOX_KEY', '', 6, 1, $setFunctionFieldapiSandboxKey);
        $this->addConfiguration('API_SANDBOX_SECRET', '', 6, 1, $setFunctionFieldapiSandboxSecret);
        $this->addConfiguration('API_LIVE_KEY', '', 6, 1, $setFunctionFieldapiLiveKey);
        $this->addConfiguration('API_LIVE_SECRET', '', 6, 1, $setFunctionFieldapiLiveSecret);
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
        $session = new Session();

        $order = new Order();
        $session->setOrder($order);

        // NOTE: Maybe the following code could be useful, that remains to be seen.
        // $sessionId = $session->createSessionId();
        // $hiddenInputHtml = xtc_draw_hidden_field('rth_stripe_session_id', $sessionId);
        // return $hiddenInputHtml;

        return '';
    }

    private function hasWebhookEndpoint(): bool
    {
        $config = new Configuration(self::NAME);

        try {
            \Stripe\Stripe::setApiKey($config->apiSandboxSecret);
            $endpoints = WebhookEndpoint::all();
        } catch (Exception $e) {
            return false;
        }

        if (!$endpoints['data']) {
            return false;
        }

        return true;
    }
}
