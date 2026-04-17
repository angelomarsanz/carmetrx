const mix = require('laravel-mix');

const { execSync } = require('child_process');

const buildApp = process.env.BUILD_APP === 'true';
const buildInt = process.env.BUILD_INTEGRACIONES === 'true';

// 1. APP ORIGINAL
if (buildApp) {
    console.log('🏗️  Compilando App Original...');
    mix.js('resources/js/app.js', 'public/js')
       .sass('resources/sass/app.scss', 'public/css');
}

// 2. INTEGRACIONES
if (buildInt) {
    console.log('🏗️  Compilando Integraciones...');
    mix.js('packages/Reda/Integraciones/resources/js/main.js', 'public/js/integraciones.js')
       .sass('packages/Reda/Integraciones/resources/sass/main.scss', 'public/css/integraciones.css');
}
