let mix = require('laravel-mix');

mix
    // .setPublicPath('apps/headless/dist')
    .js('apps/headless/src/app.js', 'apps/headless/dist/app.js')

    // commented out when phpstorms file watcher is enabled for sass

    // .sass('apps/headless/src/app.scss', 'apps/headless/dist/app.css')
    // .sass('web/assets/src/cp_styles.scss', 'web/assets/dist/cp_styles.css')
    // .sass('web/assets/src/styles.scss', 'web/assets/dist/styles.css')

    // .sourceMaps()
    // .version()
    // .extract()
    // .disableSuccessNotifications()
   // .browserSync('temp2.local/client/app')
;
