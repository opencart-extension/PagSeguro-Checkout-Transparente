const app = require('express')()
const fs = require('fs').promises
const host = '0.0.0.0'
const port = process.env.PORT || 3000

app.all(/.*/, (req, res) => {
    const path = req.url.replace(/\//g, '_').substr(1)
    const method = req.method.toLocaleLowerCase()

    console.log(`Acessando ${req.method} ${req.url}`)
    console.log(`./responses/${method}_${path}.json`)

    fs.readFile(`./responses/${method}_${path}.json`)
        .then((result) => {
            let data = JSON.parse(result)

            for (let header of data.headers) {
                res.setHeader(header.name, header.value)
            }

            res.status(data.status)
                .send(data.response)
        })
        .catch(() => res.status(404).send())
})

app.listen(port, host)
console.log(`Servidor iniciado em ${host}:${port}`)
