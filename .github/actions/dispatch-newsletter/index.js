(async () => {
  const core = require('@actions/core')
  const github = require('@actions/github')
  const axios = require('axios').default

  const TOKEN = core.getInput('TOKEN', { required: true })
  const URL = core.getInput('URL', { required: true })
  const HTTP_OK = 200

  const module_name = github.context.repo.repo.toLocaleLowerCase().replace(/\W/g, ' ')
  const module_code = module_name.replace(/\s/g, '_')

  const data = {
    module_name,
    module_code
  }

  const config = {
    headers: {
      Authorization: `Bearer ${TOKEN}`
    }
  }

  console.log('Sending...')
  console.log(`Module name: ${module_name}`)
  console.log(`Module code: ${module_code}`)

  axios.post(URL, data, config).then((response) => {
    if (response.status === HTTP_OK) {
      console.log('Success')
    } else {
      console.log('Failed')
      console.log(response.statusText)
      core.setFailed(error)
    }
  }).catch((error) => {
    core.setFailed(error)
  }).then(() => {
    console.log('Finish')
  })
})()
