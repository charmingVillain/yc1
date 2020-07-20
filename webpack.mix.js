const mix = require('laravel-mix');
const path = require('path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');

function resolve(dir) {
    return path.join(__dirname, '.', dir)
}

mix.webpackConfig({
    output: {
        publicPath: '/dist/',
        filename: '[name].js',
        chunkFilename: '[name].js?id=[chunkhash:20]'
    },
    resolve: {
        // 添加一个前缀方便引入文件
        alias: {
            '@': resolve('resources/js'),
        }
    },
    plugins: [
        // 清除老文件
        new CleanWebpackPlugin()
    ],
    externals: {
        jquery: ['$', 'window.jQuery'],
        moment: ['window.moment']
    }
}).setPublicPath('public/dist') // 设置编译的文件目录

mix.autoload({
    jquery: ['$', 'window.jQuery'],
    moment: ['window.moment']
});

mix.options({
    postCss: [require('autoprefixer')],
})
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// 分离打包
mix.extract(['vue', 'axios', 'element-ui', 'lodash']);

mix
    .js('resources/js/admin/role/index.js', 'js/admin/role/index.js')
    .js('resources/js/admin/menu/index.js', 'js/admin/menu/index.js')
    .js('resources/js/admin/user/index.js', 'js/admin/user/index.js')
    .js('resources/js/admin/test/index.js', 'js/admin/test/index.js')
    .js("resources/js/admin/goods/index.js", "js/admin/goods/index.js")
    .js("resources/js/admin/goodsCategory/index.js", "js/admin/goodsCategory/index.js")
    .js("resources/js/admin/template/index.js", "js/admin/template/index.js")
    .js('resources/js/app.js', 'js')
    .version()
