<?php
namespace ApplicationTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include '/var/www/orba/MoneyExchanger/config/application.config.php'
        );
        parent::setUp();
    }
    
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Application');
        $this->assertControllerName('Application\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('application');
    }
    
    /**
     * @dataProvider providerConvert
     * @param array $data
     */
    public function testConvertAction(array $data)
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
 
        $this->dispatch('/convert', 'POST', $data);
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Application');
        $this->assertControllerName('Application\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('application');
        
        $content = json_decode($this->getResponse()->getContent());
        
        // check response header
        $this->assertResponseHeaderContains('Content-Type', 'application/json; charset=utf-8');
        
        // check response body
        $this->assertEquals($content->success, true);
        $this->assertObjectHasAttribute('course',$content);
        $this->assertObjectHasAttribute('from',$content->course);
        $this->assertObjectHasAttribute('to',$content->course);
        $this->assertObjectHasAttribute('value',$content->course);
    }
    
    public function providerConvert()
    {
        return array(
            array(array('convertible' => 125))
        );
    }
}