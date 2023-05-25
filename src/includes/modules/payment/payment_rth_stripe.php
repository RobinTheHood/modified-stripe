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
 */

declare(strict_types=1);

use RobinTheHood\ModifiedStdModule\Classes\StdModule;
use RobinTheHood\Stripe\Classes\Order;
use RobinTheHood\Stripe\Classes\Session;

class payment_rth_stripe extends StdModule
{
    public const VERSION = '0.1.0';
    public const NAME    = 'MODULE_PAYMENT_PAYMENT_RTH_STRIPE';

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

    /**
     * Internal helper function used in install(). This simplifies using modifieds setFunction to configure settings.
     * //NOTE: Can eventually be replaced with a new StdModule.
     *
     * @param string $function A base64 encodes string of a calllable function
     * 
     * @return string
     * 
     * @see payment_rth_stripe::install
     */
    public static function setFunction($function, $value, $option): string
    {
        return call_user_func(base64_decode($function), $value, $option);
    }

    public function __construct()
    {
        parent::__construct(self::NAME);

        $this->checkForUpdate(true);

        foreach (self::$configurationKeys as $key) {
            $this->addKey($key);
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
         * @see payment_rth_stripe::setFunction
         */
        $setFunctionField                 = self::class . '::setFunction(\'%s\',';
        $setFunctionFieldapiSandboxKey    = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxKey'));
        $setFunctionFieldapiSandboxSecret = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiSandboxSecret'));
        $setFunctionFieldapiLiveKey       = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveKey'));
        $setFunctionFieldapiLiveSecret    = sprintf($setFunctionField, base64_encode('\\RobinTheHood\\Stripe\\Classes\\Field::apiLiveSecret'));

        $this->addConfiguration('API_SANDBOX_KEY', 'pk_test_f3duw0VsAEM2TJFMtWQ90QAT', 6, 1, $setFunctionFieldapiSandboxKey);
        $this->addConfiguration('API_SANDBOX_SECRET', 'sk_test_Y17KokhC3SRYCQTLYiU5ZCD2', 6, 1, $setFunctionFieldapiSandboxSecret);
        $this->addConfiguration('API_LIVE_KEY', 'pk_f3duw0VsAEM2TJFMtWQ90QAT', 6, 1, $setFunctionFieldapiLiveKey);
        $this->addConfiguration('API_LIVE_SECRET', 'sk_Y17KokhC3SRYCQTLYiU5ZCD2', 6, 1, $setFunctionFieldapiLiveSecret);
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
     * Displays the Stripe payment option at checkout step 2 (checkout_payment.php)
     * @link https://docs.module-loader.de/references/module-classes/concrete/payment/#selection
     * 
     * @return array (SelectionArray)
     */
    public function selection(): array
    {
        $selectionArray = [
            'id' => $this->code,
            'module' => 'Stripe (RobinTheHood)',
            'description' => 'Zahle mit Stripe'
        ];

        return $selectionArray;
    }

    /**
     * This method is called in checkout_confirmation.php to display a button next to the "Buy Now" button. At this
     * point we save the order in the session, because in the next step rth_stripe.php we no longer have easy access
     * to the order. We can make life easier for ourselves if we already save the order in the session right now.
     */
    public function process_button()
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
}
