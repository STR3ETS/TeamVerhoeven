<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Client Verlenging Notificatie</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f5f7;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1f2933;
        }
        table {
            border-collapse: collapse;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f5f7;
            padding: 20px 0;
        }
        .content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(15, 27, 40, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #c8ab7a 0%, #a89067 100%);
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            margin: 0;
            font-weight: 600;
        }
        .header .emoji {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .inner {
            padding: 24px 24px 32px;
        }
        h2 {
            font-size: 18px;
            margin: 0 0 16px;
            color: #111827;
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 12px;
        }
        .label {
            font-weight: 600;
            color: #111827;
        }
        .value {
            color: #4b5563;
        }
        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .info-box h3 {
            font-size: 14px;
            margin: 0 0 12px;
            color: #166534;
        }
        .meta-table {
            width: 100%;
            margin: 0;
        }
        .meta-table td {
            padding: 6px 0;
            font-size: 14px;
            vertical-align: top;
        }
        .meta-table td:first-child {
            width: 140px;
            font-weight: 600;
            color: #374151;
        }
        .meta-table td:last-child {
            color: #4b5563;
        }
        .highlight {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        .button-wrapper {
            text-align: center;
            margin: 24px 0 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 10px;
            background-color: #c8ab7a;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .button:hover {
            background-color: #a89067;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 16px 24px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .note {
            background-color: #fef9e7;
            border-left: 4px solid #c8ab7a;
            padding: 12px 16px;
            margin: 16px 0;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table role="presentation" width="100%">
            <tr>
                <td align="center">
                    <table role="presentation" class="content">
                        <tr>
                            <td class="header">
                                <div class="emoji">🔄</div>
                                <h1>Client Verlenging</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class="inner">
                                <p>Hallo {{ $coachName }},</p>

                                <p>
                                    Goed nieuws! <strong>{{ $clientName }}</strong> heeft zojuist zijn/haar abonnement verlengd.
                                </p>

                                <div class="info-box">
                                    <h3>📋 Verlenging Details</h3>
                                    <table class="meta-table">
                                        <tr>
                                            <td>Client:</td>
                                            <td><strong>{{ $clientName }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>E-mail:</td>
                                            <td>{{ $client->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pakket:</td>
                                            <td>{{ $package }}</td>
                                        </tr>
                                        <tr>
                                            <td>Verlengd met:</td>
                                            <td><span class="highlight">+ {{ $addedWeeks }} weken</span></td>
                                        </tr>
                                        <tr>
                                            <td>Totale periode:</td>
                                            <td>{{ $totalWeeks }} weken</td>
                                        </tr>
                                        @if($endDate)
                                        <tr>
                                            <td>Nieuwe einddatum:</td>
                                            <td><strong>{{ $endDate }}</strong></td>
                                        </tr>
                                        @endif
                                        @if($order)
                                        <tr>
                                            <td>Order ID:</td>
                                            <td>#{{ $order->id }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                <div class="note">
                                    <strong>💡 Tip:</strong> Het trainingschema van de client is ongewijzigd gebleven. 
                                    De nieuwe weken zijn toegevoegd aan het bestaande abonnement. 
                                    Overweeg contact op te nemen voor een check-in of eventuele schema-aanpassingen.
                                </div>

                                <div class="divider"></div>

                                <div class="button-wrapper">
                                    <a href="{{ $portalUrl }}/coach/clients/{{ $client->id }}" class="button">
                                        Bekijk Client
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="footer">
                                <p>
                                    Deze e-mail is automatisch verzonden door het 2BeFit systeem.<br>
                                    &copy; {{ date('Y') }} 2BeFit X Team Verhoeven
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
