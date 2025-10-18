import chokidar from 'chokidar';
import { writeFileSync, existsSync, readFileSync } from 'fs';
import { resolve } from 'path';

// Define the paths to watch
const watchPaths = [
    'resources/views/**/*.{blade.php}',
    'app/Http/**/*.{php}',
    'routes/**/*.{php}',
    'app/Models/**/*.{php}',
    'app/Services/**/*.{php}',
    'lang/**/*.{php}'
];

const timestampFile = 'bootstrap/cache/bladewatch-timestamp';

// Create timestamp directory if it doesn't exist
const timestampDir = resolve('bootstrap/cache');
if (!existsSync(timestampDir)) {
    import('fs').then(({ mkdirSync }) => {
        mkdirSync(timestampDir, { recursive: true });
    }).catch(console.error);
}

// Initialize timestamp file if it doesn't exist
if (!existsSync(timestampFile)) {
    try {
        writeFileSync(timestampFile, Date.now().toString());
    } catch (err) {
        console.error('Could not create timestamp file:', err);
    }
}

// Watch for changes in Blade files and other relevant files
chokidar.watch(watchPaths, {
    ignored: /node_modules|\.git|storage|vendor/,
    persistent: true,
    ignoreInitial: true
}).on('change', (path) => {
    console.log(`File changed: ${path}`);
    
    // Update the timestamp
    const timestamp = Date.now();
    try {
        writeFileSync(timestampFile, timestamp.toString());
        console.log(`Timestamp updated: ${timestamp}`);
    } catch (err) {
        console.error('Error updating timestamp:', err);
    }
});

console.log('Watching for file changes...');