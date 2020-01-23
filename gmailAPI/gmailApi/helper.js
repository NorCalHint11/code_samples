/* global browser */
const moment = require('moment');
const uuidRegexp = require('uuid-regexp');
const { assign } = Object;

module.exports = class Helpers {
  constructor({ config, environment }) {
    assign(this, { config, environment });
  }

  findDayOfWeek(day = 'Monday', startingAt = moment.utc({ day: 1 })) {
    let weekDayToFind = moment()
      .day(day)
      .isoWeekday();
    let date = moment.utc(startingAt);
    while (date.isoWeekday() != weekDayToFind) {
      date.add(1, 'day');
    }
    return date;
  }

  findRandomDayOfWeek(
    workDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
    day = workDays[Math.floor(Math.random() * workDays.length)],
    startingAt = moment.utc({ day: 1 })
  ) {
    let weekDayToFind = moment()
      .day(day)
      .isoWeekday();
    let date = moment.utc(startingAt);
    while (date.isoWeekday() != weekDayToFind) {
      date.add(1, 'day');
    }
    return date;
  }

  /**
   * Receive a number that indicates which retry is occurring.
   * Return number of milliseconds to wait before that retry occurs.
   */
  exponentialWaitTime(whichRetry = 1) {
    return Math.pow(2, parseInt(whichRetry, 10)) * 1000;
  }

  /**
   * Try running given function, with a set number of retries.
   * Will use a utility function to wait an exponential amount of time after each failure.
   */
  resilientTry(fn = () => {}, retries = this.config.retries) {
    let err = new Error('never tried');
    let succeeded = false;
    let wait = 0;
    let numTimes = retries + 1;
    for (let i = 0; i < numTimes; i++) {
      try {
        if (wait > 0) {
          browser.pause(wait);
        }
        fn();
        succeeded = true;
        break;
      } catch (__error__) {
        err = __error__;
        wait = this.exponentialWaitTime(i + 1);
      }
    }
    if (!succeeded) {
      throw err;
    }
  }

  /**
   * Grabs the first UUID from the current URL and returns it.
   */
  idFromUrl() {
    let idFromUrl = null;
    let matches = browser.getUrl().match(uuidRegexp());
    if (matches) {
      idFromUrl = matches[0];
    }
    return idFromUrl;
  }

  /**
   * Wait until the current browser URL matches the given relative URL.
   * Takes into account the current environment URL.
   */
  waitUntilUrlStartsWith(relativeUrl) {
    browser.waitUntil(
      () =>
        browser.getUrl().startsWith(`${this.environment.url}${relativeUrl}`),
      this.config.waitforTimeout
    );
  }
};
