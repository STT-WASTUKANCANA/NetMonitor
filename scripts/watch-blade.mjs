import chokidar from 'chokidar';
import { writeFileSync, existsSync, mkdirSync } from 'fs';
import { resolve } from 'path';

// Define the paths to watch for changes that should trigger a page reload
const watchPaths = [
    'resources/views/**/*.blade.php',
    'resources/views/**/**/*.blade.php',
    'app/Http/**/*.php',
    'routes/**/*.php',
    'app/Models/**/*.php',
    'app/Services/**/*.php',
    'app/View/Components/**/*.php',
    'lang/**/*.php',
    'config/**/*.php'
];

const timestampFile = './storage/framework/cache/laravel-livereload-timestamp';

// Ensure the directory exists
const timestampDir = resolve('./storage/framework/cache');
if (!existsSync(timestampDir)) {
    mkdirSync(timestampDir, { recursive: true });
}

// Initialize timestamp file if it doesn't exist
if (!existsSync(timestampFile)) {
    try {
        writeFileSync(timestampFile, Date.now().toString());
        console.log('Created initial timestamp file');
    } catch (err) {
        console.error('Could not create timestamp file:', err.message);
        process.exit(1);
    }
}

// Watch for changes in Blade files and other relevant files
chokidar.watch(watchPaths, {
    ignored: /node_modules|\.git|storage|vendor|public/,
    persistent: true,
    ignoreInitial: true,
    awaitWriteFinish: true // Wait for files to be completely written
}).on('change', (path) => {
    console.log(`File changed: ${path}`);
    
    // Update the timestamp to trigger live reload
    const timestamp = Date.now();
    try {
        writeFileSync(timestampFile, timestamp.toString());
        console.log(`Timestamp updated: ${timestamp}`);
    } catch (err) {
        console.error('Error updating timestamp:', err.message);
    }
}).on('add', (path) => {
    if (path.endsWith('.blade.php')) {
        console.log(`New Blade file detected: ${path}`);
        const timestamp = Date.now();
        try {
            writeFileSync(timestampFile, timestamp.toString());
            console.log(`Timestamp updated: ${timestamp}`);
        } catch (err) {
            console.error('Error updating timestamp:', err.message);
        }
    }
}).on('unlink', (path) => {
    if (path.endsWith('.blade.php')) {
        console.log(`Blade file removed: ${path}`);
        const timestamp = Date.now();
        try {
            writeFileSync(timestampFile, timestamp.toString());
            console.log(`Timestamp updated: ${timestamp}`);
        } catch (err) {
            console.error('Error updating timestamp:', err.message);
        }
    }
});

console.log('Blade file watcher started. Monitoring:');
console.log(watchPaths.join('\n'));
console.log('\nWaiting for file changes...');