function loadWidgetHelpDesk() {
  const script = document.createElement('script')
  script.dataset.JsdEmbedded = true
  script.dataset.key = 'c965996b-d570-489a-87c7-855a21779a85'
  script.dataset.baseUrl = 'https://jsd-widget.atlassian.com'
  script.src = 'https://jsd-widget.atlassian.com/assets/embed.js'
  document.head.appendChild(script)
}

function loadAd() {
  fetch('/ad.json')
    .then((response) => response.json())
    .then((response) => {
      document.querySelectorAll('div.tab-pane').forEach((item, key) => {
        const keyArr = (key + 1) % arr.length
        
        item.insertAdjacentHTML('beforeEnd', response[keyArr])
      })
    })
    .catch(() => {})
}

(() => {
  loadWidgetHelpDesk()
  loadAd()
})