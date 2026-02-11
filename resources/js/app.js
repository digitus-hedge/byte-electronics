import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp, h } from 'vue';
import { createInertiaApp,router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import topbar from 'topbar';
const appName = import.meta.env.VITE_APP_NAME || 'Byte-Electronics';

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
createInertiaApp({
    title: (title) => `${title}  ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: false,
});
router.on('start', () => {
    // NProgress.start();
    topbar.show();
});
router.on('finish', (event) => {
    if (event.detail.visit.completed) {
        // setTimeout(() => {
            // NProgress.done();
        // }, 5000);
        topbar.hide();
    } else if (event.detail.visit.interrupted) {
        // NProgress.set(0);
    } else if (event.detail.visit.cancelled) {
        // NProgress.done();
        // NProgress.remove();
    }
});


document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('loader').style.display = 'none';
});
