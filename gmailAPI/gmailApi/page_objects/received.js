const Page = require('../page');
const fs = require('fs');
const gmailApiSync = require('gmail-api-sync');
const jsdom = require('jsdom');

class receivedPage extends Page {
  get pdfMetaTitleElement() {
    return $("//meta[@property='og:title']");
  }
  getEmailBySubject(subject) {
    var options = {
      query: `subject:${subject}`,
      format: 'full'
    };

    browser.waitUntil(
      () => this.getEmailByQuery(options).emails.length >= 1,
      300000,
      'Email still not received',
      30000
    );

    return this.getEmailByQuery(options);
  }

  getEmailByQuery(options) {

    let rawdata = fs.readFileSync('location of JSON file');
    let accessToken = JSON.parse(rawdata);

    gmailApiSync.setClientSecretsFile('location of JSON file');

    return browser.call(() => {
      return new Promise(function(resolve) {
        gmailApiSync.authorizeWithToken(accessToken, function(err, oauth) {
          gmailApiSync.queryMessages(oauth, options, function(err, response) {
            return resolve(JSON.parse(JSON.stringify(response)));
          });
        });
      });
    });
  }

  /**
   * Load View Email link and return pdf title
   * @return {string}
   */
  getLinkedPdfTitle(parsedMessages) {
    const dom = new jsdom.JSDOM(parsedMessages.emails[0].textHtml);
    let emailLink = dom.window.document.querySelector('a').href;

    browser.url(emailLink);

    const pdfHTML = new jsdom.JSDOM(this.pdfMetaTitleElement.getHTML());
    return pdfHTML.window.document.querySelector('meta').content;
  }

  /**
   * Load View Email link and return pdf title
   * @return {true|false}
   */
  getAllEmailLinks(parsedMessages) {
    const dom = new jsdom.JSDOM(parsedMessages.emails[0].textHtml);
    return dom.window.document.querySelectorAll('a');
  }
}
module.exports = receivedPage;
