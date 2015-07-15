<?php

namespace Meebio\PhpEvalConsole\Authorizers;

abstract class Authorizer implements AuthorizerInterface
{

    public function renderError()
    {
        header('HTTP/1.0 401 Unauthorized', true, 404);
        exit(1);
    }
}
