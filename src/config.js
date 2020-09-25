window.addEventListener('load', () => {
  const Main = require('./main')
  const instance = new Main()
  instance.loadWidgetHelpDesk()
  instance.loadAd()
  instance.createInputTelemtryUrl()
  instance.createInputNewsletterUrl()
})