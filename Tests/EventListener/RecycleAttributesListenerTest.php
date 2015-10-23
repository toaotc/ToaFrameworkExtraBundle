<?php

namespace Toa\Bundle\FrameworkExtraBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Toa\Bundle\FrameworkExtraBundle\EventListener\RecycleAttributesListener;

/**
 * RecycleAttributesListenerTest
 *
 * @author Enrico Thies <enrico.thies@gmail.com>
 */
class RecycleAttributesListenerTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    protected function setUp()
    {
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', array(), array(), '', false);
    }

    public function testRequestAttribute()
    {
        $request = Request::create('/');
        session_name('foo');

        $request->attributes->set('_domain', 'foo');
        $listener = new RecycleAttributesListener(array('_domain'), null, $this->requestStack);
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('foo', $request->attributes->get('_domain'));
    }

    public function testAttributeSetForRoutingContext()
    {
        // the request context is updated
        $context = $this->getMock('Symfony\Component\Routing\RequestContext');
        $context->expects($this->once())->method('setParameter')->with('_domain', 'foo');

        $router = $this->getMock('Symfony\Component\Routing\Router', array('getContext'), array(), '', false);
        $router->expects($this->once())->method('getContext')->will($this->returnValue($context));

        $request = Request::create('/');

        $request->attributes->set('_domain', 'foo');
        $listener = new RecycleAttributesListener(array('_domain'), $router, $this->requestStack);
        $listener->onKernelRequest($this->getEvent($request));
    }

    public function testRouterResetWithParentRequestOnKernelFinishRequest()
    {
        // the request context is updated
        $context = $this->getMock('Symfony\Component\Routing\RequestContext');
        $context->expects($this->once())->method('setParameter')->with('_domain', 'foo');

        $router = $this->getMock('Symfony\Component\Routing\Router', array('getContext'), array(), '', false);
        $router->expects($this->once())->method('getContext')->will($this->returnValue($context));

        $parentRequest = Request::create('/');
        $parentRequest->attributes->set('_domain', 'foo');

        $this->requestStack->expects($this->once())->method('getParentRequest')->will($this->returnValue($parentRequest));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\FinishRequestEvent', array(), array(), '', false);

        $listener = new RecycleAttributesListener(array('_domain'), $router, $this->requestStack);
        $listener->onKernelFinishRequest($event);
    }

    private function getEvent(Request $request)
    {
        return new GetResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST);
    }
}
