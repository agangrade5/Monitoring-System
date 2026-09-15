@extends('layouts.emails.app')
@section('title', $frequencyLabel . ' Monitoring Report - ' . config('app.name'))
@section('content')

<div style="text-align: center; margin-bottom: 25px;">
    <span style="display: inline-block; background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 6px 16px; border-radius: 50px; border: 1px solid rgba(13, 110, 253, 0.2);">
        📊 {{ $frequencyLabel }} Health Digest
    </span>
</div>

<h2 style="margin: 0 0 10px; color: #1e293b; font-size: 22px; font-weight: 700; text-align: center;">
    Your {{ $frequencyLabel }} Monitoring Report is Ready
</h2>

<p style="text-align: center; color: #64748b; font-size: 14px; margin-top: 0; margin-bottom: 30px; line-height: 1.6;">
    Hello <strong>{{ $user->name }}</strong>, your automated {{ strtolower($frequencyLabel) }} performance & health monitoring report for <strong>{{ $totalMonitors }} endpoint{{ $totalMonitors === 1 ? '' : 's' }}</strong> has been generated successfully.
</p>

{{-- Prominent Download Action Box --}}
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px 20px; margin-bottom: 25px; text-align: center;">
    
    <div style="font-size: 32px; margin-bottom: 10px;">
        📑
    </div>

    <div style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
        {{ $fileName }}
    </div>

    <div style="font-size: 13px; color: #64748b; margin-bottom: 22px;">
        Contains complete summary overview, detailed monitor status, SSL & domain health, and outage logs.
    </div>

    {{-- Download Button --}}
    <div>
        <a href="{{ $downloadUrl }}" style="background-color: #16a34a; color: #ffffff; text-decoration: none; padding: 13px 32px; border-radius: 6px; font-weight: 700; font-size: 15px; display: inline-block; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);">
            📥 Download Excel Report (.xlsx)
        </a>
    </div>

    <div style="font-size: 11px; color: #94a3b8; margin-top: 14px;">
        * This secure download link is valid for 30 days.
    </div>
</div>

<div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-bottom: 10px; text-align: left;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="28" valign="middle" style="font-size: 20px;">
                📎
            </td>
            <td valign="middle" style="padding-left: 8px; font-size: 12px; color: #166534; line-height: 1.5;">
                <strong>Attachment Available:</strong> The <code>.xlsx</code> file is also directly attached to this email message. You can download it directly from the button above or save the email attachment.
            </td>
        </tr>
    </table>
</div>

@endsection
