import {app} from "./bootstrap";
import '@tabler/core/dist/js/tabler.js';
import './styles/tabler.css';
import './styles/app.css';
import ResendLinkController from "./controllers/resend-link-controller";

app.register('resend-link', ResendLinkController);
