require('jest-fetch-mock').enableMocks()

const Main = require('../src/main')

beforeEach(() => {
  fetchMock.doMock()
  document.body.innerHTML = ''
  document.head.innerHTML = ''
})

test('Verifica se o widget do helpdesk está sendo carregado', () => {
  const obj = new Main()
  obj.loadWidgetHelpDesk()

  const scriptTotals = document.head.querySelectorAll('script[data-key]').length

  expect(scriptTotals).toBe(1)
})

test('Verifica se o campo com a URL de newsletter está sendo criado corretamente', () => {
  document.body.innerHTML = `<form psr></form>`

  const obj = new Main()
  obj.createInputNewsletterUrl()

  expect(document.body).toMatchSnapshot()
})

test('Verifica se o campo com a URL para telemetria está sendo criado corretamente', () => {
  document.body.innerHTML = `<form psr></form>`

  const obj = new Main()
  obj.createInputTelemtryUrl()

  expect(document.body).toMatchSnapshot()
})