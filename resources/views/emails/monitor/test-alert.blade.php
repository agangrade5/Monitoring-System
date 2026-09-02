@extends('layouts.emails.app')
@section('title', 'Test Monitor Notification - ' . $monitor->name)
@section('content')

<div style="text-align: center; margin-bottom: 25px;">
    <span style="display: inline-block; background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 6px 14px; border-radius: 50px; border: 1px solid rgba(13, 110, 253, 0.2);">
        🔔 Diagnostic Test Notification
    </span>
</div>

<h2 style="margin: 0 0 12px; color: #1e293b; font-size: 22px; font-weight: 700; text-align: center;">
    Test Alert for {{ $monitor->name }}
</h2>

<p style="text-align: center; color: #64748b; font-size: 14px; margin-top: 0; margin-bottom: 25px;">
    This is an on-demand test notification to verify your alert channel and recipient integration.
</p>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 25px; overflow: hidden;">
    <tr>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; width: 35%; font-weight: 600;">Monitor Name:</td>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b; font-weight: 600;">{{ $monitor->name }}</td>
    </tr>
    <tr>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">Endpoint URL:</td>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0d6efd;">{{ $monitor->url ?? 'Not configured' }}</td>
    </tr>
    <tr>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">Current Status:</td>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #16a34a; font-weight: 700;">
            ● {{ strtoupper($monitor->status ?? 'UP') }}
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">Average Response:</td>
        <td style="padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b;">{{ $monitor->response_time ?? 245 }} ms</td>
    </tr>
    <tr>
        <td style="padding: 12px 18px; font-size: 13px; color: #64748b; font-weight: 600;">Triggered At:</td>
        <td style="padding: 12px 18px; font-size: 14px; color: #1e293b;">{{ now()->format('Y-m-d H:i:s T') }}</td>
    </tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ $url }}" style="display: inline-block; background: #0d6efd; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);">
                View Monitor Dashboard &rarr;
            </a>
        </td>
    </tr>
</table>

<div style="background: #f1f5f9; border-radius: 6px; padding: 12px 16px; color: #475569; font-size: 12px; line-height: 1.5; text-align: center;">
    <strong>Verification Successful:</strong> If you received this email, your email alerts for this endpoint are functioning properly.
</div>

<p style="margin-top: 25px; font-size: 13px; color: #64748b;">
    Triggered by: <strong>{{ $user->name ?? 'Administrator' }}</strong><br>
    Regards,<br>
    <strong>{{ config('app.name') }} Automated Monitoring</strong>
</p>

@endsection
