<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; color: #172033; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f6f8;">
        <tr>
            <td align="center" style="padding: 28px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <tr>
                        <td align="center" style="padding: 26px 32px 20px; background-color: #fff7ed; border-bottom: 3px solid #f97316;">
                            @if (file_exists(public_path('images/heatalert-logo-transparent.png')))
                                <img src="{{ asset('images/heatalert-logo-transparent.png') }}" alt="HeatAlert" width="140" style="display: block; width: 140px; max-width: 100%; height: auto; margin: 0 auto 12px; border: 0;">
                            @else
                                <div style="font-size: 28px; line-height: 32px; font-weight: 700; color: #e65100;">HeatAlert</div>
                            @endif
                            <div style="font-size: 13px; line-height: 20px; color: #1e3a5f;">Prévention &bull; Sécurité &bull; Information</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="margin: 0 0 22px; color: #172033; font-size: 24px; line-height: 32px; font-weight: 700;">{{ $title }}</h1>
                            <p style="margin: 0 0 18px; color: #172033; font-size: 16px; line-height: 24px;">{{ $greeting }}</p>
                            @foreach ($introLines as $line)
                                <p style="margin: 0 0 16px; color: #475569; font-size: 16px; line-height: 24px;">{{ $line }}</p>
                            @endforeach
                            @if ($actionText && $actionUrl)
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 28px 0;">
                                    <tr>
                                        <td align="center" bgcolor="#f97316" style="border-radius: 7px;">
                                            <a href="{{ $actionUrl }}" style="display: inline-block; padding: 13px 22px; color: #ffffff; font-size: 16px; line-height: 20px; font-weight: 700; text-decoration: none; border-radius: 7px;">{{ $actionText }}</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            @foreach ($outroLines as $line)
                                <p style="margin: 0 0 14px; color: #64748b; font-size: 14px; line-height: 22px;">{{ $line }}</p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 22px 32px; background-color: #f8fafc; border-top: 1px solid #e5e7eb; color: #64748b; font-size: 12px; line-height: 18px; text-align: center;">
                            <strong style="color: #1e3a5f;">HeatAlert &copy; {{ now()->year }}</strong><br>
                            Cet e-mail a été envoyé automatiquement par HeatAlert.<br>
                            Merci de ne pas répondre directement à ce message.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
