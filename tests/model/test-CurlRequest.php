<?php

require_once __DIR__."/../../src/utils/CurlRequest.php";

use wpblockchainaccounts\CurlRequest;

/**
 * CurlRequestTest
 */
class CurlRequestTest extends WP_UnitTestCase {

	/**
	 * Make a test request.
	 */
	/*function testRequest() {
		$req=new CurlRequest();
		$req->setUrl("http://jsonplaceholder.typicode.com/posts");
		$req->setResultProcessing(CurlRequest::JSON);
		$req->exec();
		$this->assertEquals(sizeof($req->getResult()),100);

		$req=new CurlRequest();
		$req->setUrl("http://jsonplaceholder.typicode.com/comments");
		$req->setParam("postId",1);
		$req->setResultProcessing(CurlRequest::JSON);
		$req->exec();
		$this->assertEquals(sizeof($req->getResult()),5);

		$this->setExpectedException('Exception','Unable to parse json');
		$req=new CurlRequest();
		$req->setUrl("http://dn.se");
		$req->setResultProcessing(CurlRequest::JSON);
		$req->exec();
	}*/

	/**
	 * Test mocking.
	 */
	function testMock() {
		$f=function($p) {
			return "hello".$p["hello"];
		};

		$req=new CurlRequest();
		$req->setMockHandler($f);
		$req->setParam("hello","world");
		$res=$req->exec();
		$this->assertEquals($res,"helloworld");
	}
}