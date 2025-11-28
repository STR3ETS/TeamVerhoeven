<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuwe chat van cliënt</title>
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
                            <h1>Nieuwe chat van cliënt</h1>

                            <p>Er is zojuist een nieuw gesprek gestart door een cliënt.</p>

                            @php
                                $thread = $thread ?? null;
                                $client = $thread?->clientUser;
                                $coach  = $thread?->coachUser;
                            @endphp

                            <table class="meta-table" role="presentation">
                                <tr>
                                    <td class="label">Cliënt:</td>
                                    <td class="value">
                                        {{ $client->name ?? 'Onbekende cliënt' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">E-mail cliënt:</td>
                                    <td class="value">
                                        {{ $client->email ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Onderwerp:</td>
                                    <td class="value">
                                        {{ $thread->subject ?: '(Geen onderwerp opgegeven)' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Coach:</td>
                                    <td class="value">
                                        {{ $coach->name ?? 'Onbekende coach' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Gestart op:</td>
                                    <td class="value">
                                        {{ $thread->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            </table>

                            <div class="divider"></div>

                            <p class="label" style="margin-bottom: 4px;">Wat nu?</p>
                            <p class="value">
                                Log in op je omgeving om het gesprek te bekijken en te beantwoorden.
                            </p>

                            <div class="button-wrapper">
                                <a href="{{ url('/coach/threads/'.$thread->id) }}" class="button">
                                    Open dit gesprek
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
