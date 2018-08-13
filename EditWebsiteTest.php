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

class EditWebsiteTest extends AbstractTestCase
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

    public function testEditWebsiteButton()
    {
        $login_user = new Admin();
        $login_user->adminLogin($this);

        $this->wd->findElement(WebDriverBy::className("btn-admin"))->click();

        $login_user->navigate_past_cover_page($this);

        //Verify edit website page loads and Edit Section button is present
        $edit_section_btn = $this->wd->findElement
        (WebDriverby::cssSelector("button.main-tool-item.btn-admin.center-block"))->isDisplayed();
        $this->assertTrue($edit_section_btn);

        //Verify welcome img is present on page
        $welcome_img = $this->wd->findElement(WebDriverby::id("welcome-img-container"))->isDisplayed();
        $this->assertTrue($welcome_img);

    }
}
