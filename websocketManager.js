// websocketManager.js

class WebSocketManager {
    constructor(url) {
      this.url = url;
      this.ws = null;
      this.listeners = [];
    }
  
    connect() {
      this.ws = new WebSocket(this.url);
  
      this.ws.onopen = () => {
        console.log('WebSocket is connected');
      };
  
      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        this.listeners.forEach((callback) => callback(data));
      };
  
      this.ws.onerror = (error) => {
        console.error('WebSocket error:', error);
      };
  
      this.ws.onclose = () => {
        console.log('WebSocket connection closed');
        // Optionally implement reconnection logic here
      };
    }
  
    addListener(callback) {
      this.listeners.push(callback);
    }
  
    removeListener(callback) {
      this.listeners = this.listeners.filter((listener) => listener !== callback);
    }
  
    send(data) {
      if (this.ws && this.ws.readyState === WebSocket.OPEN) {
        this.ws.send(JSON.stringify(data));
      } else {
        console.error('WebSocket is not open. Unable to send data.');
      }
    }
  }
  
  // Export a singleton instance
  const websocketManager = new WebSocketManager('ws://192.168.0.101:8080');
  export default websocketManager;
  