@php
    $siteTitle = $setup->from_name ?? $setup->title ?? config('app.name');
    $logo = !empty($setup->logo) ? asset('assets/images/'.$setup->logo) : null;
    $siteUrl = url('/');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteTitle }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;color:#172033;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f4f7fb;margin:0;padding:30px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:640px;background:#ffffff;border:1px solid #e3e8f0;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px 28px;background:#0b5f30;color:#ffffff;">
                            @if($logo)
                                <img src="{{ $logo }}" alt="{{ $siteTitle }}" style="display:block;max-width:180px;max-height:60px;margin:0 0 14px 0;">
                            @endif
                            <div style="font-size:22px;line-height:1.35;font-weight:700;">
                                {{ $siteTitle }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 28px;font-size:15px;line-height:1.7;color:#263044;">
                            {!! $email_body !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px;background:#f8fafc;border-top:1px solid #e3e8f0;color:#667085;font-size:13px;line-height:1.6;">
                            <p style="margin:0 0 8px 0;">This email was sent by {{ $siteTitle }}.</p>
                            <p style="margin:0;">
                                <a href="{{ $siteUrl }}" style="color:#0b5f30;text-decoration:none;">{{ $siteUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
