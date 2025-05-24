// assets/controllers/resend-link-controller.js

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    handleResend(event) {
        event.preventDefault();

        const url = this.element.getAttribute('href');

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Код успешно отправлен снова!');
                } else {
                    alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при отправке запроса.');
            });
    }
}