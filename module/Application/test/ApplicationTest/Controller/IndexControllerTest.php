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
    /*public function testConvertAction(array $data)
    {
        $this->dispatch('/convert', 'POST', $data);
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Application');
        $this->assertControllerName('Application\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('application');
        
        $response = json_decode($this->getResponse()->getContent(), true);
    }
    
    public function providerConvert()
    {
        return array(
            array(array('convertible' => 125))
        );
    }*/
}