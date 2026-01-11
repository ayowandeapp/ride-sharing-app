import Pusher from 'pusher-js'
import Echo from 'laravel-echo'

// Force disable Pusher's WebSocket fallback and retry logic
// Pusher.logToConsole = true; // Enable for debugging

const echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-app-key',
    wsHost: window.location.hostname,
    wsPort: 8080,
    wssPort: 8080,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    cluster: 'mt1',
})

// Debug logging
echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Connection error:', err);
});

export default echo