module.exports = {
  module: {
    rules: [
      {
        test: /\.html$/i,
        use: ['file-loader?name=[name].[ext]', 'extract-loader', 'html-loader']
      }
    ]
  }
}
