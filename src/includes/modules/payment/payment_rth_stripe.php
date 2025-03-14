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

use RobinTheHood\Stripe\Classes\Config\StripeConfig;
use RobinTheHood\Stripe\Classes\Framework\DIContainer;
use RobinTheHood\Stripe\Classes\Framework\Order;
use RobinTheHood\Stripe\Classes\Framework\PaymentModule;
use RobinTheHood\Stripe\Classes\Repository\PaymentRepository;
use RobinTheHood\Stripe\Classes\Repository\PhpSessionRepository;
use RobinTheHood\Stripe\Classes\Routing\UrlBuilder;
use RobinTheHood\Stripe\Classes\Storage\PhpSession;
use RobinTheHood\Stripe\Classes\Service\WebhookEndpointService;

class payment_rth_stripe extends PaymentModule
{
    /** @var string */
    public const VERSION = '0.8.0';

    /** @var string */
    public const NAME = 'MODULE_PAYMENT_PAYMENT_RTH_STRIPE';

    // StatusId 1 is a default modified status 'Pending'
    public const DEFAULT_ORDER_STATUS_PENDING = 1;

    // StatusId 2 is a default modified status 'Processing'
    public const DEFAULT_ORDER_STATUS_PAID = 2;

    // StatusId for authorized payments (default to 'Pending')
    public const DEFAULT_ORDER_STATUS_AUTHORIZED = 1;

    // StatusId for captured payments (default to 'Processing')
    public const DEFAULT_ORDER_STATUS_CAPTURED = 2;

    // StatusId for canceled payments (default to 'Canceled')
    public const DEFAULT_ORDER_STATUS_CANCELED = 4;

    // StatusId for refunded payments (default to some reasonable value)
    public const DEFAULT_ORDER_STATUS_REFUNDED = 4;

    public static $actionInvoked = false;

    /**
     * Redirect URL after click on the "Buy Button" on step 3 (checkout_confirmation.php)
     * Because we set $tmpOrders true, checkout_process.php first creates a temp Order
     *
     * @var string $form_action_url RobinTheHood\Stripe\Classes\Controller\Controller::invokeCheckout()
     */
    public $form_action_url = '';

    /**
     * If $tmpOrders is true, checkout_process.php creates a temp Order.
     *
     * @var bool $tmpOrders
     */
    public $tmpOrders = true;

    /**
     * If $tmpOrders is true, checkout_process.php creates a temp Order with statusId $tmpStatus
     * This value is overwritten in the constructor.
     *
     * @var int $tmpStatus
     */
    public $tmpStatus = self::DEFAULT_ORDER_STATUS_PENDING;

    private UrlBuilder $urlBuilder;
    private StripeConfig $stripeConfig;
    private PaymentRepository $paymentRepo;
    private PhpSessionRepository $phpSessionRepo;
    private PhpSession $phpSession;
    private WebhookEndpointService $webhookEndpointService;

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
        'LIVE_MODE',
        'API_SANDBOX_KEY',
        'API_SANDBOX_SECRET',
        'API_LIVE_KEY',
        'API_LIVE_SECRET',
        'API_LIVE_ENDPOINT_SECRET',
        'CHECKOUT_TITLE',
        'CHECKOUT_DESC',
        'PAYMENT_TITLE',
        'PAYMENT_DESC',
        'ORDER_STATUS_PENDING',
        'ORDER_STATUS_PAID',
        'ORDER_STATUS_AUTHORIZED',
        'ORDER_STATUS_CAPTURED',
        'ORDER_STATUS_CANCELED',
        'ORDER_STATUS_REFUNDED',
        'MANUAL_CAPTURE',
    ];

    private DIContainer $container;

    public function __construct()
    {
        parent::__construct(self::NAME);

        $this->container = new DIContainer();
        $this->urlBuilder = $this->container->get(UrlBuilder::class);
        $this->stripeConfig = $this->container->get(StripeConfig::class);
        $this->paymentRepo = $this->container->get(PaymentRepository::class);
        $this->phpSessionRepo = $this->container->get(PhpSessionRepository::class);
        $this->phpSession = $this->container->get(PhpSession::class);
        $this->webhookEndpointService = $this->container->get(WebhookEndpointService::class);

        $this->form_action_url = $this->urlBuilder->getFormActionUrl();
        $this->tmpStatus = $this->stripeConfig->getOrderStatusPending(self::DEFAULT_ORDER_STATUS_PENDING);

        $this->checkForUpdate(true);
        $this->addKeys(self::$configurationKeys);
        $this->addActions();
    }

    private function addActions(): void
    {
        $currentPage = $_SERVER['PHP_SELF'];
        $targetPage  = 'modules.php';

        if (substr($currentPage, -strlen($targetPage)) !== $targetPage) {
            return;
        }

        $webhookEndpointStatus = $this->webhookEndpointService->getWebhookStatus();

        if (0 === $webhookEndpointStatus) {
            $buttonText = 'Stripe Webhook hinzufügen';
            $this->addAction('connectWebhook', $buttonText);
        } elseif (1 === $webhookEndpointStatus) {
            $buttonText = 'Stripe Webhook aktualisieren';
            $this->addAction('updateWebhook', $buttonText);
        }

        if (1 === $webhookEndpointStatus || 2 === $webhookEndpointStatus) {
            $buttonText = 'Stripe Webhook entfernen';
            $this->addAction('disconnectWebhook', $buttonText);
        }
    }

    /**
     * Show an icon in the module name if version is 2.0.6.0. In newer versions of modified it is new standard to not
     * display an icon anymore.
     */
    protected function getTitle(): string
    {
        if (defined('PROJECT_VERSION_NO') && in_array(PROJECT_VERSION_NO, ['2.0.6.0'])) {
            $imgSrc = DIR_WS_CATALOG . 'vendor-mmlc/robinthehood/stripe/assets/img/icon.svg';
            return parent::getTitle() . '<br><img style="width: 50px" src="' . $imgSrc . '">';
        }
        return parent::getTitle();
    }

    public function invokeConnectWebhook()
    {
        if (self::$actionInvoked) {
            return;
        }
        self::$actionInvoked = true;

        $result = $this->webhookEndpointService->connectWebhook();
        $messageType = $result['success'] ? self::MESSAGE_SUCCESS : self::MESSAGE_ERROR;
        $prefix = $result['success'] ? '' : 'Fehler: Stripe Webhook Endpoint konnte nicht hinzugefügt werden. ';
        $this->addMessage($prefix . $result['message'], $messageType);
    }

    public function invokeUpdateWebhook()
    {
        if (self::$actionInvoked) {
            return;
        }
        self::$actionInvoked = true;

        $result = $this->webhookEndpointService->updateWebhook();
        $messageType = $result['success'] ? self::MESSAGE_SUCCESS : self::MESSAGE_ERROR;
        $prefix = $result['success'] ? '' : 'Fehler: Stripe Webhook Endpoint konnte nicht aktualisiert werden. ';
        $this->addMessage($prefix . $result['message'], $messageType);
    }

    public function invokeDisconnectWebhook()
    {
        if (self::$actionInvoked) {
            return;
        }
        self::$actionInvoked = true;

        $result = $this->webhookEndpointService->disconnectWebhook();
        $messageType = $result['success'] ? self::MESSAGE_SUCCESS : self::MESSAGE_ERROR;
        $prefix = $result['success'] ? '' : 'Fehler: Stripe Webhook Endpoint konnte nicht entfernt werden. ';
        $this->addMessage($prefix . $result['message'], $messageType);
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites StdModule::install()
     */
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
        $setFunctionFieldcheckoutTitleDesc     = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::checkoutTitleDesc'));

        $this->addConfigurationSelect('LIVE_MODE', 'false', 6, 1);
        $this->addConfiguration('API_SANDBOX_KEY', '', 6, 1, $setFunctionFieldapiSandboxKey);
        $this->addConfiguration('API_SANDBOX_SECRET', '', 6, 1, $setFunctionFieldapiSandboxSecret);
        $this->addConfiguration('API_LIVE_KEY', '', 6, 1, $setFunctionFieldapiLiveKey);
        $this->addConfiguration('API_LIVE_SECRET', '', 6, 1, $setFunctionFieldapiLiveSecret);
        $this->addConfiguration('API_LIVE_ENDPOINT_SECRET', '', 6, 1, $setFunctionFieldapiLiveEndpointSecret);
        $this->addConfiguration('CHECKOUT_TITLE', 'DE::Einkauf bei SHOPNAME||EN::Purchase at SHOPNAME', 6, 1, $setFunctionFieldcheckoutTitleDesc);
        $this->addConfiguration('CHECKOUT_DESC', 'DE::Kaufbetrag der gesamten Bestellung||EN::Purchase amount of the entire order', 6, 1, $setFunctionFieldcheckoutTitleDesc);
        $this->addConfiguration('PAYMENT_TITLE', 'DE::Stripe||EN::Stripe', 6, 1, $setFunctionFieldcheckoutTitleDesc);
        $this->addConfiguration('PAYMENT_DESC', 'DE::Zahle mit Stripe||EN::Payment with Stripe', 6, 1, $setFunctionFieldcheckoutTitleDesc);
        $this->addConfigurationSelect('MANUAL_CAPTURE', 'false', 6, 1);

        $this->addConfigurationOrderStatus('ORDER_STATUS_PENDING', (string) self::DEFAULT_ORDER_STATUS_PENDING, 6, 1);
        $this->addConfigurationOrderStatus('ORDER_STATUS_PAID', (string) self::DEFAULT_ORDER_STATUS_PAID, 6, 1);
        $this->addConfigurationOrderStatus('ORDER_STATUS_AUTHORIZED', (string) self::DEFAULT_ORDER_STATUS_AUTHORIZED, 6, 1);
        $this->addConfigurationOrderStatus('ORDER_STATUS_CAPTURED', (string) self::DEFAULT_ORDER_STATUS_CAPTURED, 6, 1);
        $this->addConfigurationOrderStatus('ORDER_STATUS_CANCELED', (string) self::DEFAULT_ORDER_STATUS_CANCELED, 6, 1);
        $this->addConfigurationOrderStatus('ORDER_STATUS_REFUNDED', (string) self::DEFAULT_ORDER_STATUS_REFUNDED, 6, 1);

        $this->setAdminAccess('rth_stripe');

        $this->paymentRepo->createTable();
        $this->phpSessionRepo->createTable();
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites StdModule::remove()
     */
    public function remove(): void
    {
        parent::remove();

        // foreach (self::$configurationKeys as $key) {
        //     $this->deleteConfiguration($key);
        // }
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites StdModule::updateSteps()
     */
    protected function updateSteps(): int
    {
        $currentVersion = $this->getVersion();

        if (!$currentVersion) {
            $this->setVersion(self::VERSION);
            return self::UPDATE_SUCCESS;
        }

        if ('0.1.0' === $currentVersion) {
            $this->setVersion('0.2.0');
            return self::UPDATE_SUCCESS;
        }

        if ('0.2.0' === $currentVersion) {
            $this->addConfigurationOrderStatus('ORDER_STATUS_PENDING', (string) self::DEFAULT_ORDER_STATUS_PENDING, 6, 1);
            $this->addConfigurationOrderStatus('ORDER_STATUS_PAID', (string) self::DEFAULT_ORDER_STATUS_PAID, 6, 1);

            $this->setVersion('0.3.0');
            return self::UPDATE_SUCCESS;
        }

        if ('0.3.0' === $currentVersion) {
            $this->setVersion('0.4.0');
            return self::UPDATE_SUCCESS;
        }

        if ('0.4.0' === $currentVersion) {
            $this->setVersion('0.4.1');
            return self::UPDATE_SUCCESS;
        }

        if ('0.4.1' === $currentVersion) {
            $this->setVersion('0.5.0');
            return self::UPDATE_SUCCESS;
        }

        if ('0.5.0' === $currentVersion) {
            $this->setVersion('0.6.0');
            return self::UPDATE_SUCCESS;
        }

        if ('0.6.0' === $currentVersion) {
            $setFunctionField = self::class . '::setFunction(\'%s\',';
            $setFunctionFieldcheckoutTitleDesc = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::checkoutTitleDesc'));
            $this->addConfiguration('PAYMENT_TITLE', 'DE::Stripe||EN::Stripe', 6, 1, $setFunctionFieldcheckoutTitleDesc);
            $this->addConfiguration('PAYMENT_DESC', 'DE::Zahle mit Stripe||EN::Payment with Stripe', 6, 1, $setFunctionFieldcheckoutTitleDesc);

            $this->setVersion('0.7.0');
            return self::UPDATE_SUCCESS;
        }

        if ('0.7.0' === $currentVersion) {
            $this->setAdminAccess('rth_stripe');
            $this->addConfigurationSelect('MANUAL_CAPTURE', 'false', 6, 1);
            $this->addConfigurationOrderStatus('ORDER_STATUS_AUTHORIZED', (string) self::DEFAULT_ORDER_STATUS_AUTHORIZED, 6, 1);
            $this->addConfigurationOrderStatus('ORDER_STATUS_CAPTURED', (string) self::DEFAULT_ORDER_STATUS_CAPTURED, 6, 1);
            $this->addConfigurationOrderStatus('ORDER_STATUS_CANCELED', (string) self::DEFAULT_ORDER_STATUS_CANCELED, 6, 1);
            $this->addConfigurationOrderStatus('ORDER_STATUS_REFUNDED', (string) self::DEFAULT_ORDER_STATUS_REFUNDED, 6, 1);
            $this->setVersion('0.8.0');
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
        $selectionFieldArray = [
            'title' => '',
            'field' => xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()),
        ];

        $config = new StripeConfig('MODULE_PAYMENT_PAYMENT_RTH_STRIPE');

        $titel = parse_multi_language_value($config->getPaymentTitle(), $_SESSION['language_code']) ?: 'Stripe';
        $description = parse_multi_language_value($config->getPaymentDescription(), $_SESSION['language_code']) ?: 'Zahle mit Stripe';

        $selectionArray = [
            'id'          => $this->code,
            'module'      => $titel, // Stripe
            'description' => $description, // Zahle mit Stripe
            'fields'      => [$selectionFieldArray],
        ];

        return $selectionArray;
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites PaymentModule::pre_confirmation_check()
     *
     * //TODO: See Issue #42 - Add option to keep temporary order - for more options of cancelation
     */
    public function pre_confirmation_check(): void
    {
        $_SESSION['rth_stripe_status'] = 'start';

        $tempOrderId = $this->getTemporaryOrderId();

        if (!$this->isValidOrderId($tempOrderId)) {
            return;
        }

        $this->removeOrder($tempOrderId);
        $this->setTemporaryOrderId(false);
        xtc_redirect($this->urlBuilder->getCheckoutConfirmation());
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
        $tempOrderId = $this->getTemporaryOrderId();
        if (!$tempOrderId) {
            trigger_error('No temporary Order created');
        }

        // We use our Order class, because so we can wrap the $orderId and a modified Order in one object. A temp
        // modified Order Object has no orderId.
        $modifiedOrder = $this->getModifiedOrder();
        if (!$modifiedOrder) {
            // TODO: Handle error
            // TODO: Maybe we can redirect to shipping_card with error message
        }

        $rthOrder = new Order($tempOrderId, $modifiedOrder);

        $this->phpSession->setOrder($rthOrder);

        // TODO: Correct the entry in Order Status History, see also method description.

        // See RobinTheHood\Stripe\Classes\Controller\Controller::invokeCheckout()
        xtc_redirect($this->form_action_url);
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites PaymentModule::before_process()
     */
    public function before_process(): void
    {
        // If an error occurs on checkout_process.php, it may happen that a temporary order was created without that we
        // was already on Stripe. In this case the checkout_process should immediately redirect to Stripe again.
        if ($_SESSION['tmp_oID'] && 'success' !== $_SESSION['rth_stripe_status']) {
            xtc_redirect($this->form_action_url);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Overwrites PaymentModule::after_process()
     */
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
}
