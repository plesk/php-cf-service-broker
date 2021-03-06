<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 10/05/2015
 */

namespace Sphring\MicroWebFramework\Controller\ServiceBroker;


use Sphring\MicroWebFramework\Controller\IndexController;
use Sphring\MicroWebFramework\Model\ServiceInstance;

class Unbinding extends IndexController
{
    public function action()
    {
        $action = parent::action();
        if ($action !== null) {
            return $action;
        }
        $args = $this->getArgs();
        $instanceId = $args['instance_id'];
        $bindingId = $args['binding_id'];
        $serviceBroker = $this->getServiceBroker($_GET['service_id']);
        $em = $this->getDoctrineBoot()->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $repoBinding = $em->getRepository(\Sphring\MicroWebFramework\Model\Binding::class);
        $binding = $repoBinding->find($bindingId);
        $serviceInstance = $repoServiceInstance->find($instanceId);
        if ($binding === null || $serviceInstance === null) {
            $serviceBroker->responseGone();
            return '{}';
        }
        $returnFromMethod = $serviceBroker->unbinding($repoServiceInstance->find($instanceId), $repoBinding->find($bindingId));
        $serviceBroker->afterUnbinding($instanceId, $bindingId);
        $em->flush();
        if ($returnFromMethod !== null) {
            return $returnFromMethod;
        }
        return '{}';
    }
}
