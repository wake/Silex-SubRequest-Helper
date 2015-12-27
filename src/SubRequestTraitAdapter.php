<?php

  namespace Silex\Application;

  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;


  /**
   * Subrequest trait.
   *
   * @author Wake Liu <wake.gs@gmail.com>
   */
  trait SubRequestTraitAdapter {


    /**
     * Handoff to subrequest
     *
     */
    public function handoff ($url, $option = [], $params = []) {

      $option += [
        'request' => '',
        'method'  => ''
        ];

      $params += [
        'query'      => [],
        'request'    => [],
        'attributes' => [],
        'cookies'    => [],
        'files'      => [],
        'server'     => [],
        'content'    => null,
        ];

      if ($option['request'] == '')
        $option['request'] = $this['request'];

      $request = $option['request'];

      if ($option['method'] == '')
        $option['method'] = $request->getMethod ();

      $params['query']   += $request->query->all ();
      $params['request'] += $request->request->all ();
      $params['cookies'] += $request->cookies->all ();
      $params['files']   += $request->files->all ();
      $params['server']  += $request->server->all ();

      $subRequest = Request::create (
        $url,
        $option['method'],
        [],
        $params['cookies'],
        $params['files'],
        $params['server']
        );

      if ($request->getSession ())
        $subRequest->setSession ($request->getSession ());

      foreach (['query', 'request', 'attributes'] as $p) {
        foreach ($params[$p] as $k => $v)
          $subRequest->$p->set ($k, $v);
      }

      return $this->handle ($subRequest, HttpKernelInterface::MASTER_REQUEST, false);
    }


    /**
     * Fire off a subrequest
     *
     */
    public function fireoff ($url, $option = [], $params = []) {

      $option += [
        'request' => '',
        'method'  => 'GET'
        ];

      $params += [
        'query'      => [],
        'request'    => [],
        'attributes' => [],
        'cookies'    => [],
        'files'      => [],
        'server'     => [],
        'content'    => null,
        ];

      if ($option['request'] == '')
        $option['request'] = $this['request'];

      $request = $option['request'];

      $params['query']   += $request->query->all ();
      $params['request'] += $request->request->all ();
      $params['cookies'] += $request->cookies->all ();
      $params['files']   += $request->files->all ();
      $params['server']  += $request->server->all ();

      $subRequest = Request::create (
        $url,
        $option['method'],
        [],
        $params['cookies'],
        $params['files'],
        $params['server']
        );

      if ($request->getSession ())
        $subRequest->setSession ($request->getSession ());

      foreach (['query', 'request', 'attributes'] as $p) {
        foreach ($params[$p] as $k => $v)
          $subRequest->$p->set ($k, $v);
      }

      return $this->handle ($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
  }
