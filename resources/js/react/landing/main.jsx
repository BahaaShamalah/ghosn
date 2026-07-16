import { createRoot } from 'react-dom/client';
import App from './App';
import StandaloneApp from './StandaloneApp';
import { getBootstrap } from './content';

const root = document.getElementById('ghosn-landing-root');

if (root) {
    const pageType = getBootstrap().pageType ?? 'home';
    const Component = pageType === 'home' ? App : StandaloneApp;

    createRoot(root).render(<Component />);
}
