<?php
/*
 Copyright (C) 2015 David Négrier - THE CODING MACHINE

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace Interop\Framework\Slim;

use Acclimate\Container\CompositeContainer;
use Mouf\Utils\Cache\InMemoryCache;
use Mouf\Utils\Cache\NoCache;
use Mouf\Utils\I18n\Fine\Language\FixedLanguageDetection;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 */
class SlimFrameworkModuleTest extends \PHPUnit_Framework_TestCase {

	protected $module;

	protected function setUp() {
		$this->module = new SlimFrameworkModule();
		$compositeContainer = new CompositeContainer();
		$this->module->getContainer($compositeContainer);

		$this->module->getSlimApp()->get('/foo', function(RequestInterface $request, ResponseInterface $response, array $args) {
			$response->write('Hello, world!');
			return $response;
		});
	}


	public function testRequestNotCatched() {
		$environment = Environment::mock([
			'SCRIPT_NAME'		   => 'index.php',
			'REQUEST_URI'         => 'toto'
		]);
		$request = Request::createFromEnvironment($environment);
		$response = new Response();

		$out = function(ServerRequestInterface $request, ResponseInterface $response, $next = null) {
			return new Response(201);
		};

		$middleware = $this->module->getHttpMiddleware();
		$response = $middleware($request, $response, $out);

		$this->assertEquals(201, $response->getStatusCode());
	}

	public function testRequestCatched() {
		$environment = Environment::mock([
			'SCRIPT_NAME'		   => 'index.php',
			'REQUEST_URI'         => 'foo'
		]);
		$request = Request::createFromEnvironment($environment);
		$response = new Response();

		$out = function(ServerRequestInterface $request, ResponseInterface $response, $next = null) {
			return new Response(201);
		};

		$middleware = $this->module->getHttpMiddleware();
		$response = $middleware($request, $response, $out);

		$this->assertEquals('Hello, world!', (string) $response->getBody());
		$this->assertEquals(200, $response->getStatusCode());

	}
}
