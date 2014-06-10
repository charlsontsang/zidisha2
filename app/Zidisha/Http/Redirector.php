<?php
/**
 * Created by IntelliJ IDEA.
 * User: jonas
 * Date: 10/06/14
 * Time: 17:52
 */

namespace Zidisha\Http;


class Redirector extends \Illuminate\Routing\Redirector{

    /**
     * Create a new redirect response.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Zidisha\Http\RedirectResponse
     */
    protected function createRedirect($path, $status, $headers)
    {
        $redirect = new RedirectResponse($path, $status, $headers);

        if (isset($this->session))
        {
            $redirect->setSession($this->session);
        }

        $redirect->setRequest($this->generator->getRequest());

        return $redirect;
    }
    
} 