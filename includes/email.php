<?php
defined( 'CTI_LS' ) || die;

class Email {

    public static function send_license( string $to, string $license_key, string $plan ): bool {
        $plan_label = License::plan_label( $plan );
        $subject    = 'Your CTI WooBundleBoost License Key';

        $html = self::wrap( '
            <h2 style="color:#1e293b;margin:0 0 8px;">Thank you for your purchase!</h2>
            <p style="color:#475569;margin:0 0 24px;">Here is your license key for <strong>' . htmlspecialchars( SITE_NAME ) . '</strong> — <strong>' . htmlspecialchars( $plan_label ) . '</strong>.</p>

            <div style="background:#f1f5f9;border:2px dashed #94a3b8;border-radius:8px;padding:20px 24px;text-align:center;margin:0 0 24px;">
                <p style="margin:0 0 6px;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Your License Key</p>
                <p style="margin:0;font-size:22px;font-weight:700;color:#2563eb;letter-spacing:.08em;font-family:monospace;">' . htmlspecialchars( $license_key ) . '</p>
            </div>

            <h3 style="color:#1e293b;font-size:15px;margin:0 0 10px;">How to activate:</h3>
            <ol style="color:#475569;padding-left:20px;margin:0 0 24px;">
                <li style="margin-bottom:6px;">Install and activate CTI WooBundleBoost on your WordPress site.</li>
                <li style="margin-bottom:6px;">Go to <strong>WooCommerce &rarr; Bundle Pricing &rarr; License</strong>.</li>
                <li style="margin-bottom:6px;">Paste your license key and click <strong>Activate</strong>.</li>
            </ol>

            <p style="color:#475569;margin:0 0 8px;">
                Need help? Reply to this email or visit <a href="' . htmlspecialchars( SITE_URL ) . '" style="color:#2563eb;">' . htmlspecialchars( SITE_URL ) . '</a>.
            </p>
            <p style="color:#94a3b8;font-size:12px;margin:0;">Keep this email safe — it is your proof of purchase.</p>
        ' );

        return self::send( $to, $subject, $html );
    }

    private static function send( string $to, string $subject, string $html ): bool {
        $payload = json_encode( [
            'from'    => MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
            'to'      => [ $to ],
            'subject' => $subject,
            'html'    => $html,
        ] );

        $ctx = stream_context_create( [ 'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n" .
                               "Authorization: Bearer " . RESEND_API_KEY . "\r\n",
            'content'       => $payload,
            'ignore_errors' => true,
        ] ] );

        $result = file_get_contents( 'https://api.resend.com/emails', false, $ctx );

        if ( $result === false ) {
            error_log( 'Resend: network error sending to ' . $to );
            return false;
        }

        $data = json_decode( $result, true );
        if ( ! empty( $data['id'] ) ) {
            return true;
        }

        error_log( 'Resend error: ' . $result );
        return false;
    }

    private static function wrap( string $content ): string {
        return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:40px 16px;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden;">
        <tr><td style="background:#2563eb;padding:28px 32px;">
          <p style="margin:0;color:#fff;font-size:20px;font-weight:700;">' . htmlspecialchars( SITE_NAME ) . '</p>
        </td></tr>
        <tr><td style="padding:32px;">
          ' . $content . '
        </td></tr>
        <tr><td style="background:#f8fafc;padding:20px 32px;border-top:1px solid #e2e8f0;">
          <p style="margin:0;color:#94a3b8;font-size:12px;">&copy; ' . date( 'Y' ) . ' ' . htmlspecialchars( SITE_NAME ) . '. All rights reserved.</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body></html>';
    }
}
