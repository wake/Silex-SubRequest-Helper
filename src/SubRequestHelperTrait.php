<?php

  namespace Silex\Application;

  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;


  /**
   * Subrequest helper trait.
   *
   * @author Wake Liu <wake.gs@gmail.com>
   */
  trait SubRequestHelperTrait {


    /**
     * Handoff with a cloned request
     *
     */
    public function handoff ($url, $options = [], $params = []) {
      return $app['helper.request.clone.master'] ($url, $options, $params);
    }


    /**
     * Fire off a clones request
     *
     */
    public function fireoff ($url, $options = [], $params = []) {
      return $app['helper.request.clone.sub'] ($url, $options, $params);
    }
  }
