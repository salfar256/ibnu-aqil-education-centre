import * as pdfjsLib from './pdfjs/pdf.mjs';

pdfjsLib.GlobalWorkerOptions.workerSrc = new URL('./pdfjs/pdf.worker.mjs', import.meta.url).href;

const zoomLevels = [
    { value: 0.75, className: 'pdf-zoom-75', label: '75%' },
    { value: 1, className: 'pdf-zoom-100', label: '100%' },
    { value: 1.25, className: 'pdf-zoom-125', label: '125%' },
    { value: 1.5, className: 'pdf-zoom-150', label: '150%' },
    { value: 2, className: 'pdf-zoom-200', label: '200%' },
];

class PdfReader {
    constructor(element) {
        this.element = element;
        this.canvas = element.querySelector('[data-pdf-canvas]');
        this.wrap = element.querySelector('[data-pdf-canvas-wrap]');
        this.status = element.querySelector('[data-pdf-status]');
        this.current = element.querySelector('[data-pdf-current]');
        this.total = element.querySelector('[data-pdf-total]');
        this.previous = element.querySelector('[data-pdf-previous]');
        this.next = element.querySelector('[data-pdf-next]');
        this.zoomOut = element.querySelector('[data-pdf-zoom-out]');
        this.zoomIn = element.querySelector('[data-pdf-zoom-in]');
        this.fit = element.querySelector('[data-pdf-fit]');
        this.zoomLabel = element.querySelector('[data-pdf-zoom-label]');
        this.document = null;
        this.loadingTask = null;
        this.renderTask = null;
        this.queue = Promise.resolve();
        this.version = 0;
        this.page = 1;
        this.zoomIndex = 1;
        this.lastWidth = 0;
        this.destroyed = false;
        this.resizeFrame = null;

        this.previous.addEventListener('click', () => this.goTo(this.page - 1));
        this.next.addEventListener('click', () => this.goTo(this.page + 1));
        this.zoomOut.addEventListener('click', () => this.setZoom(this.zoomIndex - 1));
        this.zoomIn.addEventListener('click', () => this.setZoom(this.zoomIndex + 1));
        this.fit.addEventListener('click', () => this.setZoom(1));

        if ('ResizeObserver' in window) {
            this.observer = new ResizeObserver(() => {
                if (this.resizeFrame !== null) cancelAnimationFrame(this.resizeFrame);
                this.resizeFrame = requestAnimationFrame(() => {
                    this.resizeFrame = null;
                    const width = this.availableWidth();
                    if (this.document && width > 0 && Math.abs(width - this.lastWidth) > 1) {
                        this.requestRender();
                    }
                });
            });
            this.observer.observe(this.wrap);
        } else {
            this.onResize = () => this.document && this.requestRender();
            window.addEventListener('resize', this.onResize);
        }

        window.addEventListener('pagehide', event => {
            if (!event.persisted) this.destroy();
        }, { once: true });

        this.load();
    }

    async load() {
        try {
            this.loadingTask = pdfjsLib.getDocument({
                url: this.element.dataset.pdfUrl,
                withCredentials: true,
                isEvalSupported: false,
                enableXfa: false,
                cMapUrl: new URL('./pdfjs/cmaps/', import.meta.url).href,
                cMapPacked: true,
                standardFontDataUrl: new URL('./pdfjs/standard_fonts/', import.meta.url).href,
                wasmUrl: new URL('./pdfjs/wasm/', import.meta.url).href,
            });
            this.document = await this.loadingTask.promise;
            if (this.destroyed) return;
            this.total.textContent = String(this.document.numPages);
            this.updateControls();
            this.requestRender();
        } catch (error) {
            if (!this.destroyed) this.showError(error);
        }
    }

    availableWidth() {
        const styles = getComputedStyle(this.wrap);
        return Math.max(0, this.wrap.clientWidth - parseFloat(styles.paddingLeft || '0') - parseFloat(styles.paddingRight || '0'));
    }

    goTo(page) {
        if (!this.document || page < 1 || page > this.document.numPages || page === this.page) return;
        this.page = page;
        this.wrap.scrollTop = 0;
        this.wrap.scrollLeft = 0;
        this.updateControls();
        this.requestRender();
    }

    setZoom(index) {
        if (!this.document || index < 0 || index >= zoomLevels.length || index === this.zoomIndex) return;
        this.element.classList.remove(zoomLevels[this.zoomIndex].className);
        this.zoomIndex = index;
        this.element.classList.add(zoomLevels[index].className);
        if (index === 1) {
            this.wrap.scrollLeft = 0;
            this.wrap.scrollTop = 0;
        }
        this.updateControls();
        this.requestRender();
    }

    updateControls() {
        const ready = Boolean(this.document) && !this.destroyed;
        this.previous.disabled = !ready || this.page <= 1;
        this.next.disabled = !ready || this.page >= this.document.numPages;
        this.zoomOut.disabled = !ready || this.zoomIndex === 0;
        this.zoomIn.disabled = !ready || this.zoomIndex === zoomLevels.length - 1;
        this.fit.disabled = !ready || this.zoomIndex === 1;
        this.zoomLabel.textContent = zoomLevels[this.zoomIndex].label;
    }

    requestRender() {
        if (!this.document || this.destroyed) return;
        const version = ++this.version;
        if (this.renderTask) this.renderTask.cancel();
        // A cancelled canvas task must finish before the next task uses it.
        this.queue = this.queue.catch(() => {}).then(() => this.render(version));
    }

    async render(version) {
        if (version !== this.version || this.destroyed) return;
        let task = null;
        const pageNumber = this.page;
        this.element.setAttribute('aria-busy', 'true');
        this.status.setAttribute('role', 'status');
        this.status.textContent = `Menampilkan halaman ${pageNumber}…`;
        this.element.classList.remove('pdf-has-error');

        try {
            const page = await this.document.getPage(pageNumber);
            if (version !== this.version || this.destroyed) return;
            const width = this.availableWidth();
            if (width <= 0) return;
            this.lastWidth = width;
            const natural = page.getViewport({ scale: 1 });
            const viewport = page.getViewport({ scale: width * zoomLevels[this.zoomIndex].value / natural.width });
            const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
            this.canvas.width = Math.max(1, Math.floor(viewport.width * pixelRatio));
            this.canvas.height = Math.max(1, Math.floor(viewport.height * pixelRatio));
            const context = this.canvas.getContext('2d', { alpha: false });
            if (!context) throw new Error('Canvas tidak tersedia.');
            task = page.render({
                canvasContext: context,
                viewport,
                transform: pixelRatio === 1 ? null : [pixelRatio, 0, 0, pixelRatio, 0, 0],
                background: 'rgb(255,255,255)',
            });
            this.renderTask = task;
            await task.promise;
            if (version !== this.version || this.destroyed) return;
            this.canvas.hidden = false;
            this.canvas.setAttribute('aria-label', `Halaman ${pageNumber} dari ${this.document.numPages}: ${this.element.dataset.pdfName || 'dokumen PDF'}`);
            this.current.textContent = String(pageNumber);
            this.status.textContent = `Halaman ${pageNumber} dari ${this.document.numPages} ditampilkan.`;
        } catch (error) {
            if (error?.name !== 'RenderingCancelledException' && version === this.version && !this.destroyed) {
                this.showError(error);
            }
        } finally {
            if (this.renderTask === task) this.renderTask = null;
            if (version === this.version && !this.destroyed) this.element.setAttribute('aria-busy', 'false');
        }
    }

    showError(error) {
        this.element.classList.add('pdf-has-error');
        this.element.setAttribute('aria-busy', 'false');
        this.canvas.hidden = true;
        this.status.setAttribute('role', 'alert');
        this.status.textContent = error?.name === 'PasswordException'
            ? 'PDF ini dilindungi kata sandi. Buka berkas asli menggunakan aplikasi PDF Anda.'
            : 'PDF belum dapat ditampilkan. Muat ulang halaman untuk memeriksa akses, atau gunakan tautan Buka PDF asli di bawah.';
        this.updateControls();
    }

    destroy() {
        this.destroyed = true;
        this.version++;
        this.observer?.disconnect();
        if (this.resizeFrame !== null) cancelAnimationFrame(this.resizeFrame);
        if (this.onResize) window.removeEventListener('resize', this.onResize);
        this.renderTask?.cancel();
        if (this.loadingTask) this.loadingTask.destroy().catch(() => {});
    }
}

document.querySelectorAll('[data-pdf-reader]').forEach(element => new PdfReader(element));
