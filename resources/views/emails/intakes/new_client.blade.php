<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuwe intake / klant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Basis e-mail styles (simpel & veilig) -->
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
            max-width: 1000px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(15, 27, 40, 0.06);
        }
        .inner {
            padding: 24px 24px 32px;
        }
        h1 {
            font-size: 22px;
            margin: 0 0 16px;
            color: #111827;
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 10px;
        }
        .label {
            font-weight: 600;
            color: #111827;
        }
        .value {
            color: #111827;
        }
        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 16px 0;
        }
        .button-wrapper {
            text-align: center;
            margin: 24px 0 8px;
        }
        .button {
            display: inline-block;
            padding: 10px 22px;
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
            margin-top: 12px;
        }
        .meta-table {
            width: 100%;
            margin: 8px 0 0;
        }
        .meta-table td {
            padding: 3px 0;
            font-size: 14px;
            vertical-align: top;
        }
        .meta-table td:first-child {
            width: 150px;
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
                            <td class="inner">
                                <h1>Nieuwe intake / klant</h1>

                                <p>Er is zojuist een nieuwe gebruiker aangemaakt via de intake.</p>

                                <table class="meta-table" role="presentation">
                                    <tr>
                                        <td class="label">Naam:</td>
                                        <td class="value">{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">E-mail:</td>
                                        <td class="value">{{ $user->email }}</td>
                                    </tr>
                                    @if(!empty($intake->payload['contact']['phone']))
                                        <tr>
                                            <td class="label">Telefoon:</td>
                                            <td class="value">{{ $intake->payload['contact']['phone'] }}</td>
                                        </tr>
                                    @endif
                                </table>

                                @php
                                    $contact = $intake->payload['contact'] ?? [];
                                    $profile = $intake->payload['profile'] ?? [];
                                    $goal    = $intake->payload['goal'] ?? [];

                                    $packageCode = $intake->payload['package'] ?? null;
                                    $packageLabel = match ($packageCode) {
                                        'pakket_a' => 'Basis Pakket',
                                        'pakket_b' => 'Chasing Goals Pakket',
                                        'pakket_c' => 'Elite Hyrox Pakket',
                                        default    => $packageCode ?? '-',
                                    };
                                @endphp

                                <div class="divider"></div>

                                <table class="meta-table" role="presentation">
                                    <tr>
                                        <td class="label">Gekozen pakket:</td>
                                        <td class="value">{{ $packageLabel }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Duur:</td>
                                        <td class="value">
                                            {{ $intake->payload['duration_weeks'] ?? '-' }} weken
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label">Coach voorkeur:</td>
                                        <td class="value">{{ $contact['preferred_coach'] ?? 'geen voorkeur' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Startdatum:</td>
                                        <td class="value">{{ $intake->start_date ?? '-' }}</td>
                                    </tr>
                                </table>

                                @if(!empty($profile['goals']))
                                    <div style="margin-top: 12px;">
                                        <p class="label" style="margin-bottom: 4px;">Doelen:</p>
                                        <p class="value">
                                            {{ is_array($profile['goals']) ? implode(', ', $profile['goals']) : $profile['goals'] }}
                                        </p>
                                    </div>
                                @endif

                                @if($order ?? false)
                                    <div class="divider"></div>

                                    <p class="label" style="margin-bottom: 4px;">Ordergegevens</p>
                                    <table class="meta-table" role="presentation">
                                        <tr>
                                            <td class="label">Order ID:</td>
                                            <td class="value">{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Status:</td>
                                            <td class="value">{{ $order->status }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Bedrag:</td>
                                            <td class="value">
                                                € {{ number_format(($order->amount_cents ?? 0) / 100, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </table>
                                @endif

                                <div class="button-wrapper">
                                    <a href="{{ config('app.url') }}" class="button">
                                        Open mijn app
                                    </a>
                                </div>

                                <p class="footer">
                                    Groeten,<br>
                                    Team Verhoeven / 2BeFit
                                </p>
                            </td>
                        </tr>
                    </table>

                    <div class="footer">
                        &copy; {{ date('Y') }} Team Verhoeven / 2BeFit
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
