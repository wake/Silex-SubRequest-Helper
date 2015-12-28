<?php

  namespace Silex\Provider;

  use Silex\Application;
  use Silex\ServiceProviderInterface;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\HttpKernelInterface;


  /**
   * Subrequest helper provider.
   *
   * @author Wake Liu <wake.gs@gmail.com>
   */
  class SubRequestHelperProvider implements ServiceProviderInterface {


    /**
     *
     * @param  Application $app
     */
    public function register (Application $app) {


      /**
       *
       *
       */
      $app['helper.request.clone'] = $app->protect (function ($url, $options = [], $params = []) use ($app) {

        $options += [
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

        if ($options['request'] == '')
          $options['request'] = $app['request'];

        $request = $options['request'];

        if ($options['method'] == '')
          $options['method'] = $request->getMethod ();

        $params['query']   += $request->query->all ();
        $params['request'] += $request->request->all ();
        $params['cookies'] += $request->cookies->all ();
        $params['files']   += $request->files->all ();
        $params['server']  += $request->server->all ();

        $subRequest = Request::create (
          $url,
          $options['method'],
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

        return $subRequest;
      });


      /**
       *
       *
       */
      $app['helper.request.clone.master'] = $app->protect (function ($url, $options = [], $params = []) use ($app) {
        return $app->handle ($app['helper.request.clone'] ($url, $options, $params), HttpKernelInterface::MASTER_REQUEST, false);
      });


      /**
       *
       *
       */
      $app['helper.request.clone.sub'] = $app->protect (function ($url, $options = [], $params = []) use ($app) {
        return $app->handle ($app['helper.request.clone'] ($url, $options, $params), HttpKernelInterface::SUB_REQUEST, false);
      });

    }


    /**
     *
     * @param  Application $app
     */
    function boot (Application $app) {
    }
  }
