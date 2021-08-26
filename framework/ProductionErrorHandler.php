<?php

namespace Framework;

class ProductionErrorHandler
{
    /**
     * @param mixed $logger
     */
    public function setLogger($logger): void
    {
        $this->logger = $logger;
    }

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function register()
    {
        // to catch general error like include/require;
        set_error_handler('abort');

        // to catch Fatal Error that can not be handled by set_error_handler; e.g. no semicolon
//        register_shutdown_function('abort);

        set_exception_handler('contactYourAdministrator');
    }
}
