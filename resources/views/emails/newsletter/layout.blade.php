<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        /* Base reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        
        /* Layout */
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-top: 30px; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
        .header { background-color: #0f172a; padding: 24px 30px; text-align: center; }
        .header-logo { color: #ffffff; font-size: 22px; font-weight: bold; text-decoration: none; letter-spacing: -0.5px; }
        .content { padding: 35px 30px; color: #374151; font-size: 16px; line-height: 1.6; }
        .footer { padding: 25px 30px; text-align: center; font-size: 12px; color: #9ca3af; background-color: #f9fafb; border-top: 1px solid #e5e7eb; }
        .footer a { color: #4b5563; text-decoration: underline; }
        .btn { display: inline-block; padding: 12px 28px; background-color: #10b981; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px; text-align: center; }
        .btn:hover { background-color: #059669; }
    </style>
</head>
<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="wrapper">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <a href="{{ url('/') }}" class="header-logo">
                                {{ setting('site_name', config('app.name', 'Celios CMS')) }}
                            </a>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="content">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p style="margin: 0 0 8px 0;">
                                {{ setting('newsletter_company_address', 'Celios CMS • All rights reserved') }}
                            </p>
                            @yield('footer_links')
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @yield('tracking_pixel')
</body>
</html>
