/** @format */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Only initialize Echo if broadcasting is configured and required keys are present
const broadcastDriver = import.meta.env.VITE_BROADCAST_DRIVER;
const hasBroadcastKey = import.meta.env.VITE_ABLY_PUBLIC_KEY || import.meta.env.VITE_PUSHER_APP_KEY || import.meta.env.VITE_REVERB_APP_KEY;

// Don't initialize Echo if broadcasting is disabled (null or log) or if no keys are configured
if (hasBroadcastKey && broadcastDriver && broadcastDriver !== 'null' && broadcastDriver !== 'log') {
  const echoConfig = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_ABLY_PUBLIC_KEY || import.meta.env.VITE_PUSHER_APP_KEY || import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST || 'realtime-pusher.ably.io',
    wsPort: import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || 443,
    disableStats: true,
    encrypted: true,
  };

  // Pusher requires a cluster option, but provide a default to avoid errors
  if (broadcastDriver === 'pusher') {
    echoConfig.cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1';
  }

  try {
    window.Echo = new Echo(echoConfig);
  } catch (error) {
    // Silently fail if Echo initialization fails (e.g., in test environments)
    console.warn('Echo initialization failed:', error);
  }
}
