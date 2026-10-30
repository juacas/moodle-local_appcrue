<?php
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

/**
 * AppCRUE connection diagnostics and safe configuration repairs.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/locallib.php');

admin_externalpage_setup('local_appcrue_troubleshooting');
$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_url(new moodle_url('/local/appcrue/troubleshooting.php'));
$PAGE->set_title(get_string('troubleshooting:title', 'local_appcrue'));
$PAGE->set_heading(get_string('troubleshooting:title', 'local_appcrue'));
// Maximum distinct diagnostic result types: versions (3), logs (3), IdP (3),
// configuration (9), user and endpoints (7), push test (1), MFA (1), and message_appcrue (7).
$maxchecks = 34;

$eastereggdata = json_encode([
    'image' => (new moodle_url('/local/appcrue/pix/JP_AppCrue_problemas.jpg'))->out(false),
    'alt' => get_string('troubleshooting:easteregg_alt', 'local_appcrue'),
    'message' => get_string('troubleshooting:easteregg_message', 'local_appcrue'),
    'close' => get_string('troubleshooting:easteregg_close', 'local_appcrue'),
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$PAGE->requires->js_init_code(<<<JS
(function() {
    const content = $eastereggdata;
    const selector = '.local-appcrue-installed-version';
    let clicks = 0;

    const openDialog = () => {
        const dialog = document.createElement('dialog');
        dialog.className = 'p-4 rounded border-0 shadow';
        dialog.style.maxWidth = 'min(90vw, 560px)';
        const image = document.createElement('img');
        image.src = content.image;
        image.alt = content.alt;
        image.className = 'img-fluid rounded mb-3 d-block mx-auto';
        image.style.maxHeight = '60vh';
        const message = document.createElement('p');
        message.textContent = content.message;
        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn btn-primary';
        close.textContent = content.close;
        close.addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => dialog.remove());
        dialog.append(image, message, close);
        document.body.appendChild(dialog);
        dialog.showModal();
    };

    const countClick = (target) => {
        if (!(target instanceof Element) || !target.closest(selector)) {
            return;
        }
        clicks++;
        if (clicks === 5) {
            clicks = 0;
            openDialog();
        }
    };

    document.addEventListener('click', (event) => countClick(event.target));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            if (event.target instanceof Element && event.target.closest(selector)) {
                event.preventDefault();
                countClick(event.target);
            }
        }
    });
})()
JS);

/**
 * Read and validate the optional log cutoff in the administrator's timezone.
 *
 * @return array Cutoff timestamp, HTML input value and validity flag.
 */
function local_appcrue_troubleshooting_log_filter(): array {
    $value = optional_param('logsince', '', PARAM_RAW_TRIMMED);
    if ($value === '') {
        return ['since' => time() - (30 * DAYSECS), 'value' => '', 'valid' => true];
    }
    $since = \local_appcrue\troubleshooting_helper::parse_log_since($value);
    if ($since === null) {
        return ['since' => time() - (30 * DAYSECS), 'value' => '', 'valid' => false];
    }
    return ['since' => $since, 'value' => $value, 'valid' => true];
}

/**
 * Keep the selected log cutoff when a troubleshooting action reloads the page.
 *
 * @return moodle_url Troubleshooting page URL.
 */
function local_appcrue_troubleshooting_page_url(): moodle_url {
    $filter = local_appcrue_troubleshooting_log_filter();
    $params = $filter['value'] === '' ? [] : ['logsince' => $filter['value']];
    if (optional_param('fragment', 0, PARAM_BOOL)) {
        $params['fragment'] = 1;
    }
    return new moodle_url('/local/appcrue/troubleshooting.php', $params);
}

/**
 * Build a small POST form for an explicit administrator action.
 *
 * @param string $action Action name.
 * @param string $label Button label.
 * @param array $parameters Additional hidden fields.
 * @param string $buttonclass Button CSS classes.
 * @return string HTML form.
 */
function local_appcrue_troubleshooting_post_button(
    string $action,
    string $label,
    array $parameters = [],
    string $buttonclass = 'btn btn-secondary'
): string {
    $html = html_writer::start_tag('form', [
        'method' => 'post',
        'action' => local_appcrue_troubleshooting_page_url()->out(false),
        'class' => 'local-appcrue-troubleshooting-action mb-2',
    ]);
    $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    $html .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => $action]);
    foreach ($parameters as $name => $value) {
        $html .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $name,
            'value' => $value,
        ]);
    }
    $html .= html_writer::tag('button', s($label), ['type' => 'submit', 'class' => $buttonclass]);
    $html .= html_writer::end_tag('form');
    return $html;
}

/**
 * Explain the meaning of a diagnostic result.
 *
 * @param string $title Diagnostic title.
 * @return string Interpretation text.
 */
function local_appcrue_troubleshooting_interpretation(string $title): string {
    global $messageplugininstalled, $messageplugincodeavailable;

    $interpretations = [
        get_string('troubleshooting:versions', 'local_appcrue') => 'versions',
        get_string('troubleshooting:logs', 'local_appcrue') => 'logs',
        get_string('troubleshooting:lastsuccessfulautologin', 'local_appcrue') => 'logs',
        get_string('troubleshooting:log_bad_apikey', 'local_appcrue') => 'badapikey',
        get_string('troubleshooting:key', 'local_appcrue') => 'apikey',
        get_string('troubleshooting:networks', 'local_appcrue') => 'networks',
        get_string('troubleshooting:extra_networks', 'local_appcrue') => 'extranetworks',
        get_string('troubleshooting:localtestips', 'local_appcrue') => 'extranetworks',
        get_string('troubleshooting:keyrotation', 'local_appcrue') => 'rotation',
        get_string('troubleshooting:clientip', 'local_appcrue') => 'ipdiagnostics',
        get_string('troubleshooting:proxyheaders', 'local_appcrue') => 'ipdiagnostics',
        get_string('troubleshooting:autoconfig', 'local_appcrue') => 'autoconfig',
        get_string('troubleshooting:idpcredentials', 'local_appcrue') => 'idpdiagnostics',
        get_string('troubleshooting:idp_environment', 'local_appcrue') => 'idpdiagnostics',
        get_string('troubleshooting:idpresponse', 'local_appcrue') => 'idpdiagnostics',
        get_string('troubleshooting:mfa', 'local_appcrue') => 'mfa',
        get_string('troubleshooting:apitest', 'local_appcrue') => 'apitest',
        get_string('troubleshooting:testuser', 'local_appcrue') => 'testuser',
        'local_appcrue' => 'versions',
        'message_appcrue' => 'versions',
    ];
    if ($messageplugininstalled && $messageplugincodeavailable) {
        $interpretations[get_string('pluginname', 'message_appcrue')] = 'messageprocessor';
        $interpretations[get_string('enable_push', 'message_appcrue')] = 'messagepush';
        $interpretations[get_string('api_key', 'message_appcrue')] = 'messagecredentials';
        $interpretations[get_string('app_id', 'message_appcrue')] = 'messagecredentials';
        $interpretations[get_string('match_user_by', 'message_appcrue')] = 'messageuserfield';
        $interpretations[get_string('bufferedmode', 'message_appcrue')] = 'messagebuffer';
        $interpretations[get_string('testconnection', 'message_appcrue')] = 'messagetest';
    }
    $endpointtitles = [
        'lmsappcrue:calendar:enable_calendar',
        'lmsappcrue:enable_forums',
        'lmsappcrue:enable_grades',
        'lmsappcrue:enable_announcements',
        'lmsappcrue:enable_files',
        'lmsappcrue:enable_assignments',
    ];
    foreach ($endpointtitles as $stringkey) {
        $interpretations[get_string($stringkey, 'local_appcrue')] = 'endpoint';
    }
    foreach (['calendar', 'forums', 'grades', 'announcements', 'files', 'assignments'] as $endpoint) {
        $interpretations[get_string('troubleshooting:endpointtesttitle', 'local_appcrue', $endpoint)] = 'endpointtest';
        $interpretations[get_string('troubleshooting:endpointtitle', 'local_appcrue', $endpoint)] = 'endpoint';
    }
    $key = $interpretations[$title] ?? 'general';
    return get_string('troubleshooting:interpretation_' . $key, 'local_appcrue');
}

/**
 * Render the collapsible interpretation for a diagnostic.
 *
 * @param string $title Diagnostic title.
 * @return string HTML panel.
 */
function local_appcrue_troubleshooting_interpretation_panel(string $title): string {
    $content = html_writer::tag(
        'summary',
        get_string('troubleshooting:interpretation_label', 'local_appcrue')
    );
    $content .= html_writer::div(
        html_writer::tag('p', s(local_appcrue_troubleshooting_interpretation($title))),
        'local-appcrue-interpretation-content'
    );
    return html_writer::tag('details', $content, ['class' => 'local-appcrue-interpretation mt-2']);
}

/**
 * Return the settings page and anchor that control a diagnostic.
 *
 * @param string $title Diagnostic title.
 * @return moodle_url|null Direct link to the setting, when one exists.
 */
function local_appcrue_troubleshooting_setting_url(string $title): ?moodle_url {
    global $messageplugininstalled, $messageplugincodeavailable;

    $settings = [
        get_string('troubleshooting:log_bad_apikey', 'local_appcrue') => 'api_key',
        get_string('troubleshooting:key', 'local_appcrue') => 'api_key',
        get_string('troubleshooting:networks', 'local_appcrue') => 'api_authorized_networks',
        get_string('troubleshooting:extra_networks', 'local_appcrue') => 'api_authorized_networks',
        get_string('troubleshooting:localtestips', 'local_appcrue') => 'api_authorized_networks',
        get_string('troubleshooting:clientip', 'local_appcrue') => 'api_authorized_networks',
        get_string('troubleshooting:keyrotation', 'local_appcrue') => 'lmsappcrue_enable_keyrotation',
        get_string('troubleshooting:autoconfig', 'local_appcrue') => 'lmsappcrue_enable_autoconfig',
        get_string('troubleshooting:idpcredentials', 'local_appcrue') =>
            trim((string)get_config('local_appcrue', 'appcrue_appid')) === '' ? 'appcrue_appid' : 'appcrue_apptoken',
        get_string('troubleshooting:idp_environment', 'local_appcrue') => 'use_pre_server',
        get_string('troubleshooting:idpresponse', 'local_appcrue') => 'use_pre_server',
        get_string('troubleshooting:testuser', 'local_appcrue') => 'lmsappcrue_match_user_by',
        get_string('troubleshooting:mfa', 'local_appcrue') => 'enable_autologin',
        get_string('troubleshooting:apitest', 'local_appcrue') => 'api_authorized_networks',
    ];
    foreach (['calendar', 'forums', 'grades', 'announcements', 'files', 'assignments'] as $endpoint) {
        $setting = 'lmsappcrue_enable_' . $endpoint;
        $settings[get_string('troubleshooting:endpointtitle', 'local_appcrue', $endpoint)] = $setting;
        $settings[get_string('troubleshooting:endpointtesttitle', 'local_appcrue', $endpoint)] = $setting;
    }
    if (isset($settings[$title])) {
        $url = new moodle_url('/admin/settings.php', ['section' => 'local_appcrue']);
        $url->set_anchor('admin-' . $settings[$title]);
        return $url;
    }

    if ($messageplugininstalled && $messageplugincodeavailable) {
        $messagesettings = [
            get_string('enable_push', 'message_appcrue') => 'enable_push',
            get_string('api_key', 'message_appcrue') => 'apikey',
            get_string('app_id', 'message_appcrue') => 'appid',
            get_string('match_user_by', 'message_appcrue') => 'match_user_by',
            get_string('bufferedmode', 'message_appcrue') => 'bufferedmode',
            get_string('testconnection', 'message_appcrue') => 'testconnection',
            get_string('troubleshooting:messagetest_title', 'local_appcrue') => 'testconnection',
        ];
        if (isset($messagesettings[$title])) {
            $url = new moodle_url('/admin/settings.php', ['section' => 'messagesettingappcrue']);
            $url->set_anchor('admin-' . $messagesettings[$title]);
            return $url;
        }
    }
    return null;
}

/**
 * Render an icon link to the setting that controls a diagnostic.
 *
 * @param string $title Diagnostic title.
 * @return string Icon link, or an empty string if there is no setting.
 */
function local_appcrue_troubleshooting_setting_link(string $title): string {
    global $OUTPUT;

    $url = local_appcrue_troubleshooting_setting_url($title);
    if ($url === null) {
        return '';
    }
    $label = get_string('troubleshooting:opensetting', 'local_appcrue', $title);
    return html_writer::link($url, $OUTPUT->pix_icon('i/settings', '', 'moodle'), [
        'class' => 'btn btn-sm btn-outline-secondary me-2',
        'title' => $label,
        'aria-label' => $label,
    ]);
}

/**
 * Render one diagnostic result.
 *
 * @param string $title Check name.
 * @param string $message Explanation.
 * @param string $severity Diagnostic status severity.
 * @param string $action Optional action HTML.
 * @param string $titleprefix Optional unbolded title prefix.
 * @param bool $trustedhtml Whether the message is already escaped HTML made by this page.
 * @return string HTML result.
 */
function local_appcrue_troubleshooting_result(
    string $title,
    string $message,
    string $severity = 'info',
    string $action = '',
    string $titleprefix = '',
    bool $trustedhtml = false
): string {
    $badges = [
        'success' => ['text' => get_string('statusok'), 'class' => 'badge bg-success text-white'],
        'warning' => ['text' => get_string('check'), 'class' => 'badge bg-warning text-dark'],
        'danger' => ['text' => get_string('check'), 'class' => 'badge bg-danger text-white'],
        'info' => [
            'text' => get_string('troubleshooting:statusinfo', 'local_appcrue'),
            'class' => 'badge bg-info text-dark',
        ],
    ];
    $badge = $badges[$severity] ?? $badges['info'];
    $html = local_appcrue_troubleshooting_setting_link($title);
    $html .= html_writer::span(s($badge['text']), $badge['class']) . ' ';
    $html .= $titleprefix !== '' ? html_writer::tag('span', s($titleprefix)) : '';
    $html .= html_writer::tag('strong', s($title) . ': ');
    $html .= ' ' . html_writer::tag('span', $trustedhtml ? $message : s($message));
    if ($action !== '') {
        $html .= html_writer::div($action, 'mt-2');
    }
    $html .= local_appcrue_troubleshooting_interpretation_panel($title);
    return html_writer::div($html, 'alert alert-' . $severity . ' p-2 mb-2', ['role' => 'status']);
}

/**
 * Run one enabled endpoint check and prepare its visible summary and JSON panel.
 *
 * @param string $endpoint Endpoint name.
 * @param string $identifier User identifier submitted by the administrator.
 * @param bool $usernotfound Whether the identifier did not match a Moodle user locally.
 * @return array Status text, severity and report HTML.
 */
function local_appcrue_troubleshooting_endpoint_test(
    string $endpoint,
    string $identifier,
    bool $usernotfound = false
): array {
    $endpointresult = \local_appcrue\troubleshooting_helper::run_endpoint_for_user($endpoint, $identifier);
    $json = json_encode(
        $endpointresult['response'],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    if ($json === false) {
        $endpointresult['success'] = false;
        $json = json_encode([
            'success' => false,
            'error' => ['message' => json_last_error_msg()],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    $summary = $endpointresult['success']
        ? \local_appcrue\troubleshooting_helper::summarize_endpoint_response($endpointresult['response'])
        : null;
    $warning = $endpointresult['success'] && (
        $summary === null || $summary['count'] === null || $summary['count'] === 0
    );
    $expectedmissinguser = $usernotfound && !$endpointresult['success']
        && (int)$endpointresult['httpcode'] === 404
        && is_array($endpointresult['response'])
        && (int)($endpointresult['response']['error']['code'] ?? 0)
            === \local_appcrue\appcrue_service::USER_NOT_ENROLLED;
    $severity = $expectedmissinguser ? 'success'
        : (!$endpointresult['success'] ? 'danger' : ($warning ? 'warning' : 'success'));
    $statuskey = $expectedmissinguser ? 'troubleshooting:endpointtestmissinguser'
        : (!$endpointresult['success'] ? 'troubleshooting:endpointtesterror'
            : ($warning ? 'troubleshooting:endpointtestwarning' : 'troubleshooting:endpointtestsuccess'));
    $status = get_string($statuskey, 'local_appcrue');
    if (!empty($endpointresult['httpcode'])) {
        $status .= ' (' . get_string(
            'troubleshooting:endpointtesthttpstatus',
            'local_appcrue',
            (int)$endpointresult['httpcode']
        ) . ')';
    }

    $details = [];
    if ($expectedmissinguser) {
        $details[] = get_string('troubleshooting:endpointtestmissinguserexplanation', 'local_appcrue');
    }
    if ($endpointresult['success'] && $summary !== null) {
        $details[] = $summary['count'] === null
            ? get_string('troubleshooting:endpointtestcountunknown', 'local_appcrue')
            : get_string('troubleshooting:endpointtestcount', 'local_appcrue', $summary['count']);
        if ($summary['from'] !== null && $summary['to'] !== null) {
            $daterange = (object)[
                'from' => userdate($summary['from']),
                'to' => userdate($summary['to']),
            ];
            $details[] = get_string('troubleshooting:endpointtestdaterange', 'local_appcrue', $daterange);
        }
    } else if ($endpointresult['success']) {
        $details[] = get_string('troubleshooting:endpointtestnoitems', 'local_appcrue');
    } else if (
        is_array($endpointresult['response'])
        && isset($endpointresult['response']['error']['message'])
        && is_string($endpointresult['response']['error']['message'])
    ) {
        $details[] = $endpointresult['response']['error']['message'];
    }
    $details[] = isset($endpointresult['responsetime'])
        ? get_string(
            'troubleshooting:endpointtestresponsetime',
            'local_appcrue',
            format_float($endpointresult['responsetime'], 3)
        )
        : get_string('troubleshooting:endpointtestresponsetime_unknown', 'local_appcrue');
    $report = '';
    foreach ($details as $detail) {
        $report .= html_writer::tag('p', s($detail), ['class' => 'local-appcrue-endpoint-summary mb-1']);
    }
    $jsonpanel = html_writer::tag('summary', get_string('troubleshooting:endpointtestjson', 'local_appcrue'));
    $jsonpanel .= html_writer::tag('pre', s($json), ['class' => 'local-appcrue-endpoint-json']);
    $report .= html_writer::tag('details', $jsonpanel, ['class' => 'local-appcrue-endpoint-result mt-2']);
    return ['status' => $status, 'severity' => $severity, 'report' => $report];
}

/**
 * Apply only explicitly requested configuration actions.
 *
 * @param string $action Action name.
 * @return void
 */
function local_appcrue_troubleshooting_apply_action(string $action): void {
    $knownendpoints = ['calendar', 'forums', 'grades', 'announcements', 'files', 'assignments'];
    switch ($action) {
        case 'add_official_ips':
            \local_appcrue\troubleshooting_helper::add_networks(\local_appcrue\appcrue_service::APPCRUE_SERVERS);
            break;
        case 'remove_additional_networks':
            $networks = (string)get_config('local_appcrue', 'api_authorized_networks');
            $trusted = \local_appcrue\troubleshooting_helper::keep_trusted_networks(
                $networks,
                \local_appcrue\appcrue_service::APPCRUE_SERVERS,
                \local_appcrue\troubleshooting_helper::get_local_test_ips()
            );
            set_config('api_authorized_networks', $trusted, 'local_appcrue');
            break;
        case 'allow_browser_ip':
            $adminip = \getremoteaddr('');
            if (!is_string($adminip) || !filter_var($adminip, FILTER_VALIDATE_IP)) {
                throw new moodle_exception('invalidparameter');
            }
            \local_appcrue\troubleshooting_helper::add_local_test_ip($adminip);
            break;
        case 'remove_local_test_ip':
            $adminip = required_param('address', PARAM_RAW_TRIMMED);
            if (!\local_appcrue\troubleshooting_helper::remove_local_test_ip($adminip)) {
                throw new moodle_exception('invalidparameter');
            }
            break;
        case 'enable_keyrotation':
            set_config('lmsappcrue_enable_keyrotation', 1, 'local_appcrue');
            set_config('enable_api_rotation', 1, 'local_appcrue');
            break;
        case 'enable_endpoint':
            $endpoint = required_param('endpoint', PARAM_ALPHA);
            if (!in_array($endpoint, $knownendpoints, true)) {
                throw new moodle_exception('invalidparameter');
            }
            set_config("lmsappcrue_enable_{$endpoint}", 1, 'local_appcrue');
            break;
        case 'sync_mfa':
            \local_appcrue\mfa_helper::update_autologin_exclusion();
            break;
        case 'enable_autoconfig':
            $apikey = (string) get_config('local_appcrue', 'api_key');
            $networks = (string) get_config('local_appcrue', 'api_authorized_networks');
            $missingips = \local_appcrue\troubleshooting_helper::get_missing_addresses(
                $networks,
                \local_appcrue\appcrue_service::APPCRUE_SERVERS
            );
            if ($apikey !== '' || $missingips || \local_appcrue\troubleshooting_helper::has_unrestricted_network($networks)) {
                throw new moodle_exception('invalidparameter');
            }
            set_config('lmsappcrue_enable_autoconfig', 1, 'local_appcrue');
            break;
        case 'disable_autoconfig':
            set_config('lmsappcrue_enable_autoconfig', 0, 'local_appcrue');
            break;
        case 'apply_user_field':
            $field = required_param('field', PARAM_ALPHA);
            $identifier = required_param('identifier', PARAM_RAW_TRIMMED);
            if (!in_array($field, ['email', 'username'], true) || $identifier === '') {
                throw new moodle_exception('invalidparameter');
            }
            try {
                $user = local_appcrue_find_user($field, $identifier);
            } catch (Throwable $e) {
                throw new moodle_exception('invalidparameter');
            }
            if (!$user || !empty($user->deleted) || !empty($user->suspended)) {
                throw new moodle_exception('invalidparameter');
            }
            set_config('lmsappcrue_match_user_by', $field, 'local_appcrue');
            redirect(
                local_appcrue_troubleshooting_page_url(),
                get_string('troubleshooting:userfieldchanged', 'local_appcrue', $field),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
            return;
        case 'refresh_plugin_versions':
            $checker = \core\update\checker::instance();
            if (!$checker->enabled()) {
                redirect(
                    local_appcrue_troubleshooting_page_url(),
                    get_string('troubleshooting:versioncheckdisabled', 'local_appcrue'),
                    null,
                    \core\output\notification::NOTIFY_WARNING
                );
            }
            try {
                $checker->fetch();
                redirect(
                    local_appcrue_troubleshooting_page_url(),
                    get_string('troubleshooting:versionfetchsuccess', 'local_appcrue'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            } catch (Throwable $e) {
                redirect(
                    local_appcrue_troubleshooting_page_url(),
                    get_string('troubleshooting:versionfetchfailed', 'local_appcrue'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }
            return;
        default:
            throw new moodle_exception('invalidparameter');
    }

    redirect(
        local_appcrue_troubleshooting_page_url(),
        get_string('troubleshooting:changesaved', 'local_appcrue'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$fragmentmode = optional_param('fragment', 0, PARAM_BOOL);
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !$fragmentmode) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('troubleshooting:title', 'local_appcrue'));
    echo html_writer::tag('p', get_string('troubleshooting:checkscount', 'local_appcrue', $maxchecks));
    echo html_writer::div(
        html_writer::span('', 'spinner-border spinner-border-sm me-2', ['aria-hidden' => 'true']) .
            s(get_string('troubleshooting:runningtests', 'local_appcrue', $maxchecks)),
        'alert alert-info',
        ['id' => 'local-appcrue-loading', 'role' => 'status', 'aria-live' => 'polite']
    );
    echo html_writer::div('', '', ['id' => 'local-appcrue-report', 'hidden' => 'hidden']);
    $PAGE->requires->js_call_amd('local_appcrue/troubleshooting', 'initPage', [[
        'failed' => get_string('troubleshooting:loadfailed', 'local_appcrue'),
        'retry' => get_string('troubleshooting:retry', 'local_appcrue'),
    ]]);
    echo $OUTPUT->footer();
    exit;
}

$usertestaction = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && data_submitted()) {
    require_sesskey();
    $action = required_param('action', PARAM_ALPHAEXT);
    if (in_array($action, ['check_user', 'send_message_push'], true)) {
        $usertestaction = $action;
    } else {
        local_appcrue_troubleshooting_apply_action($action);
    }
}

$networkconfig = (string) get_config('local_appcrue', 'api_authorized_networks');
$networkentries = preg_split('/\r\n|\r|\n/', $networkconfig, -1, PREG_SPLIT_NO_EMPTY);
$networkentries = array_values(array_filter(array_map('trim', $networkentries)));
$ipdiagnostics = \local_appcrue\troubleshooting_helper::get_request_ip_diagnostics();
$remoteip = $ipdiagnostics['effective'];
$validremoteip = (bool) filter_var($remoteip, FILTER_VALIDATE_IP);
$localtestips = \local_appcrue\troubleshooting_helper::get_local_test_ips();
$additionalnetworks = \local_appcrue\troubleshooting_helper::get_additional_networks(
    $networkconfig,
    \local_appcrue\appcrue_service::APPCRUE_SERVERS,
    $localtestips
);
$unrestrictednetwork = \local_appcrue\troubleshooting_helper::has_unrestricted_network($networkconfig);
$missingofficialips = \local_appcrue\troubleshooting_helper::get_missing_addresses(
    $networkconfig,
    \local_appcrue\appcrue_service::APPCRUE_SERVERS
);
$currentipallowed = $validremoteip && (new \local_appcrue\network_security_helper())->is_request_in_list();
$currentiptemporary = $currentipallowed && !in_array($remoteip, \local_appcrue\appcrue_service::APPCRUE_SERVERS, true);
$cantracklocaltestip = $validremoteip
    && !$currentipallowed
    && !in_array($remoteip, \local_appcrue\appcrue_service::APPCRUE_SERVERS, true)
    && !in_array($remoteip, $localtestips, true);
$apikey = (string) get_config('local_appcrue', 'api_key');
$apikeyvalid = $apikey !== '' && clean_param($apikey, PARAM_ALPHANUMEXT) === $apikey;
$pluginmanager = core_plugin_manager::instance();
$localplugininfo = $pluginmanager->get_plugin_info('local_appcrue');
$messageplugininfo = $pluginmanager->get_plugin_info('message_appcrue');
$messageplugininstalled = $messageplugininfo && $messageplugininfo->versiondb !== null;
$messageplugincodeavailable = $messageplugininfo && $messageplugininfo->versiondisk !== null;

$endpointsettings = [
    'calendar' => 'lmsappcrue:calendar:enable_calendar',
    'forums' => 'lmsappcrue:enable_forums',
    'grades' => 'lmsappcrue:enable_grades',
    'announcements' => 'lmsappcrue:enable_announcements',
    'files' => 'lmsappcrue:enable_files',
    'assignments' => 'lmsappcrue:enable_assignments',
];
$enabledendpoint = '';
foreach ($endpointsettings as $endpoint => $stringkey) {
    if ((string) get_config('local_appcrue', "lmsappcrue_enable_{$endpoint}") === '1') {
        $enabledendpoint = $endpoint;
        break;
    }
}
$testurl = $enabledendpoint !== ''
    ? (new moodle_url('/local/appcrue/appcrue.php/' . $enabledendpoint))->out(false)
    : '';
$autoconfigenabled = (bool) get_config('local_appcrue', 'lmsappcrue_enable_autoconfig');

$useridentifier = '';
$userresult = null;
$usercandidate = null;
if (in_array($usertestaction, ['check_user', 'send_message_push'], true)) {
    $useridentifier = optional_param('identifier', '', PARAM_RAW_TRIMMED);
    $currentfield = get_config('local_appcrue', 'lmsappcrue_match_user_by') ?: 'email';
    if ($useridentifier !== '') {
        try {
            $userresult = local_appcrue_find_user($currentfield, $useridentifier);
        } catch (Throwable $e) {
            $userresult = false;
        }
        if (!$userresult) {
            foreach (['email', 'username'] as $candidatefield) {
                if ($candidatefield === $currentfield) {
                    continue;
                }
                try {
                    $candidate = local_appcrue_find_user($candidatefield, $useridentifier);
                } catch (Throwable $e) {
                    $candidate = false;
                }
                if ($candidate && empty($candidate->deleted) && empty($candidate->suspended)) {
                    $usercandidate = $candidatefield;
                    break;
                }
            }
        }
    }
}

if (!$fragmentmode) {
    echo $OUTPUT->header();
}
echo html_writer::start_tag('div', ['id' => 'local-appcrue-report-fragment']);
if ($fragmentmode) {
    foreach (\core\notification::fetch() as $notification) {
        echo $OUTPUT->render_from_template(
            $notification->get_template_name(),
            $notification->export_for_template($OUTPUT)
        );
    }
}
if (!$fragmentmode) {
    echo $OUTPUT->heading(get_string('troubleshooting:title', 'local_appcrue'));
    echo html_writer::tag('p', get_string('troubleshooting:checkscount', 'local_appcrue', $maxchecks));
}
echo $OUTPUT->heading(get_string('troubleshooting:versions', 'local_appcrue'), 3);
$updateschecker = \core\update\checker::instance();
$lastupdatescheck = $updateschecker->get_last_timefetched();
echo html_writer::tag('p', get_string('troubleshooting:versioncheckintro', 'local_appcrue'));
if ($updateschecker->enabled()) {
    echo local_appcrue_troubleshooting_post_button(
        'refresh_plugin_versions',
        get_string('troubleshooting:versionrefresh', 'local_appcrue')
    );
} else {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:versions', 'local_appcrue'),
        get_string('troubleshooting:versioncheckdisabled', 'local_appcrue'),
        'warning'
    );
}
$versionplugins = [
    'local_appcrue' => $localplugininfo,
    'message_appcrue' => $messageplugininfo,
];
foreach ($versionplugins as $component => $plugininfo) {
    $installedversion = $plugininfo->versiondb ?? null;
    $availableversion = $plugininfo->versiondisk ?? null;
    $release = $plugininfo->release ?? get_string('troubleshooting:version_norelease', 'local_appcrue');
    $versiondata = (object)[
        'available' => $availableversion === null
            ? get_string('none') : (string)$availableversion,
        'release' => $release,
    ];
    if ($plugininfo && $availableversion !== null) {
        $latestupdate = \local_appcrue\troubleshooting_helper::get_latest_remote_update(
            $plugininfo->available_updates()
        );
    } else {
        $latestupdate = null;
    }
    $versiondata->code = $versiondata->available;
    $versiondata->remoterelease = $latestupdate && $latestupdate->release
        ? $latestupdate->release
        : get_string('troubleshooting:version_norelease', 'local_appcrue');
    $versiondata->remote = $latestupdate ? (string)$latestupdate->version : '';
    if (!$plugininfo || $availableversion === null) {
        $severity = 'warning';
        $versionmessage = get_string('troubleshooting:version_unavailable', 'local_appcrue');
    } else if ($latestupdate) {
        $severity = 'warning';
        $versionmessage = get_string('troubleshooting:version_update_found', 'local_appcrue', $versiondata);
    } else if (!$updateschecker->enabled()) {
        $severity = 'warning';
        $versionmessage = get_string('troubleshooting:version_check_disabled_item', 'local_appcrue', $versiondata);
    } else if (!$lastupdatescheck) {
        $severity = 'warning';
        $versionmessage = get_string('troubleshooting:version_not_checked', 'local_appcrue', $versiondata);
    } else {
        $severity = 'success';
        $versiondata->checked = userdate($lastupdatescheck);
        $versionmessage = get_string('troubleshooting:version_no_update', 'local_appcrue', $versiondata);
    }
    $installeddisplay = $installedversion === null
        ? s(get_string('none'))
        : html_writer::tag('span', s((string)$installedversion), [
            'class' => 'local-appcrue-installed-version',
            'role' => 'button',
            'tabindex' => '0',
            'style' => 'cursor: pointer',
        ]);
    $versionhtml = s(get_string('troubleshooting:version_installed', 'local_appcrue')) . ' ' .
        $installeddisplay . '. ' . s($versionmessage);
    echo local_appcrue_troubleshooting_result($component, $versionhtml, $severity, '', '', true);
}
echo $OUTPUT->notification(get_string('troubleshooting:intro', 'local_appcrue'), \core\output\notification::NOTIFY_INFO);

echo $OUTPUT->heading(get_string('troubleshooting:logs', 'local_appcrue'), 3);
$logfilter = local_appcrue_troubleshooting_log_filter();
$logsince = $logfilter['since'];
$logform = html_writer::start_tag('form', [
    'method' => 'get',
    'action' => (new moodle_url('/local/appcrue/troubleshooting.php'))->out(false),
    'class' => 'mb-3',
]);
$logform .= html_writer::label(get_string('troubleshooting:logsince', 'local_appcrue'), 'local-appcrue-logsince');
$logform .= html_writer::empty_tag('input', [
    'type' => 'datetime-local',
    'id' => 'local-appcrue-logsince',
    'name' => 'logsince',
    'value' => $logfilter['value'],
    'class' => 'form-control mb-2',
]);
$logform .= html_writer::tag('p', get_string(
    'troubleshooting:logsince_timezone',
    'local_appcrue',
    \core_date::get_user_timezone()
), ['class' => 'form-text']);
$logform .= html_writer::tag('button', get_string('troubleshooting:logsince_apply', 'local_appcrue'), [
    'type' => 'submit',
    'class' => 'btn btn-secondary me-2',
]);
if ($logfilter['value'] !== '') {
    $logform .= html_writer::link(
        new moodle_url('/local/appcrue/troubleshooting.php'),
        get_string('troubleshooting:logsince_clear', 'local_appcrue'),
        ['class' => 'btn btn-outline-secondary']
    );
}
$logform .= html_writer::end_tag('form');
echo $logform;
if (!$logfilter['valid']) {
    echo $OUTPUT->notification(
        get_string('troubleshooting:logsince_invalid', 'local_appcrue'),
        \core\output\notification::NOTIFY_WARNING
    );
}
$logintro = $logfilter['value'] === ''
    ? get_string('troubleshooting:logsintro', 'local_appcrue')
    : get_string('troubleshooting:logsintro_custom', 'local_appcrue', userdate($logsince));
echo html_writer::tag('p', $logintro);
$logsummary = \local_appcrue\troubleshooting_helper::get_recent_log_summary($logsince);
if ($logsummary !== null && $logsummary['lastsuccessfulautologin'] !== null) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:lastsuccessfulautologin', 'local_appcrue'),
        get_string(
            'troubleshooting:lastsuccessfulautologin_found',
            'local_appcrue',
            userdate($logsummary['lastsuccessfulautologin'])
        ),
        'success'
    );
}
if ($logsummary === null) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:logs', 'local_appcrue'),
        get_string('troubleshooting:logunavailable', 'local_appcrue'),
        'warning'
    );
} else if (!$logsummary['counts']) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:logs', 'local_appcrue'),
        get_string('troubleshooting:lognone', 'local_appcrue'),
        'info'
    );
} else {
    $eventlabels = [
        '\\local_appcrue\\event\\api_accessed' => 'troubleshooting:log_api_accessed',
        '\\local_appcrue\\event\\bad_apikey_failed' => 'troubleshooting:log_bad_apikey',
        '\\local_appcrue\\event\\unauthorized_ip_failed' => 'troubleshooting:log_unauthorized_ip',
        '\\local_appcrue\\event\\autologin_login' => 'troubleshooting:log_autologin_login',
        '\\local_appcrue\\event\\autologin_failed' => 'troubleshooting:log_autologin_failed',
    ];
    $userids = [];
    foreach ($logsummary['activities'] as $activitysummary) {
        $userids = array_merge($userids, $activitysummary['users']['values']);
    }
    $userids = array_values(array_unique(array_map('intval', $userids)));
    $users = $userids ? $DB->get_records_list(
        'user',
        'id',
        $userids,
        '',
        'id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename'
    ) : [];
    $reportlines = [];
    foreach ($eventlabels as $eventname => $stringkey) {
        $activity = $logsummary['activities'][$eventname];
        $reportlines[] = get_string($stringkey, 'local_appcrue') . ': ' . ($logsummary['counts'][$eventname] ?? 0);
        $usernames = array_map(
            static function (int $userid) use ($users): string {
                return isset($users[$userid]) ? fullname($users[$userid]) : '#' . $userid;
            },
            $activity['users']['values']
        );
        $reportlines[] = '  ' . get_string('troubleshooting:log_users', 'local_appcrue') . ': ' .
            (implode(', ', $usernames) ?: get_string('none'));
        $reportlines[] = '  ' . get_string('troubleshooting:log_ips', 'local_appcrue') . ': ' .
            (implode(', ', $activity['ips']['values']) ?: get_string('none'));
        if ($activity['users']['truncated'] || $activity['ips']['truncated']) {
            $reportlines[] = '  ' . get_string('troubleshooting:log_list_truncated', 'local_appcrue');
        }
        $reportlines[] = '';
    }
    $badkeycount = $logsummary['counts']['\\local_appcrue\\event\\bad_apikey_failed'] ?? 0;
    if ($badkeycount > 0) {
        $officialips = \local_appcrue\appcrue_service::APPCRUE_SERVERS;
        $criticalips = array_map(static function ($record): string {
            return (string)$record->ip . ' (' . (int)$record->total . ')';
        }, $logsummary['badkeyofficialips']);
        $technicianips = [];
        $officialcount = $logsummary['badkeyofficialtotal'];
        foreach ($logsummary['activities']['\\local_appcrue\\event\\bad_apikey_failed']['ips']['values'] as $ip) {
            if (!in_array($ip, $officialips, true)) {
                $technicianips[] = $ip;
            }
        }
        $reportlines[] = '';
        $reportlines[] = get_string(
            'troubleshooting:log_badkey_source_summary',
            'local_appcrue',
            (object)[
                'official' => $officialcount,
                'other' => max(0, $badkeycount - $officialcount),
                'officialips' => implode(', ', $criticalips) ?: get_string('none'),
                'otherips' => implode(', ', $technicianips) ?: get_string('none'),
            ]
        );
        if ($officialcount > 0) {
            echo local_appcrue_troubleshooting_result(
                get_string('troubleshooting:log_bad_apikey', 'local_appcrue'),
                get_string('troubleshooting:log_badkey_official_critical', 'local_appcrue'),
                'danger',
                '',
                get_string('troubleshooting:log_badkey_prefix', 'local_appcrue')
            );
        } else {
            echo local_appcrue_troubleshooting_result(
                get_string('troubleshooting:log_bad_apikey', 'local_appcrue'),
                get_string('troubleshooting:log_badkey_other_info', 'local_appcrue'),
                'info',
                '',
                get_string('troubleshooting:log_badkey_prefix', 'local_appcrue')
            );
        }
    }
    echo html_writer::tag('pre', s(implode("\n", $reportlines)), ['class' => 'local-appcrue-log-summary']);
    echo local_appcrue_troubleshooting_interpretation_panel(
        get_string('troubleshooting:logs', 'local_appcrue')
    );
}

echo $OUTPUT->heading(get_string('troubleshooting:checks', 'local_appcrue'), 3);

if (!get_config('local_appcrue', 'use_custom_idp')) {
    $appid = trim((string)get_config('local_appcrue', 'appcrue_appid'));
    $apptoken = trim((string)get_config('local_appcrue', 'appcrue_apptoken'));
    if ($appid === '' || $apptoken === '') {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:idpcredentials', 'local_appcrue'),
            get_string('troubleshooting:idpcredentials_missing', 'local_appcrue'),
            'danger'
        );
    } else {
        $probe = \local_appcrue\troubleshooting_helper::probe_universia_idp();
        $probedata = (object)[
            'code' => $probe['httpcode'],
            'seconds' => format_float($probe['duration'], 3),
            'curlerror' => $probe['curlerror'],
        ];
        if ($probe['httpcode'] === 406 && $probe['curlerror'] === 0) {
            $probemessage = get_string('troubleshooting:idp_probe_expected', 'local_appcrue', $probedata);
            $probeseverity = 'success';
        } else if ($probe['httpcode'] === 403 && $probe['curlerror'] === 0) {
            $otherusepre = !(bool)get_config('local_appcrue', 'use_pre_server');
            $otherprobe = \local_appcrue\troubleshooting_helper::probe_universia_idp($otherusepre);
            if ($otherprobe['httpcode'] === 406 && $otherprobe['curlerror'] === 0) {
                $probemessage = get_string(
                    'troubleshooting:idp_probe_wrong_environment',
                    'local_appcrue',
                    $otherusepre ? 'PRE' : 'PRO'
                );
                $probeseverity = 'warning';
            } else if ($otherprobe['httpcode'] === 403 && $otherprobe['curlerror'] === 0) {
                $probemessage = get_string('troubleshooting:idp_probe_both_rejected', 'local_appcrue');
                $probeseverity = 'danger';
            } else {
                $otherprobedata = (object)[
                    'code' => $otherprobe['httpcode'],
                    'curlerror' => $otherprobe['curlerror'],
                ];
                $probemessage = get_string('troubleshooting:idp_probe_other_failed', 'local_appcrue', $otherprobedata);
                $probeseverity = 'danger';
            }
        } else if ($probe['httpcode'] === 0 || $probe['curlerror'] !== 0) {
            $probemessage = get_string('troubleshooting:idp_probe_unreachable', 'local_appcrue', $probedata);
            $probeseverity = 'danger';
        } else {
            $probemessage = get_string('troubleshooting:idp_probe_unexpected', 'local_appcrue', $probedata);
            $probeseverity = $probe['httpcode'] >= 500 || $probe['httpcode'] === 200 ? 'danger' : 'warning';
        }
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:idpcredentials', 'local_appcrue'),
            get_string('troubleshooting:idpcredentials_present', 'local_appcrue') . ' ' . $probemessage,
            $probeseverity
        );
    }

    if (!get_config('local_appcrue', 'use_pre_server') && stripos($appid, 'pre') !== false) {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:idp_environment', 'local_appcrue'),
            get_string('troubleshooting:idp_pre_hint', 'local_appcrue'),
            'warning'
        );
    }

    $idp406time = \local_appcrue\troubleshooting_helper::get_recent_idp_status(406, $logsince);
    if ($idp406time === null) {
        $idpmessage = get_string('troubleshooting:idp_log_unavailable', 'local_appcrue');
        $idpseverity = 'warning';
    } else if ($idp406time !== false) {
        $idpmessage = get_string(
            'troubleshooting:idp406',
            'local_appcrue',
            userdate($idp406time)
        );
        $idpseverity = 'warning';
    } else {
        $idpmessage = get_string('troubleshooting:idp406_not_found', 'local_appcrue', userdate($logsince));
        $idpseverity = 'info';
    }
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:idpresponse', 'local_appcrue'),
        $idpmessage,
        $idpseverity
    );
}

if (!$apikey) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:key', 'local_appcrue'),
        get_string('troubleshooting:keymissing', 'local_appcrue'),
        'warning'
    );
} else if (!$apikeyvalid) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:key', 'local_appcrue'),
        get_string('troubleshooting:keyinvalid', 'local_appcrue'),
        'danger'
    );
} else {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:key', 'local_appcrue'),
        get_string('troubleshooting:keyok', 'local_appcrue'),
        'success'
    );
}

$rotationtime = (int)get_config('local_appcrue', 'api_key_last_rotation');
$rotationendpointenabled = (bool)get_config('local_appcrue', 'lmsappcrue_enable_keyrotation');
$rotationserviceenabled = (bool)get_config('local_appcrue', 'enable_api_rotation');
$rotationdata = (object)[
    'date' => $rotationtime ? userdate($rotationtime) : get_string('none'),
    'endpoint' => get_string(
        $rotationendpointenabled ? 'troubleshooting:rotation_enabled' : 'troubleshooting:rotation_disabled',
        'local_appcrue'
    ),
    'service' => get_string(
        $rotationserviceenabled ? 'troubleshooting:rotation_enabled' : 'troubleshooting:rotation_disabled',
        'local_appcrue'
    ),
];
$rotationok = $rotationendpointenabled && $rotationserviceenabled;
echo local_appcrue_troubleshooting_result(
    get_string('troubleshooting:keyrotation', 'local_appcrue'),
    get_string(
        $rotationtime ? 'troubleshooting:rotation_seen' : 'troubleshooting:rotation_not_seen',
        'local_appcrue',
        $rotationdata
    ),
    $rotationok ? ($rotationtime ? 'success' : 'info') : 'warning',
    $rotationok ? '' : local_appcrue_troubleshooting_post_button(
        'enable_keyrotation',
        get_string('troubleshooting:rotation_enable_button', 'local_appcrue')
    )
);

if (!$networkentries) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:networks', 'local_appcrue'),
        get_string('troubleshooting:networksempty', 'local_appcrue'),
        'danger',
        local_appcrue_troubleshooting_post_button(
            'add_official_ips',
            get_string('troubleshooting:addofficial', 'local_appcrue')
        )
    );
} else if ($unrestrictednetwork) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:networks', 'local_appcrue'),
        get_string('troubleshooting:networkunrestricted', 'local_appcrue'),
        'danger'
    );
} else if ($missingofficialips) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:networks', 'local_appcrue'),
        get_string('troubleshooting:officialmissing', 'local_appcrue', implode(', ', $missingofficialips)),
        'warning',
        local_appcrue_troubleshooting_post_button(
            'add_official_ips',
            get_string('troubleshooting:addofficial', 'local_appcrue')
        )
    );
} else {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:networks', 'local_appcrue'),
        get_string('troubleshooting:officialpresent', 'local_appcrue'),
        'success'
    );
}

if ($additionalnetworks) {
    $additionalmessage = get_string(
        'troubleshooting:extra_networks_critical',
        'local_appcrue',
        implode(', ', $additionalnetworks)
    );
    if (in_array($remoteip, $additionalnetworks, true)) {
        $additionalmessage .= ' ' . get_string('troubleshooting:extra_networks_browser', 'local_appcrue', $remoteip);
    }
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:extra_networks', 'local_appcrue'),
        $additionalmessage,
        'danger',
        local_appcrue_troubleshooting_post_button(
            'remove_additional_networks',
            get_string('troubleshooting:correct_networks', 'local_appcrue'),
            [],
            'btn btn-danger'
        )
    );
} else {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:extra_networks', 'local_appcrue'),
        get_string('troubleshooting:extra_networks_ok', 'local_appcrue'),
        'success'
    );
}
echo html_writer::start_tag('section');
echo $OUTPUT->heading(get_string('troubleshooting:apitest', 'local_appcrue'), 4);
if ($autoconfigenabled) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:apitest', 'local_appcrue'),
        get_string('troubleshooting:apitestautoconfig', 'local_appcrue'),
        'warning'
    );
} else if ($testurl === '') {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:apitest', 'local_appcrue'),
        get_string('troubleshooting:noendpoint', 'local_appcrue'),
        'warning'
    );
} else {
    echo html_writer::tag('p', s($testurl));
    echo html_writer::tag('button', get_string('troubleshooting:apitestbutton', 'local_appcrue'), [
        'id' => 'local-appcrue-api-test-button',
        'type' => 'button',
        'class' => 'btn btn-secondary mb-2',
    ]);
    echo html_writer::div(get_string('troubleshooting:statusinfo', 'local_appcrue'), '', [
        'id' => 'local-appcrue-api-test-result',
        'role' => 'status',
        'aria-live' => 'polite',
    ]);
    $teststrings = [
        'passed' => get_string('troubleshooting:apitestpassed', 'local_appcrue'),
        'denied' => get_string('troubleshooting:apitestdenied', 'local_appcrue'),
        'notfound' => get_string('troubleshooting:apitestnotfound', 'local_appcrue'),
        'unexpected' => get_string('troubleshooting:apitestunexpected', 'local_appcrue'),
        'networkerror' => get_string('troubleshooting:apitestnetworkerror', 'local_appcrue'),
    ];
    if ($fragmentmode) {
        echo html_writer::div('', '', [
            'id' => 'local-appcrue-api-test-config',
            'data-url' => $testurl,
            'data-strings' => json_encode($teststrings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        ]);
    } else {
        $PAGE->requires->js_call_amd('local_appcrue/troubleshooting', 'init', [$testurl, $teststrings]);
    }
}

echo html_writer::tag('p', get_string('troubleshooting:apitestintro', 'local_appcrue'));
echo html_writer::end_tag('section');
if ($validremoteip) {
    $clientmessage = get_string(
        $currentiptemporary
            ? 'troubleshooting:clientallowed_temporary'
            : ($currentipallowed ? 'troubleshooting:clientallowed' : 'troubleshooting:clientdenied'),
        'local_appcrue',
        $remoteip
    );
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:clientip', 'local_appcrue'),
        $clientmessage,
        $currentiptemporary ? 'danger' : ($currentipallowed ? 'success' : 'warning'),
        !$cantracklocaltestip ? '' : local_appcrue_troubleshooting_post_button(
            'allow_browser_ip',
            get_string('troubleshooting:allow_browser_ip', 'local_appcrue')
        )
    );
} else {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:clientip', 'local_appcrue'),
        get_string('troubleshooting:clientunknown', 'local_appcrue'),
        'warning'
    );
}
$iplines = [
    get_string('troubleshooting:ip_effective', 'local_appcrue') . ': ' . ($ipdiagnostics['effective'] ?: get_string('none')),
    'REMOTE_ADDR: ' . ($ipdiagnostics['remoteaddr'] ?: get_string('none')),
    'HTTP_CLIENT_IP: ' . ($ipdiagnostics['clientip'] ?: get_string('none')),
    'HTTP_X_FORWARDED_FOR: ' . ($ipdiagnostics['forwardedfor'] ?: get_string('none')),
    get_string('troubleshooting:ip_header_order', 'local_appcrue') . ': ' . $ipdiagnostics['selection'],
    'reverseproxyignore: ' . ($ipdiagnostics['reverseproxyignore'] ?: get_string('none')),
];
echo html_writer::tag('pre', s(implode("\n", $iplines)), ['class' => 'local-appcrue-ip-diagnostics']);
if ($ipdiagnostics['selection'] !== 'REMOTE_ADDR only') {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:proxyheaders', 'local_appcrue'),
        get_string('troubleshooting:proxyheaders_review', 'local_appcrue'),
        'warning'
    );
}

foreach ($localtestips as $localtestip) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:localtestips', 'local_appcrue'),
        get_string('troubleshooting:localtestip_value', 'local_appcrue', $localtestip),
        'danger',
        local_appcrue_troubleshooting_post_button(
            'remove_local_test_ip',
            get_string('troubleshooting:remove_local_test_ip', 'local_appcrue'),
            ['address' => $localtestip],
            'btn btn-danger'
        )
    );
}

if ($autoconfigenabled) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:autoconfig', 'local_appcrue'),
        get_string('troubleshooting:autoconfigenabled', 'local_appcrue'),
        'warning',
        local_appcrue_troubleshooting_post_button(
            'disable_autoconfig',
            get_string('troubleshooting:disableautoconfig', 'local_appcrue')
        )
    );
} else if (!$apikey && !$missingofficialips && !$unrestrictednetwork) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:autoconfig', 'local_appcrue'),
        get_string('troubleshooting:autoconfigavailable', 'local_appcrue'),
        'warning',
        local_appcrue_troubleshooting_post_button(
            'enable_autoconfig',
            get_string('troubleshooting:enableautoconfig', 'local_appcrue'),
            [],
            'btn btn-warning'
        )
    );
} else if (!$apikey && $missingofficialips) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:autoconfig', 'local_appcrue'),
        get_string('troubleshooting:autoconfignetworkfirst', 'local_appcrue'),
        'warning'
    );
}

echo $OUTPUT->heading(get_string('troubleshooting:endpoints', 'local_appcrue'), 3);
echo $OUTPUT->heading(get_string('troubleshooting:testuser', 'local_appcrue'), 4);
echo html_writer::tag(
    'p',
    get_string(
        'troubleshooting:currentfield',
        'local_appcrue',
        s(get_config('local_appcrue', 'lmsappcrue_match_user_by') ?: 'email')
    )
);
echo html_writer::tag(
    'p',
    get_string(
        'troubleshooting:currentparam',
        'local_appcrue',
        get_config('local_appcrue', 'lmsappcrue_use_user_param') === 'username' ? 'username' : 'studentemail'
    )
);
echo html_writer::tag('p', get_string('troubleshooting:identifierhelp', 'local_appcrue'));
$userform = html_writer::start_tag('form', [
    'method' => 'post',
    'action' => local_appcrue_troubleshooting_page_url()->out(false),
    'class' => 'mb-3',
]);
$userform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
$userform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'check_user']);
$userform .= html_writer::label(get_string('troubleshooting:identifier', 'local_appcrue'), 'local-appcrue-user-identifier');
$userform .= html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'local-appcrue-user-identifier',
    'name' => 'identifier',
    'value' => $useridentifier,
    'class' => 'form-control mb-2',
    'autocomplete' => 'off',
    'required' => 'required',
]);
$userform .= html_writer::tag('button', get_string('troubleshooting:testuserbutton', 'local_appcrue'), [
    'type' => 'submit',
    'class' => 'btn btn-secondary',
]);
$userform .= html_writer::end_tag('form');
echo $userform;

if ($userresult) {
    $courses = enrol_get_users_courses($userresult->id, true);
    if (!empty($userresult->deleted) || !empty($userresult->suspended)) {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:testuser', 'local_appcrue'),
            get_string('troubleshooting:userinactive', 'local_appcrue'),
            'warning'
        );
    } else {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:testuser', 'local_appcrue'),
            get_string('troubleshooting:userfound', 'local_appcrue', count($courses)),
            count($courses) > 0 ? 'success' : 'warning',
            count($courses) > 0 ? '' : html_writer::tag(
                'p',
                get_string('troubleshooting:usernocourses', 'local_appcrue')
            )
        );
    }
} else if ($useridentifier !== '') {
    if ($usercandidate !== null) {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:testuser', 'local_appcrue'),
            get_string('troubleshooting:usercandidate', 'local_appcrue', $usercandidate),
            'warning',
            html_writer::tag('p', get_string('troubleshooting:applyuserfield', 'local_appcrue', $usercandidate)) .
                local_appcrue_troubleshooting_post_button(
                    'apply_user_field',
                    get_string('troubleshooting:applyuserfield', 'local_appcrue', $usercandidate),
                    ['identifier' => $useridentifier, 'field' => $usercandidate]
                )
        );
    } else {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:testuser', 'local_appcrue'),
            get_string('troubleshooting:usernotfound', 'local_appcrue'),
            'danger'
        );
    }
}

// Always call enabled endpoints for a submitted identifier, including one that does not match a Moodle user.
$canrunendpointtests = $usertestaction === 'check_user' && $useridentifier !== ''
    && (!$userresult || (empty($userresult->deleted) && empty($userresult->suspended)));
if ($canrunendpointtests) {
    echo html_writer::tag('p', get_string('troubleshooting:endpointtestintro', 'local_appcrue'));
}
foreach (array_keys($endpointsettings) as $endpoint) {
    $enabled = (string) get_config('local_appcrue', "lmsappcrue_enable_{$endpoint}") === '1';
    if (!$enabled) {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:endpointtitle', 'local_appcrue', $endpoint),
            get_string('troubleshooting:endpointdisabled', 'local_appcrue', $endpoint),
            'warning',
            local_appcrue_troubleshooting_post_button(
                'enable_endpoint',
                get_string('troubleshooting:enableendpoint', 'local_appcrue', $endpoint),
                ['endpoint' => $endpoint]
            )
        );
        continue;
    }

    $enabledmessage = get_string('troubleshooting:endpointenabled', 'local_appcrue', $endpoint);
    if (!$canrunendpointtests) {
        echo local_appcrue_troubleshooting_result(
            get_string('troubleshooting:endpointtitle', 'local_appcrue', $endpoint),
            $enabledmessage . ' ' . get_string('troubleshooting:endpointnotrequested', 'local_appcrue'),
            'warning'
        );
        continue;
    }

    $test = local_appcrue_troubleshooting_endpoint_test($endpoint, $useridentifier, !$userresult);
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:endpointtesttitle', 'local_appcrue', $endpoint),
        $enabledmessage . ' ' . $test['status'],
        $test['severity'],
        $test['report']
    );
}

if ($userresult) {
    if (empty($userresult->deleted) && empty($userresult->suspended)) {
        if ($messageplugininstalled) {
            $messagetesttitle = get_string('troubleshooting:messagetest_title', 'local_appcrue');
            if (!$messageplugincodeavailable) {
                echo local_appcrue_troubleshooting_result(
                    $messagetesttitle,
                    get_string('troubleshooting:messagetestcodeunavailable', 'local_appcrue'),
                    'warning'
                );
            } else if ($usertestaction !== 'send_message_push') {
                echo local_appcrue_troubleshooting_result(
                    $messagetesttitle,
                    get_string('troubleshooting:messagetestconfirm_prompt', 'local_appcrue'),
                    'warning',
                    local_appcrue_troubleshooting_post_button(
                        'send_message_push',
                        get_string('troubleshooting:messagetestconfirm_button', 'local_appcrue'),
                        ['identifier' => $useridentifier],
                        'btn btn-warning'
                    )
                );
            } else {
                require_once($CFG->dirroot . '/message/output/appcrue/message_output_appcrue.php');
                $messagetest = (new \message_output_appcrue())->send_test_notification($userresult);
                $messagetestseverity = 'danger';
                switch ($messagetest['status']) {
                    case 'sent':
                        $messagetestmessage = get_string(
                            'troubleshooting:messagetest_sent',
                            'local_appcrue',
                            fullname($userresult)
                        );
                        $messagetestseverity = 'success';
                        break;
                    case 'missingconfig':
                        $messagetestmessage = get_string('troubleshooting:messagetest_configmissing', 'local_appcrue');
                        break;
                    case 'noalias':
                        $messagetestmessage = get_string(
                            'troubleshooting:messagetest_noalias',
                            'local_appcrue',
                            (string)get_config('message_appcrue', 'match_user_by')
                        );
                        break;
                    case 'errors':
                        $messagetestmessage = get_string(
                            'troubleshooting:messagetest_deliveryerror',
                            'local_appcrue',
                            implode(', ', array_map('intval', array_keys($messagetest['errors'])))
                        );
                        break;
                    default:
                        $messagetestmessage = get_string(
                            'troubleshooting:messagetest_exception',
                            'local_appcrue',
                            $messagetest['message'] ?? get_string('unknownerror')
                        );
                        break;
                }
                echo local_appcrue_troubleshooting_result(
                    $messagetesttitle,
                    $messagetestmessage,
                    $messagetestseverity
                );
            }
        }
    }
}

echo $OUTPUT->heading(get_string('troubleshooting:mfa', 'local_appcrue'), 3);
if (!(bool) get_config('tool_mfa', 'enabled')) {
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:mfa', 'local_appcrue'),
        get_string('troubleshooting:mfadisable', 'local_appcrue'),
        'info'
    );
} else {
    $mfaok = (bool) get_config('local_appcrue', 'enable_autologin') === \local_appcrue\mfa_helper::has_autologin_exclusion();
    echo local_appcrue_troubleshooting_result(
        get_string('troubleshooting:mfa', 'local_appcrue'),
        get_string(
            $mfaok ? 'troubleshooting:mfaok' : 'troubleshooting:mfamismatch',
            'local_appcrue'
        ),
        $mfaok ? 'success' : 'warning',
        $mfaok ? '' : local_appcrue_troubleshooting_post_button(
            'sync_mfa',
            get_string('troubleshooting:syncmfa', 'local_appcrue')
        )
    );
}

if ($messageplugininstalled && $messageplugincodeavailable) {
    echo $OUTPUT->heading(get_string('troubleshooting:messageheading', 'local_appcrue'), 3);
    echo html_writer::tag('p', get_string('troubleshooting:messageintro', 'local_appcrue'));
    echo $OUTPUT->notification(get_string('troubleshooting:pushnote', 'local_appcrue'), \core\output\notification::NOTIFY_INFO);
    echo $OUTPUT->notification(
        get_string('troubleshooting:messagelognote', 'local_appcrue') . ' ' .
        html_writer::link(
            new moodle_url('/admin/tasklogs.php', ['filter' => 'message_appcrue\\task\\sendbuffered']),
            get_string('troubleshooting:tasklogslink', 'local_appcrue')
        ),
        \core\output\notification::NOTIFY_INFO
    );
    $processorenabled = (bool)$messageplugininfo->is_enabled();
    echo local_appcrue_troubleshooting_result(
        get_string('pluginname', 'message_appcrue'),
        get_string(
            $processorenabled
                ? 'troubleshooting:messageprocessor_enabled'
                : 'troubleshooting:messageprocessor_disabled',
            'local_appcrue'
        ),
        $processorenabled ? 'success' : 'warning'
    );

    $pushenabled = (bool)get_config('message_appcrue', 'enable_push');
    echo local_appcrue_troubleshooting_result(
        get_string('enable_push', 'message_appcrue'),
        get_string($pushenabled ? 'troubleshooting:messageenabled' : 'troubleshooting:messagedisabled', 'local_appcrue'),
        $pushenabled ? 'success' : 'warning'
    );

    $pushapikey = (string)get_config('message_appcrue', 'apikey');
    echo local_appcrue_troubleshooting_result(
        get_string('api_key', 'message_appcrue'),
        get_string($pushapikey !== '' ? 'troubleshooting:messagekeyok' : 'troubleshooting:messagekeymissing', 'local_appcrue'),
        $pushapikey !== '' ? 'success' : 'danger'
    );

    $pushappid = (string)get_config('message_appcrue', 'appid');
    echo local_appcrue_troubleshooting_result(
        get_string('app_id', 'message_appcrue'),
        get_string($pushappid !== '' ? 'troubleshooting:messageappidok' : 'troubleshooting:messageappidmissing', 'local_appcrue'),
        $pushappid !== '' ? 'success' : 'danger'
    );

    $pushmatchfield = (string)(get_config('message_appcrue', 'match_user_by') ?: 'username');
    $validpushfields = get_user_fieldnames();
    require_once($CFG->dirroot . '/user/profile/lib.php');
    foreach (profile_get_custom_fields() as $profilefield) {
        $validpushfields[] = 'profile_field_' . $profilefield->shortname;
    }
    $pushfieldvalid = in_array($pushmatchfield, $validpushfields, true);
    echo local_appcrue_troubleshooting_result(
        get_string('match_user_by', 'message_appcrue'),
        get_string(
            $pushfieldvalid ? 'troubleshooting:messagefieldok' : 'troubleshooting:messagefieldinvalid',
            'local_appcrue',
            $pushmatchfield
        ),
        $pushfieldvalid ? 'success' : 'warning'
    );

    $bufferedtable = new xmldb_table('message_appcrue_buffered');
    if ($DB->get_manager()->table_exists($bufferedtable)) {
        $buffercounts = (object)[
            'pending' => $DB->count_records('message_appcrue_buffered', ['status' => 0]),
            'failed' => $DB->count_records('message_appcrue_buffered', ['status' => 1]),
        ];
        echo local_appcrue_troubleshooting_result(
            get_string('bufferedmode', 'message_appcrue'),
            get_string('troubleshooting:messagebuffer', 'local_appcrue', $buffercounts),
            $buffercounts->failed > 0 ? 'warning' : 'info'
        );
    } else if ((bool)get_config('message_appcrue', 'bufferedmode')) {
        echo local_appcrue_troubleshooting_result(
            get_string('bufferedmode', 'message_appcrue'),
            get_string('troubleshooting:messagebuffermissing', 'local_appcrue'),
            'danger'
        );
    }

    echo local_appcrue_troubleshooting_result(
        get_string('testconnection', 'message_appcrue'),
        get_string('troubleshooting:messagetestwarning', 'local_appcrue'),
        'info',
        html_writer::link(
            new moodle_url('/message/output/appcrue/testconnection.php'),
            get_string('troubleshooting:messagetestlink', 'local_appcrue'),
            ['class' => 'btn btn-secondary']
        )
    );
}


echo html_writer::end_tag('div');
if (!$fragmentmode) {
    echo $OUTPUT->footer();
}
