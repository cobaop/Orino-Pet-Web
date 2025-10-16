// CDN Vue & Echo global (pastikan sudah ada di HTML utama atau tambahkan di sini)
if (typeof Vue === 'undefined') {
    document.write('<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"><\/script>');
}
if (typeof Echo === 'undefined') {
    document.write('<script src="https://js.pusher.com/7.2/pusher.min.js"><\/script>');
    document.write('<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"><\/script>');
}

window.addEventListener('DOMContentLoaded', () => {
    const template = `
        <div v-if="visible"
            class="position-fixed top-0 end-0 m-3 bg-danger text-white p-3 rounded shadow"
            style="z-index: 9999;">
            {{ message }}
        </div>
    `;

    const root = document.createElement('div');
    root.id = 'notif-app';
    document.body.appendChild(root);

    // Konfigurasi Echo
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: document.head.querySelector('meta[name="pusher-key"]').content,
        cluster: document.head.querySelector('meta[name="pusher-cluster"]').content,
        forceTLS: true,
    });

    // Komponen
    const NotifToast = {
        template,
        data() {
            return {
                visible: false,
                message: '',
            };
        },
        mounted() {
            const userId = document.head.querySelector('meta[name="user-id"]')?.content;
            if (!userId) return;

            Echo.private(`user.${userId}`)
                .listen('.pembayaran.gagal', (e) => {
                    this.message = e.message || '❌ Pembayaran gagal karena waktu habis.';
                    this.visible = true;
                    setTimeout(() => this.visible = false, 5000);
                });
        }
    };

    Vue.createApp({ components: { NotifToast } })
        .component('notif-toast', NotifToast)
        .mount('#notif-app');
});
