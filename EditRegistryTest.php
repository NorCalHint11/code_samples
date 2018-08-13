<?php

namespace My\tests\admin\wedding_website; 

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Lmc\Steward\Test\AbstractTestCase;
use Facebook\WebDriver\WebDriverDimension;
use My\Steward\Admin;
use My\Steward\Setup;

/**
 * @group AdminSite
 */

class EditRegistryTest extends AbstractTestCase
{
    protected $conf;

    public function setUp()
    {
        $this->conf = parse_ini_file("conf.ini", true);
        $this->wd->manage()->window()->setSize(new WebDriverDimension(1225,996));
        $setup = new Setup();
        global $test_url;
        $test_url = $setup->setUrl($this->conf);
        $this->wd->get($test_url);
    }

    public function testAddRegistryContent()
    {
        $login_user = new Admin();
        $login_user->adminLogin($this);

        $this->wd->findElement(WebDriverBy::className("btn-admin"))->click();

        # Navigate to and past Cover page
        $login_user->navigate_past_cover_page($this);

        //Verify edit website page loads and Edit Section button is present
        $edit_section_btn = $this->wd->findElement
        (WebDriverby::cssSelector("button.main-tool-item.btn-admin.center-block"))->isDisplayed();
        $this->assertTrue($edit_section_btn);

        //Verify welcome img is present on page
        $welcome_img = $this->wd->findElement
        (WebDriverby::id("welcome-img-container"))->isDisplayed();
        $this->assertTrue($welcome_img);

        //Get a click "REGISTRY" in nav
        $this->waitForLinkText("REGISTRY");

        $registry_link = $this->wd->findElement(WebDriverBy::linkText('REGISTRY'));
        $registry_link->click();

        $this->waitForCss('div.seal-container');

        $seal_displayed = $this->waitForCss('div.seal-container')->isDisplayed();

        $this->assertTrue($seal_displayed);

        $add_content_btn = $this->wd->findElement(
            WebDriverBy::Id("add-widget")
        );

        $add_content_btn->click();

        //Add default "Personal Note" widget
        $this->waitForXpath('//div[@class="modal fade in"]');

        $this->waitForId('widget-type-selectized');
        $this->wd->findElement(WebDriverBy::id("widget-type-selectized"))->click();

        $this->waitForXpath('(//div[contains(text(), "Personal Note")])');

        $this->wd->findElement(WebDriverBy::xpath('(//div[contains(text(), "Personal Note")])'))->click();

        $this->wd->executeScript("document.getElementById('title').value='Regarding Gift Purchasing'");

        $registry_widget_text_area = $this->wd->findElement(WebDriverBy::className("note-editable"))->sendKeys('In lieu of boxed gifts we would like cash gifts');

        $save_button = $this->wd->findElement(WebDriverBy::Id('modal-widget-submit'))->click();

        $this->waitForXpath('(//div[contains(text(), "Regarding Gift Purchasing")])[1]', true);

        $title_text = $this->wd->findElement(
            WebDriverBy::xpath('(//div[contains(text(), "Regarding Gift Purchasing")])[1]')
        );

        $this->assertContains('REGARDING GIFT PURCHASING', $title_text->getText());

        // CLEAN UP
        $this->wd->navigate()->refresh();

        $this->wd->findElement(WebDriverBy::xpath('//*[@id="block-0"]/div/a/i/..'))->click();

        $this->waitForId('modal-widget-delete', true);
        
        $this->findById('modal-widget-delete')->click();

        $this->waitForCss('button.swal2-confirm.swal2-styled');

        $this->wd->findElement(WebDriverBy::cssSelector('button.swal2-confirm.swal2-styled'))->click();
    }
}
