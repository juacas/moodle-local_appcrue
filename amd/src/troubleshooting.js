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
            return;
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
        runTest();
    };

    return {init};
});
