<?php

use RobinTheHood\ModifiedStdModule\Classes\StdModule;

/**
 * You can find informations about payment classes and development at:
 * https://demo.hedgedoc.org/87G7LxjhR3a4zH34xHxffQ
 */
class rth_stripe extends StdModule
{
    public function __construct()
    {
        parent::__construct('MODULE_PAYMENT_RTH_STRIPE');

        $this->checkForUpdate(true);
    }

    /**
     * Payment class method
     *
     * Description: // TODO: add description
     *
     * @return void
     */
    public function update_status(): void
    {
        global $order, $xtPrice;

        // ...

        $this->enabled = true;
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *  
     * @return void
     */
    public function pre_confirmation_check(): void
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     * 
     * @return array
     */
    public function confirmation(): array
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     * 
     * @return ??? type is unknown
     */
    public function process_button()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *
     * @return ??? type is unknown
     */
    public function before_process()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *
     * @return ??? type is unknown
     */
    public function payment_action()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     * 
     * @return ??? type is unknown
     */
    public function before_send_order()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     * 
     * @return ??? type is unknown
     */
    public function after_process()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     * 
     * @return ??? type is unknown
     */
    public function success()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *
     * @return ??? type is unknown˚
     */
    public function get_error()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *
     * @return ??? type is unknown
     */
    public function iframeAction()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *
     * @return ??? type is unknown
     */
    public function create_paypal_link()
    {
    }

    /**
     * Payment class method
     * Description: // TODO: add description
     *
     * @return ??? type is unknown
     */
    public function info()
    {
    }
}
