let mix = require('laravel-mix')

mix
    .setPublicPath('dist')
    .js('resources/js/tool.js', 'js').vue({version: 2})
    .babelConfig({
        plugins: [
            "@babel/plugin-proposal-optional-chaining"
        ]
    })
    .disableSuccessNotifications()
