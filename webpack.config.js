const path = require('path');

module.exports = {
  entry: ['./src/main.js', './src/config.js'],
  mode: 'production',
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, 'lib'),
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/
      }
    ]
  }
};