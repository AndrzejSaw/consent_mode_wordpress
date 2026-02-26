<?php
/**
 * Compatibility stub – DEPRECATED.
 *
 * This file is kept only for users who had the plugin active under the old
 * file name. Please deactivate this entry in Plugins and activate
 * "consent-mode.php" instead.
 *
 * @package ConsentMode
 * @deprecated 1.1.0 Use consent-mode.php
 */

add_action(
'admin_notices',
function () {
if ( ! current_user_can( 'activate_plugins' ) ) {
return;
}
echo '<div class="notice notice-warning is-dismissible"><p>'
. '<strong>Universal Consent Mode:</strong> '
. 'Wtyczka jest aktywowana przez przestarzały plik <code>ru-consent-mode.php</code>. '
. 'Dezaktywuj tę wersję i aktywuj <code>consent-mode.php</code>.'
. '</p></div>';
}
);
