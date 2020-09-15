const { description } = require('../../package')

const sidebar = [
  {
    title: 'Guide',
    collapsable: false,
    children: [
      '/guide/',
      '/guide/install'
    ]
  },
  {
    title: 'Configuração',
    collapsable: false,
    children: [
      '/configuration/tab-home',
      '/configuration/tab-custom-fields',
      '/configuration/tab-discount-and-fee',
      '/configuration/tab-order-status',
      '/configuration/tab-geo-zone',
      '/configuration/tab-installment',
      '/configuration/tab-payment-methods',
      '/configuration/tab-layouts',
      '/configuration/tab-debug'
    ]
  }
];

module.exports = {
  /**
   * Ref：https://v1.vuepress.vuejs.org/config/#title
   */
  title: 'Documentação',
  /**
   * Ref：https://v1.vuepress.vuejs.org/config/#description
   */
  description: description,

  /**
   * Extra tags to be injected to the page HTML `<head>`
   *
   * ref：https://v1.vuepress.vuejs.org/config/#head
   */
  head: [
    ['meta', { name: 'theme-color', content: '#73ba40' }],
    ['meta', { name: 'apple-mobile-web-app-capable', content: 'yes' }],
    ['meta', { name: 'apple-mobile-web-app-status-bar-style', content: 'black' }]
  ],

  /**
   * Theme configuration, here is the default theme configuration for VuePress.
   *
   * ref：https://v1.vuepress.vuejs.org/theme/default-theme-config.html
   */
  themeConfig: {
    repo: 'opencart-extension/PagSeguro-Checkout-Transparente',
    repoLabel: 'Contribua!',

    // if your docs are in a different repo from your main project:
    docsRepo: 'opencart-extension/PagSeguro-Checkout-Transparente',
    // if your docs are not at the root of the repo:
    docsDir: '.',
    // if your docs are in a specific branch (defaults to 'master'):
    docsBranch: 'feature/doc',

    editLinks: true,
    editLinkText: 'Ajude-nos a melhorar esta página!',

    lastUpdated: false,
    nav: [
      {
        text: 'Guide',
        link: '/guide/',
      },
      {
        text: 'Central de Atendimento',
        link: 'https://valdeir.dev'
      },
      {
        text: 'GitHub',
        link: 'https://github.com/opencart-extension/PagSeguro-Checkout-Transparente'
      }
    ],
    sidebar: {
      '/guide/': sidebar,
      '/configuration/': sidebar
    }
  },

  /**
   * Apply plugins，ref：https://v1.vuepress.vuejs.org/zh/plugin/
   */
  plugins: {
    '@vuepress/plugin-back-to-top': {},
    '@vuepress/plugin-medium-zoom': {
      selector: 'img[src$="#zoom"]'
    }
  }
}
