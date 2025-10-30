<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Logger;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$addon = rex_addon::get('ycom');
$action = rex_request('warehouse_log', 'string');
$activationLink = rex_url::currentBackendPage() . '&warehouse_log=activate';
$deactivationLink = rex_url::currentBackendPage() . '&warehouse_log=deactivate';
$logFile = Logger::logFile();

switch ($action) {
    case 'activate':
        echo rex_view::success($addon->i18n('warehouse.log.activated'));
        Logger::activate();
        break;
    case 'deactivate':
        echo rex_view::success($addon->i18n('warehouse.log.deactivated'));
        Logger::deactivate();
        break;
    case 'delete':
        if (Logger::delete()) {
            echo rex_view::success($addon->i18n('syslog_deleted'));
        } else {
            echo rex_view::error($addon->i18n('syslog_delete_error'));
        }
}

if (!Logger::isActive()) {
    echo rex_view::warning(rex_i18n::rawMsg('warehouse.log.warning_logisinactive', $activationLink));
} else {
    echo rex_view::warning(rex_i18n::rawMsg('warehouse.log.warning_logisactive', $deactivationLink));
}

$content = '
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>' . rex_i18n::msg('warehouse.log.time') . '</th>
                        <th>' . rex_i18n::msg('warehouse.log.ip') . '</th>
                        <th>' . rex_i18n::msg('warehouse.log.user_id') . '</th>
                        <th>' . rex_i18n::msg('warehouse.log.event') . '</th>
                        <th>' . rex_i18n::msg('warehouse.log.params') . '</th>
                    </tr>
                </thead>
                <tbody>';

$file = rex_log_file::factory($logFile);
foreach (new LimitIterator($file, 0, 30) as $entry) {
    $data = $entry->getData();
    $class = 'ERROR' == trim($data[0]) ? 'rex-state-error' : 'rex-mailer-log-ok';
    $content .= '
                <tr class="' . $class . '">
                  <td data-title="' . rex_i18n::msg('phpmailer_log_date') . '" class="rex-table-tabular-nums">' . rex_formatter::intlDateTime($entry->getTimestamp(), [IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM]) . '</td>
                  <td data-title="' . rex_i18n::msg('warehouse.log.user_ip') . '">' . rex_escape($data[0]) . '</td>
                  <td data-title="' . rex_i18n::msg('warehouse.log.user_id') . '">' . rex_escape($data[1]) . '</td>
                  <td data-title="' . rex_i18n::msg('warehouse.log.type') . '">' . rex_escape($data[2]) . '</td>
                  <td data-title="' . rex_i18n::msg('warehouse.log.email') . '">' . rex_escape($data[3]) . '</td>
                  <td data-title="' . rex_i18n::msg('warehouse.log.params') . '">' . rex_escape($data[4] ?? '') . '</td>
                </tr>';
}

$content .= '
                </tbody>
            </table>';

$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-delete" type="submit" name="del_btn" data-confirm="' . rex_i18n::msg('warehouse.log.delete_log_msg') . '">' . rex_i18n::msg('syslog_delete') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse.log.title', $logFile), false);
$fragment->setVar('content', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
$content = '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <input type="hidden" name="warehouse_log" value="delete" />
        ' . $content . '
    </form>';

echo $content;
