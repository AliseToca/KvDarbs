import axios from 'axios';

interface ProjectWindow extends Window {
    axios?: typeof axios;
}

const w = window as ProjectWindow;

// Axios global setup
w.axios = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Image loading script

const pictures = document.querySelectorAll('figure.picture');

pictures.forEach((container) => {
    const img = container.querySelector('picture img') as HTMLImageElement | null;
    img.classList.add('loading');

    img.onload = () => {
        img.classList.remove('loading');
        img.style.opacity = '1';
    };

    if (img.complete && img.naturalHeight !== 0) {
        img.onload(null);
    }
});
