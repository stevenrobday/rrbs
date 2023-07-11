<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthorizeAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->set('path', $request->uri->getPath());
            return redirect()
                ->to('/signIn');
        }

        if ($session->level !== 'Admin' || !$session->verified) die();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}
