// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import { bunny } from 'laravel-vite-plugin/fonts';
// import tailwindcss from '@tailwindcss/vite';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//             fonts: [
//                 bunny('Instrument Sans', {
//                     weights: [400, 500, 600],
//                 }),
//             ],
//         }),
//         tailwindcss(),
//     ],
//     css: {
//         lightningcss: {
//             // Mengabaikan error/warning untuk sintaks CSS modern yang belum dikenali minifier
//             errorRecovery: true 
//         }
//     },
//     server: {
//         watch: {
//             ignored: ['**/storage/framework/views/**'],
//         },
//     },
// });

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});