const LoginPage = require('fileLocation');
const MyDrivePage = require('fileLocation');
const AppPage = require('fileLocation');
const ReceivedEmailPage = require('fileLocation');

beforeEach(function() {
  let login = new LoginPage();
  login.setup();
});

  it('Send Single Contact Email', function() {
    let receivedEmail = new ReceivedEmailPage();

    let currentTime = appPages.currentTime();
    let subject = `"${currentTime} - David Test File"`;

    emailPage.sendEmailToContactName('David Hinton', subject);

    // Calls out to gmail to test if email was received
    if (browser.config.gmailApi) {
      let gmailResult = receivedEmail.getEmailBySubject(subject);
      let parsedMessages = JSON.parse(JSON.stringify(gmailResult));

      assert.include(parsedMessages.emails[0].subject, subject);
    }
  });

  it('Verify email Links', function() {
    let receivedEmail = new ReceivedEmailPage();

    let currentTime = appPages.currentTime();
    let subject = `"${currentTime} - David Test File"`;

    emailPage.sendEmailToContactName('David Hinton', subject);

    // Calls out to gmail to test if email was received
    if (browser.config.gmailApi) {
      let gmailResult = receivedEmail.getEmailBySubject(subject);
      let parsedMessages = JSON.parse(JSON.stringify(gmailResult));
      let emailLinks = receivedEmail.getAllEmailLinks(parsedMessages);
      let sendGridPartialUrl =
        'https://www.google.com';

      assert.include(emailLinks[0].href, sendGridPartialUrl);
      assert.include(emailLinks[1].href, sendGridPartialUrl);
      assert.include(emailLinks[3].href, sendGridPartialUrl);
    }
  });
});
