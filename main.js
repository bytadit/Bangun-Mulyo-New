const { app, BrowserWindow } = require('electron');
const { exec } = require('child_process');

let laravelServer;

function createWindow() {
    const win = new BrowserWindow({
        width: 800,
        height: 600,
        webPreferences: {
            nodeIntegration: true
        }
    });

    win.loadURL('http://localhost:8000'); // Use localhost instead of your Valet URL

    win.webContents.openDevTools(); // Open DevTools for debugging

    win.on('closed', () => {
        // Dereference the window object
        // When the window is closed, it is necessary to dereference the window object
        // to avoid memory leaks
        win = null;
    });
}

app.whenReady().then(() => {
    // Start the Laravel server
    laravelServer = exec('php artisan serve');

    laravelServer.stdout.on('data', (data) => {
        console.log(`Server stdout: ${data}`);
    });

    laravelServer.stderr.on('data', (data) => {
        console.error(`Server stderr: ${data}`);
    });

    laravelServer.on('close', (code) => {
        console.log(`Server process exited with code ${code}`);
    });

    // Create Electron window
    createWindow();
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
        createWindow();
    }
});

// Catch termination signal to stop Laravel server
app.on('before-quit', () => {
    if (laravelServer) {
        laravelServer.kill('SIGINT'); // Send Ctrl+C signal to stop the server
    }
});

// Handle any unhandled exceptions
process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err);
    app.quit();
});
