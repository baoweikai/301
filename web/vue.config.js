const path = require('path')
const webpack = require('webpack')

function resolve (dir) {
  return path.join(__dirname, dir)
}

// vue.config.js
module.exports = {
  /*
    Vue-cli3:
    Crashed when using Webpack `import()` #2463
    https://github.com/vuejs/vue-cli/issues/2463

   */
  /*
  pages: {
    index: {
      entry: 'src/main.js',
      chunks: ['chunk-vendors', 'chunk-common', 'index']
    }
  },
  */
  configureWebpack: {
    plugins: [
      // Ignore all locale files of moment.js
      new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
    ] },

  chainWebpack: (config) => {
    config.resolve.alias
      .set('@assets', resolve('assets'))
      .set('@components', resolve('components'))
      .set('@store', resolve('store'))
      .set('@utils', resolve('utils'))
      .set('@config', resolve('config'))
      .set('@lang', resolve('lang'))
      .set('@views', resolve('views'))

    const svgRule = config.module.rule('svg')
    svgRule.uses.clear()
    svgRule
      .oneOf('inline')
      .resourceQuery(/inline/)
      .use('vue-svg-icon-loader')
      .loader('vue-svg-icon-loader')
      .end()
      .end()
      .oneOf('external')
      .use('file-loader')
      .loader('file-loader')
      .options({
        name: 'assets/[name].[hash:8].[ext]'
      })
    /* svgRule.oneOf('inline')
      .resourceQuery(/inline/)
      .use('vue-svg-loader')
      .loader('vue-svg-loader')
      .end()
      .end()
      .oneOf('external')
      .use('file-loader')
      .loader('file-loader')
      .options({
        name: 'assets/[name].[hash:8].[ext]'
      })
    */
  },

  css: {
    loaderOptions: {
      less: {
        modifyVars: {
          /* less 变量覆盖，用于自定义 Adv Admin 主题 */

          'primary-color': '#F5222D',
          'link-color': '#F5222D',
          'border-radius-base': '4px'
        },
        javascriptEnabled: true
      }
    }
  },

  devServer: {
    // development server port 8000
    port: 8000,
    proxy: {
      '/api': {
        // target: 'https://mock.ihx.me/mock/5baf3052f7da7e07e04a5116/antd-pro',
        target: 'http://admin.301.com/api/',
        ws: true,
        changeOrigin: true,
        pathRewrite: {
          '/api': '' // '/'这里理解成用‘/api’代替target里面的地址，后面组件中我们掉接口时直接用api代替。比如我要调用'http://api.douban.com/v2/movie/top250'，直接写‘/api/v2/movie/top250’即可
        }
      },
      '/image': {
        // target: 'https://mock.ihx.me/mock/5baf3052f7da7e07e04a5116/antd-pro',
        target: 'http://admin.301.com/uploads/',
        ws: true,
        changeOrigin: true,
        pathRewrite: {
          '/image': '' // '/'这里理解成用‘/api’代替target里面的地址，后面组件中我们掉接口时直接用api代替。比如我要调用'http://api.douban.com/v2/movie/top250'，直接写‘/api/v2/movie/top250’即可
        }
      }
    }
  },

  // disable source map in production
  productionSourceMap: false,
  lintOnSave: undefined,
  // babel-loader no-ignore node_modules/*
  transpileDependencies: []
}
