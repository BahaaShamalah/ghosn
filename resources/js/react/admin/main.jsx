import { createRoot } from 'react-dom/client';
import App from './App';

const root = document.getElementById('ghosn-admin-root');

if (root) {
    createRoot(root).render(<App />);
}
