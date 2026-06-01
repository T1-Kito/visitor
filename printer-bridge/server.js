const http = require('http');
const fs = require('fs');
const path = require('path');
const os = require('os');
const { execFile } = require('child_process');

const rootDir = __dirname;
const configPath = path.join(rootDir, 'config.json');
const defaultConfigPath = path.join(rootDir, 'config.example.json');
const jobsDir = path.join(rootDir, 'jobs');
const scriptsDir = path.join(rootDir, 'scripts');

function loadConfig() {
  const source = fs.existsSync(configPath) ? configPath : defaultConfigPath;
  return JSON.parse(fs.readFileSync(source, 'utf8'));
}

let config = loadConfig();
fs.mkdirSync(jobsDir, { recursive: true });

function publicConfig() {
  return {
    host: config.host ?? '127.0.0.1',
    port: config.port ?? 9191,
    paper: config.paper === '58mm' ? '58mm' : '80mm',
    mode: config.mode === 'escpos' ? 'escpos' : 'preview',
    printerName: config.printerName ?? '',
    openAfterPrint: config.openAfterPrint !== false,
    allowedOrigins: config.allowedOrigins ?? [],
  };
}

function saveConfig(payload) {
  const nextConfig = {
    ...config,
    paper: payload.paper === '58mm' ? '58mm' : '80mm',
    mode: payload.mode === 'escpos' ? 'escpos' : 'preview',
    printerName: String(payload.printerName ?? '').trim(),
    openAfterPrint: payload.openAfterPrint !== false,
  };

  if (Array.isArray(payload.allowedOrigins)) {
    nextConfig.allowedOrigins = payload.allowedOrigins
      .map((item) => String(item).trim())
      .filter(Boolean);
  }

  fs.writeFileSync(configPath, `${JSON.stringify(nextConfig, null, 2)}\n`, 'utf8');
  config = nextConfig;

  return publicConfig();
}

function sendJson(res, status, payload, origin = '*') {
  res.writeHead(status, {
    'Content-Type': 'application/json; charset=utf-8',
    'Access-Control-Allow-Origin': origin,
    'Access-Control-Allow-Headers': 'Content-Type, X-Printer-Token',
    'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
  });
  res.end(JSON.stringify(payload));
}

function readJson(req) {
  return new Promise((resolve, reject) => {
    let body = '';
    req.on('data', (chunk) => {
      body += chunk;
      if (body.length > 1024 * 1024) {
        reject(new Error('Payload quá lớn'));
        req.destroy();
      }
    });
    req.on('end', () => {
      try {
        resolve(body ? JSON.parse(body) : {});
      } catch (error) {
        reject(error);
      }
    });
  });
}

function escapeHtml(value) {
  return String(value ?? '-')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function ticketHtml(payload) {
  const paperWidth = config.paper === '58mm' ? '58mm' : '80mm';
  const contentWidth = config.paper === '58mm' ? '50mm' : '70mm';
  const qrSize = config.paper === '58mm' ? '38mm' : '48mm';

  return `<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>QR ${escapeHtml(payload.code)}</title>
  <style>
    @page { size: ${paperWidth} auto; margin: 5mm; }
    * { box-sizing: border-box; }
    html, body { width: ${paperWidth}; margin: 0; padding: 0; background: #fff; color: #0b1f3a; font-family: Arial, sans-serif; }
    .ticket { width: ${contentWidth}; margin: 0 auto; text-align: center; }
    .brand { margin-bottom: 2mm; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    h1 { margin: 0 0 2mm; font-size: 16px; line-height: 1.2; }
    .muted { margin-bottom: 3mm; color: #64748b; font-size: 10px; line-height: 1.35; }
    .qr { display: grid; place-items: center; margin: 2mm auto; }
    .qr svg { width: ${qrSize}; height: ${qrSize}; display: block; }
    .code { margin: 3mm 0; padding: 2mm; border: 1px dashed #94a3b8; border-radius: 3mm; font-size: 13px; font-weight: 700; }
    .row { display: flex; justify-content: space-between; gap: 4mm; margin: 1.8mm 0; font-size: 10.5px; text-align: left; }
    .row span { color: #64748b; }
    .row strong { max-width: 42mm; text-align: right; word-break: break-word; }
    .note { margin-top: 3mm; padding-top: 2mm; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 9.5px; line-height: 1.35; }
  </style>
</head>
<body>
  <section class="ticket">
    <div class="brand">Gatehouse Pro</div>
    <h1>Phiếu mã QR</h1>
    <div class="muted">Vui lòng xuất trình mã này tại quầy lễ tân.</div>
    <div class="qr">${payload.qrSvg ?? ''}</div>
    <div class="code">${escapeHtml(payload.code)}</div>
    <div class="row"><span>Khách</span><strong>${escapeHtml(payload.visitorName)}</strong></div>
    <div class="row"><span>Công ty</span><strong>${escapeHtml(payload.visitorCompany)}</strong></div>
    <div class="row"><span>Người tiếp</span><strong>${escapeHtml(payload.hostName)}</strong></div>
    <div class="row"><span>Giờ hẹn</span><strong>${escapeHtml(payload.scheduledAt)}</strong></div>
    <div class="row"><span>Trạng thái</span><strong>${escapeHtml(payload.status)}</strong></div>
    <div class="note">Mã QR chỉ dùng cho lượt khách này. Không chia sẻ mã cho người khác.</div>
  </section>
</body>
</html>`;
}

function openFile(filePath) {
  if (!config.openAfterPrint) {
    return;
  }

  if (process.platform === 'win32') {
    execFile('cmd', ['/c', 'start', '""', filePath], { windowsHide: true });
    return;
  }

  if (process.platform === 'darwin') {
    execFile('open', [filePath]);
    return;
  }

  execFile('xdg-open', [filePath]);
}

function printEscpos(payloadPath) {
  return new Promise((resolve, reject) => {
    if (process.platform !== 'win32') {
      reject(new Error('ESC/POS raw mode hiện chỉ hỗ trợ Windows trong bản MVP này.'));
      return;
    }

    if (!config.printerName) {
      reject(new Error('Chưa cấu hình máy in trong Printer Bridge.'));
      return;
    }

    const scriptPath = path.join(scriptsDir, 'print-escpos.ps1');
    execFile(
      'powershell',
      [
        '-NoProfile',
        '-ExecutionPolicy',
        'Bypass',
        '-File',
        scriptPath,
        '-PayloadPath',
        payloadPath,
        '-PrinterName',
        config.printerName,
        '-Paper',
        config.paper,
      ],
      { windowsHide: true },
      (error, stdout, stderr) => {
        if (error) {
          reject(new Error(stderr || stdout || error.message));
          return;
        }

        resolve(stdout.trim());
      }
    );
  });
}

function listPrinters() {
  return new Promise((resolve) => {
    if (process.platform !== 'win32') {
      resolve([]);
      return;
    }

    execFile(
      'powershell',
      ['-NoProfile', '-Command', 'Get-Printer | Select-Object Name,DriverName,PortName | ConvertTo-Json -Compress'],
      { windowsHide: true },
      (error, stdout) => {
        if (error || !stdout.trim()) {
          resolve([]);
          return;
        }

        try {
          const parsed = JSON.parse(stdout);
          resolve(Array.isArray(parsed) ? parsed : [parsed]);
        } catch {
          resolve([]);
        }
      }
    );
  });
}

function originAllowed(origin) {
  if (!origin) {
    return '*';
  }

  if (/^https?:\/\/(127\.0\.0\.1|localhost)(:\d+)?$/.test(origin)) {
    return origin;
  }

  if (/^https:\/\/[a-z0-9-]+\.ngrok-free\.app$/.test(origin)) {
    return origin;
  }

  const allowed = config.allowedOrigins ?? [];
  return allowed.includes(origin) ? origin : allowed[0] ?? '*';
}

async function runPrintJob(payload) {
  if (!payload.code || !payload.qrSvg) {
    const error = new Error('Thiếu mã lịch hoặc QR SVG.');
    error.status = 422;
    throw error;
  }

  const fileName = `qr-${String(payload.code).replace(/[^a-zA-Z0-9_-]/g, '-')}.html`;
  const filePath = path.join(jobsDir, fileName);
  fs.writeFileSync(filePath, ticketHtml(payload), 'utf8');

  if (config.mode === 'escpos') {
    const payloadPath = path.join(jobsDir, `${path.parse(fileName).name}.json`);
    fs.writeFileSync(payloadPath, JSON.stringify(payload), 'utf8');
    await printEscpos(payloadPath);
  } else {
    openFile(filePath);
  }

  return {
    filePath,
    mode: config.mode,
    printerName: config.printerName ?? null,
  };
}

const server = http.createServer(async (req, res) => {
  const origin = originAllowed(req.headers.origin);
  const url = new URL(req.url, `http://${config.host}:${config.port}`);

  if (req.method === 'OPTIONS') {
    sendJson(res, 204, {}, origin);
    return;
  }

  if (req.method === 'GET' && url.pathname === '/health') {
    sendJson(res, 200, {
      ok: true,
      service: 'gatehouse-printer-bridge',
      mode: config.mode,
      paper: config.paper,
      printerName: config.printerName ?? '',
      hostname: os.hostname(),
      time: new Date().toISOString(),
    }, origin);
    return;
  }

  if (req.method === 'GET' && url.pathname === '/config') {
    sendJson(res, 200, { ok: true, config: publicConfig() }, origin);
    return;
  }

  if (req.method === 'POST' && url.pathname === '/config') {
    try {
      const payload = await readJson(req);
      sendJson(res, 200, {
        ok: true,
        message: 'Đã lưu cấu hình máy in.',
        config: saveConfig(payload),
      }, origin);
    } catch (error) {
      sendJson(res, 500, { ok: false, message: error.message }, origin);
    }
    return;
  }

  if (req.method === 'GET' && url.pathname === '/printers') {
    sendJson(res, 200, { ok: true, printers: await listPrinters() }, origin);
    return;
  }

  if (req.method === 'POST' && url.pathname === '/print') {
    try {
      const payload = await readJson(req);
      const result = await runPrintJob(payload);
      sendJson(res, 200, {
        ok: true,
        message: config.mode === 'escpos'
          ? 'Đã gửi phiếu QR tới máy in nhiệt.'
          : 'Đã gửi phiếu QR sang Printer Bridge.',
        ...result,
      }, origin);
    } catch (error) {
      sendJson(res, error.status ?? 500, { ok: false, message: error.message }, origin);
    }
    return;
  }

  sendJson(res, 404, { ok: false, message: 'Endpoint không tồn tại.' }, origin);
});

server.listen(config.port, config.host, () => {
  console.log(`Gatehouse Printer Bridge running at http://${config.host}:${config.port}`);
  console.log(`Mode: ${config.mode} | Paper: ${config.paper}`);
});
