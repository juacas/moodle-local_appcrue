// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

define([], function() {
    const init = (url, strings) => {
        const button = document.getElementById('local-appcrue-api-test-button');
        const result = document.getElementById('local-appcrue-api-test-result');
        if (!button || !result) {
            return Promise.resolve();
        }

        const showResult = (message, severity) => {
            result.textContent = message;
            result.className = `alert alert-${severity}`;
        };

        const runTest = async() => {
            button.disabled = true;
            showResult('', 'info');
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {'Accept': 'application/json'},
                });
                const body = await response.text();
                let payload = {};
                try {
                    payload = JSON.parse(body);
                } catch (error) {
                    // A denied or proxy-generated response may not contain JSON.
                }

                if (response.status === 400 && payload.error && Number(payload.error.code) === 2) {
                    showResult(strings.passed, 'success');
                } else if (response.status === 403) {
                    showResult(strings.denied, 'danger');
                } else if (response.status === 404) {
                    showResult(strings.notfound, 'warning');
                } else {
                    showResult(`${strings.unexpected} (HTTP ${response.status}).`, 'warning');
                }
            } catch (error) {
                showResult(strings.networkerror, 'danger');
            } finally {
                button.disabled = false;
            }
        };

        button.addEventListener('click', runTest);
        return runTest();
    };

    const initPage = (strings) => {
        const loading = document.getElementById('local-appcrue-loading');
        const report = document.getElementById('local-appcrue-report');
        if (!loading || !report) {
            return;
        }

        const loadingMarkup = loading.innerHTML;
        const showLoading = () => {
            report.hidden = true;
            loading.hidden = false;
            loading.className = 'alert alert-info';
            loading.innerHTML = loadingMarkup;
        };
        const showFailure = () => {
            loading.className = 'alert alert-danger';
            loading.textContent = strings.failed;
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'btn btn-secondary ms-2';
            retry.textContent = strings.retry;
            retry.addEventListener('click', () => loadReport(window.location.href));
            loading.appendChild(retry);
        };
        const renderReport = async(response) => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            const documentContent = new DOMParser().parseFromString(await response.text(), 'text/html');
            const fragment = documentContent.getElementById('local-appcrue-report-fragment');
            if (!fragment) {
                throw new Error('Missing diagnostic report');
            }
            report.innerHTML = fragment.innerHTML;
            const config = report.querySelector('#local-appcrue-api-test-config');
            if (config) {
                await init(config.dataset.url, JSON.parse(config.dataset.strings));
            }
            report.hidden = false;
            loading.hidden = true;
        };
        const loadReport = async(url) => {
            showLoading();
            try {
                const requestUrl = new URL(url, window.location.href);
                requestUrl.searchParams.set('fragment', '1');
                const response = await fetch(requestUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                await renderReport(response);
            } catch (error) {
                showFailure();
            }
        };

        report.addEventListener('submit', async(event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() !== 'post') {
                return;
            }
            event.preventDefault();
            showLoading();
            try {
                const requestUrl = new URL(form.getAttribute('action'), window.location.href);
                requestUrl.searchParams.set('fragment', '1');
                const response = await fetch(requestUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    body: new FormData(form),
                });
                if (response.redirected) {
                    const redirectUrl = new URL(response.url);
                    const isFragment = redirectUrl.searchParams.get('fragment') === '1';
                    redirectUrl.searchParams.delete('fragment');
                    window.history.replaceState(null, '', redirectUrl);
                    if (isFragment) {
                        await renderReport(response);
                    } else {
                        await loadReport(redirectUrl);
                    }
                } else {
                    await renderReport(response);
                }
            } catch (error) {
                showFailure();
            }
        });

        loadReport(window.location.href);
    };

    return {init, initPage};
});
