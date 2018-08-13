<?php

namespace My\tests\admin; 

use Facebook\WebDriver\WebDriverBy;
use Lmc\Steward\Test\AbstractTestCase;
use Facebook\WebDriver\WebDriverDimension;
use My\Steward\Admin;
use My\Steward\Setup;

/**
 * @group AdminWeb
 */
 
class LoginTest extends AbstractTestCase
{
    protected $conf;

    public function setUp()
    {
       $this->conf = parse_ini_file("conf.ini", true);
       $this->wd->manage()->window()->setSize(new WebDriverDimension(1225,996));
       $setup = new Setup();
       $test_url = $setup->setUrl($this->conf);
       $this->wd->get($test_url);
    }

    public function testLogin()
    {
        //These are here to call the admin login class not complete yet
        $login_user = new Admin();
        $login_user->adminLogin($this);

        //Verify Dashboard text is present after login
        $editor_headline = $this->wd->findElement(WebDriverBy::tagName("h1"))->getText();
        $this->assertContains('DASHBOARD', $editor_headline);

        //Verify admin edit website button is present after login
        $edit_website_btn = $this->wd->findElement
        (WebDriverby::cssSelector("a.btn-admin"))->isDisplayed();
        $this->assertTrue($edit_website_btn);

    }

    public function testSecondAdminLogin()
    {
        //These are here to call the admin login class not complete yet
        $login_user = new Admin();
        $login_user->adminLoginCreds($this, $this->conf['second_admin'], $this->conf['password']);

        //Verify Dashboard text is present after login
        $editor_headline = $this->wd->findElement(WebDriverBy::tagName("h1"))->getText();
        $this->assertContains('DASHBOARD', $editor_headline);

        //Verify admin edit website button is present after login
        $edit_website_btn = $this->wd->findElement
        (WebDriverby::cssSelector("a.btn-admin"))->isDisplayed();
        $this->assertTrue($edit_website_btn);

    }
}
?>
