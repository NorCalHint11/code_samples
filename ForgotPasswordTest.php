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

class ForgotPasswordTest extends AbstractTestCase
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

    public function testForgotYourPassword()
    {

        $log_in_icon = $this->wd->findElement(WebDriverBy::cssSelector("i.si.si-login"));
        $log_in_icon->click();

        # Click underlined link to get to password reset page
        $this->waitForLinkText('Forgot your password?');
        $forgot_password_link = $this->wd->findElement(WebDriverBy::linktext('Forgot your password?'));
        $forgot_password_link->click();

        # Create array of all admin emails and select a random for each test
        $email_array = array($this->conf['email'], $this->conf['code_only_admin_user'], $this->conf['code_glm_admin_user'], $this->conf['all_access_admin_user']);
        $random_admin_email = $email_array[array_rand($email_array)];

        # Enter valid account email
        $this->wd->executeScript("document.getElementById('email').setAttribute('value', '$random_admin_email')");

        # Submit password reset request
        $submit_btn = $this->wd->findElement(WebDriverBy::className('form-submit'));
        $submit_btn->click();

        # Assert confirmation text display after request is submitted
        $this->waitForId('message_alert');
        $reset_password_confirmation = $this->wd->findElement(WebDriverBy::Id('message_alert));
        $this->assertEquals("Please check your email for a link to set your new password.", $reset_password_confirmation->getText());

    }
}
