(function () {
    function setText(node, text) {
        if (node) node.textContent = text;
    }

    function setActive(frame, active) {
        if (frame) frame.classList.toggle('is-camera-active', active);
    }

    function ensureHtml5Region(frame) {
        let region = frame.querySelector('.qr-html5-region');

        if (!region) {
            region = document.createElement('div');
            region.className = 'qr-html5-region';
            region.id = `qr-html5-${Math.random().toString(36).slice(2)}`;
            frame.appendChild(region);
        }

        return region;
    }

    window.GatehouseQrScanner = {
        create(options) {
            const frame = document.querySelector(options.frame);
            const video = document.querySelector(options.video);
            const input = document.querySelector(options.input);
            const form = document.querySelector(options.form);
            const startButton = document.querySelector(options.startButton);
            const stopButton = document.querySelector(options.stopButton);
            const status = document.querySelector(options.status);

            if (!frame || !input || !startButton) return;

            let stream = null;
            let detector = null;
            let html5Scanner = null;
            let running = false;
            let locked = false;

            function isSecureEnough() {
                return window.isSecureContext || ['localhost', '127.0.0.1'].includes(window.location.hostname);
            }

            function submitDetectedCode(rawValue) {
                const code = (rawValue || '').trim();
                if (!code || locked) return;

                locked = true;
                input.value = code;
                setText(status, 'Đã nhận mã QR. Đang kiểm tra...');

                stop().finally(() => {
                    if (typeof options.onDetected === 'function') {
                        options.onDetected(code, form);
                    } else if (form) {
                        form.requestSubmit();
                    }
                });
            }

            async function stopHtml5() {
                if (!html5Scanner) return;

                try {
                    if (html5Scanner.isScanning) {
                        await html5Scanner.stop();
                    }
                    await html5Scanner.clear();
                } catch (error) {
                    // The scanner may already be stopped by the browser.
                }
            }

            async function stop() {
                running = false;
                await stopHtml5();

                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                    stream = null;
                }

                if (video) video.srcObject = null;
                setActive(frame, false);
                startButton.hidden = false;
                if (stopButton) stopButton.hidden = true;
            }

            async function scanLoop() {
                if (!running || !detector || locked || !video) return;

                try {
                    const results = await detector.detect(video);
                    if (results.length > 0) {
                        submitDetectedCode(results[0].rawValue || '');
                        return;
                    }
                } catch (error) {
                    setText(status, 'Camera đang hoạt động, vui lòng đưa mã QR rõ hơn.');
                }

                requestAnimationFrame(scanLoop);
            }

            async function startNativeScanner() {
                if (!navigator.mediaDevices?.getUserMedia || !window.BarcodeDetector || !video) {
                    setText(status, 'Trình duyệt chưa hỗ trợ quét QR bằng camera. Vui lòng nhập mã thủ công.');
                    return;
                }

                detector = new BarcodeDetector({ formats: ['qr_code'] });
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });

                video.srcObject = stream;
                await video.play();
                running = true;
                setActive(frame, true);
                startButton.hidden = true;
                if (stopButton) stopButton.hidden = false;
                setText(status, 'Camera đã bật. Đưa mã QR vào khung để quét.');
                requestAnimationFrame(scanLoop);
            }

            async function startHtml5Scanner() {
                const region = ensureHtml5Region(frame);

                html5Scanner = new Html5Qrcode(region.id, {
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    verbose: false
                });

                setActive(frame, true);
                startButton.hidden = true;
                if (stopButton) stopButton.hidden = false;
                setText(status, 'Camera đã bật. Đưa mã QR vào khung để quét.');

                await html5Scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const size = Math.floor(Math.min(viewfinderWidth, viewfinderHeight) * 0.72);
                            return { width: size, height: size };
                        },
                        aspectRatio: 1.777
                    },
                    submitDetectedCode,
                    function () {}
                );
            }

            async function start() {
                locked = false;

                if (!isSecureEnough()) {
                    setText(status, 'Camera chỉ hoạt động trên HTTPS hoặc localhost. Vui lòng mở bằng link HTTPS khi demo.');
                    return;
                }

                try {
                    if (window.Html5Qrcode && window.Html5QrcodeSupportedFormats) {
                        await startHtml5Scanner();
                        return;
                    }

                    await startNativeScanner();
                } catch (error) {
                    await stop();
                    setText(status, 'Không mở được camera. Vui lòng cấp quyền camera hoặc nhập mã thủ công.');
                }
            }

            startButton.addEventListener('click', start);
            if (stopButton) stopButton.addEventListener('click', stop);
            window.addEventListener('beforeunload', stop);

            return { start, stop };
        }
    };
})();
