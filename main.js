function loadWidgetHelpDesk() {
  const script = document.createElement('script')
  script.dataset.jsdEmbedded = true
  script.dataset.key = 'c965996b-d570-489a-87c7-855a21779a85'
  script.dataset.baseUrl = 'https://jsd-widget.atlassian.com'
  script.src = 'https://jsd-widget.atlassian.com/assets/embed.js'
  script.onload = () => {
    var event = document.createEvent("Event")
    event.initEvent("DOMContentLoaded", true, true)
    window.document.dispatchEvent(event)
  }
  document.head.appendChild(script)
}

function loadAd() {
  fetch('https://cdn.jsdelivr.net/gh/opencart-extension/PagSeguro-Checkout-Transparente@config/ad.json')
    .then((response) => response.json())
    .then((response) => {
      document.querySelectorAll('div.tab-pane').forEach((item, key) => {
        const keyArr = (key + 1) % response.length
        console.log(item, response[keyArr])
        
        item.insertAdjacentHTML('beforeEnd', response[keyArr])
      })
    })
    .catch(console.warn)
}

function createInputTelemtryUrl() {
  const input = document.createElement('input')
  input.type = 'hidden'
  input.name = 'telemetry_url'
  input.value = 'https://telemetry.valdeirsantana.com.br'
  document.querySelector('form[psr]').appendChild(input)
}

function createInputNewsletterUrl() {
  const input = document.createElement('input')
  input.type = 'hidden'
  input.name = 'newsletter_url'
  input.value = 'https://telemetry.valdeirsantana.com.br/newsletter'
  document.querySelector('form[psr]').appendChild(input)
}

(() => {
  loadWidgetHelpDesk()
  loadAd()
  createInputTelemtryUrl()
  createInputNewsletterUrl()
})()