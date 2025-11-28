import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Ensure topbar is only initialized on the client side (in browser)
if (typeof window !== 'undefined') {
  import('topbar').then((topbar) => {
    topbar.config({
      barThickness: 4,
      barColors: {
        '0': '#ef4137',
        '1.0': '#ff5c5c',
      },
      shadowBlur: 25,
      shadowColor: 'rgba(255, 92, 92, 0.6)',
      transitionSpeed: 700,
      html: `
        <div class="progress-bar-electronics">
          <div class="electric-flow"></div>
        </div>
      `,
    });

    // Inertia event listeners for the progress bar
    Inertia.on('start', () => {
      topbar.show();
    });

    Inertia.on('finish', (event) => {
      if (event.detail.visit.completed) {
        topbar.hide();
      } else if (event.detail.visit.interrupted) {
        // Handle interruption if needed
      } else if (event.detail.visit.cancelled) {
        topbar.hide();
      }
    });
  });
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
  createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
      resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ App, props, plugin }) {
      return createSSRApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, {
          ...page.props.ziggy,
          location: new URL(page.props.ziggy.location),
        });
    },
    progress: false,
  })
);

// Hide loader once page is fully loaded, in client-side only
// if (typeof window !== 'undefined') {
//   window.addEventListener('load', function () {
//     const loader = document.getElementById('loader');
//     if (loader) {
//       loader.style.display = 'none';
//     }
//   });
// }
