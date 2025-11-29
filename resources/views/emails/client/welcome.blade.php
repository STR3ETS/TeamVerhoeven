<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Welkom bij 2BEFIT</title>
</head>
<body style="margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;background-color:#f4f4f4;">
<div style="margin:0 auto;background:#d8c1ac;width:700px;">

    {{-- HEADER / HERO --}}
    <div style="width:100%;text-align:center;padding-top:20px;">
        <a href="{{ $portalUrl }}">
            <img src="https://mcusercontent.com/ae88aba1db76c8950098bd323/images/538b66bf-5169-3021-da94-76e586612498.png"
                 alt="2Befit X Team Verhoeven"
                 style="width:550px;padding:20px;margin:0 auto;">
        </a>
    </div>

    <div style="padding:12px 24px;line-height:1.7;">
        <h3 style="margin-top:0;margin-bottom:10px;">
            Welkom {{ $firstName ?: $user->name }} bij 2BEFIT X TEAM VERHOEVEN
        </h3>

        <p style="padding-bottom:15px;margin:0;">
            Gefeliciteerd en welkom in ons team! Vanaf nu kun jij gebruikmaken van onze
            <br><strong>unieke online omgeving</strong>: jouw persoonlijk platform waar alles samenkomt om het maximale uit jezelf te halen.
        </p>

        <p style="padding-bottom:15px;margin:0;">
            Hier vind je:<br>
            ✔ Jouw persoonlijke schema’s<br>
            ✔ Direct contact met jouw coach<br>
            ✔ Toegang tot aankomende community-events<br>
        </p>

        <p style="padding-bottom:15px;margin:0;">
            Alles onder één dak, overzichtelijk en speciaal afgestemd op jou.
        </p>

        <p style="margin:0;">
            Met sportieve groet,<br>
            <strong>Team 2BEFIT X TEAM VERHOEVEN</strong>
        </p>
    </div>

    <div style="text-align:center;width:100%;padding:30px 0 40px 0;">
        <a href="{{ $portalUrl }}"
           style="display:inline-block;padding:15px 30px;color:black;text-decoration:none;border-radius:30px;border:#c4a785 2px solid;background-color:#c4a785;margin:0 auto;font-weight:bold;">
            Klik hier voor jouw online omgeving
        </a>
    </div>

    <div style="text-align:center;border-top:1px solid #b89c82;border-bottom:1px solid #b89c82;">
        <p style="padding:20px;margin:0;">
            <strong>Tip!</strong> Zet de online omgeving op jouw <strong>beginscherm</strong> van je telefoon voor snelle toegang.
        </p>
    </div>

    {{-- INSTAGRAM ICON --}}
    <div style="text-align:center;padding-top:30px;">
        <a href="https://www.instagram.com/2befitlifestyle" style="text-decoration:none;">
            <img src="https://cdn-images.mailchimp.com/icons/social-block-v3/block-icons-v3/instagram-filled-dark-40.png"
                 alt="Instagram"
                 style="width:50px;height:50px;border-radius:10px;border:none;">
        </a>
    </div>

    {{-- 2BEFIT LOGO --}}
    <div style="text-align:center;padding:30px 0 15px 0;">
        <img src="https://mcusercontent.com/ae88aba1db76c8950098bd323/images/cc0506cf-ff7f-569b-c804-98b163d7c9e5.png"
             alt="2Befit Logo"
             style="width:100px;height:100px;border-radius:10px;border:none;">
    </div>

    {{-- SUPPLEMENTS / KORTING BLOK OP BASIS VAN PAKKET --}}
    <div style="padding:0 25px 30px 25px;line-height:1.7;font-size:14px;">

        @if($package === 'pakket_b' && $duration === 24)
            {{-- 1) Chasing Goals – 24 weken --}}
            <h4 style="margin:0 0 10px 0;font-size:16px;">
                2Befit Supplements: claim je €40 korting + Voedingsleidraad
            </h4>

            <p style="margin:0 0 10px 0;">
                Hoi {{ $firstName ?: $user->name }},
            </p>

            <p style="margin:0 0 10px 0;">
                Goed bezig met je traject! Omdat je <strong>Chasing Goals – 24 weken</strong> volgt,
                krijg je <strong>€40 korting</strong> op je bestelling bij 2Befit Supplements.
            </p>

            <p style="margin:0 0 15px 0;">
                Gebruik bij het afrekenen de code <strong>ELITE-X-FREE</strong> en shop via de knop hieronder:
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $shopLink }}"
                   style="display:inline-block;padding:12px 24px;border-radius:999px;background-color:#c4a785;color:#000;text-decoration:none;font-weight:bold;">
                    Shoppen bij 2Befit Supplements
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                Daarnaast krijg je toegang tot onze <strong>Voedingsleidraad</strong>. Deze helpt je om jouw resultaat
                maximaal te ondersteunen met de juiste voeding.
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $guidelineLink }}"
                   style="display:inline-block;padding:10px 22px;border-radius:999px;border:2px solid #c4a785;background-color:#d8c1ac;color:#000;text-decoration:none;font-weight:bold;">
                    Download de Voedingsleidraad
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                <strong>Belangrijk: 12-minuten Coopertest</strong><br>
                Heb je de Coopertest nog niet gedaan? Leg ’m deze week af en stuur je afstand (in meters)
                via de chat in de online omgeving naar je coach.
            </p>

            <p style="margin:15px 0 5px 0;font-size:12px;color:#333;">
                Korting: éénmalig per atleet, niet combineerbaar met andere acties.
            </p>

        @elseif($package === 'pakket_c' && $duration === 12)
            {{-- 2) Elite – 12 weken --}}
            <h4 style="margin:0 0 10px 0;font-size:16px;">
                €40 korting op 2Befit Supplements + Voedingsleidraad
            </h4>

            <p style="margin:0 0 10px 0;">
                Hoi {{ $firstName ?: $user->name }},
            </p>

            <p style="margin:0 0 10px 0;">
                Als <strong>Elite – 12 weken</strong> atleet krijg je <strong>€40 korting</strong> bij 2Befit Supplements.
            </p>

            <p style="margin:0 0 15px 0;">
                Gebruik de code <strong>ELITE-X-FREE</strong> en bestel via de knop hieronder:
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $shopLink }}"
                   style="display:inline-block;padding:12px 24px;border-radius:999px;background-color:#c4a785;color:#000;text-decoration:none;font-weight:bold;">
                    Shoppen bij 2Befit Supplements
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                Je krijgt ook toegang tot onze <strong>Voedingsleidraad</strong> zodat je voeding aansluit
                op je trainingsdoelen.
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $guidelineLink }}"
                   style="display:inline-block;padding:10px 22px;border-radius:999px;border:2px solid #c4a785;background-color:#d8c1ac;color:#000;text-decoration:none;font-weight:bold;">
                    Download de Voedingsleidraad
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                <strong>Belangrijk: 12-minuten Coopertest</strong><br>
                Nog niet gedaan? Voer de test deze week uit en stuur je afstand (meters)
                via de chat in de online omgeving naar je coach.
            </p>

            <p style="margin:15px 0 5px 0;font-size:12px;color:#333;">
                Korting: éénmalig per atleet, niet combineerbaar met andere acties.
            </p>

        @elseif($package === 'pakket_c' && $duration === 24)
            {{-- 3) Elite – 24 weken --}}
            <h4 style="margin:0 0 10px 0;font-size:16px;">
                €40 korting + gratis Athlete T-shirt (Elite 24 weken)
            </h4>

            <p style="margin:0 0 10px 0;">
                Hoi {{ $firstName ?: $user->name }},
            </p>

            <p style="margin:0 0 10px 0;">
                Top dat je <strong>Elite – 24 weken</strong> draait! Je krijgt <strong>€40 korting</strong> bij 2Befit Supplements.
            </p>

            <p style="margin:0 0 15px 0;">
                Gebruik de kortingscode <strong>ELITE-X-FREE</strong> en bestel via:
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $shopLink }}"
                   style="display:inline-block;padding:12px 24px;border-radius:999px;background-color:#c4a785;color:#000;text-decoration:none;font-weight:bold;">
                    Shoppen bij 2Befit Supplements
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                Bovendien ontvang je een <strong>gratis Athlete T-shirt</strong>. Geef je maat en geslacht door via het formulier:
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $shirtFormLink }}"
                   style="display:inline-block;padding:10px 22px;border-radius:999px;border:2px solid #c4a785;background-color:#d8c1ac;color:#000;text-decoration:none;font-weight:bold;">
                    Gegevens voor T-shirt doorgeven
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                Je hebt ook toegang tot onze <strong>Voedingsleidraad</strong>:
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $guidelineLink }}"
                   style="display:inline-block;padding:10px 22px;border-radius:999px;border:2px solid #c4a785;background-color:#d8c1ac;color:#000;text-decoration:none;font-weight:bold;">
                    Download de Voedingsleidraad
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                <strong>Belangrijk: 12-minuten Coopertest</strong><br>
                Heb je ’m nog niet gedaan? Leg hem deze week af en stuur je afstand (meters)
                via de chat in de online omgeving naar je coach.
            </p>

            <p style="margin:15px 0 5px 0;font-size:12px;color:#333;">
                Korting: éénmalig per atleet, niet combineerbaar met andere acties.
            </p>

        @elseif($package === 'pakket_b' && $duration === 12)
            {{-- 4) Chasing Goals – 12 weken --}}
            <h4 style="margin:0 0 10px 0;font-size:16px;">
                Jouw Voedingsleidraad + vervolgstap voor je training
            </h4>

            <p style="margin:0 0 10px 0;">
                Hoi {{ $firstName ?: $user->name }},
            </p>

            <p style="margin:0 0 10px 0;">
                Als <strong>Chasing Goals – 12 weken</strong> atleet krijg je toegang tot onze
                <strong>Voedingsleidraad</strong>.
            </p>

            <p style="margin:0 0 20px 0;text-align:center;">
                <a href="{{ $guidelineLink }}"
                   style="display:inline-block;padding:10px 22px;border-radius:999px;border:2px solid #c4a785;background-color:#d8c1ac;color:#000;text-decoration:none;font-weight:bold;">
                    Download de Voedingsleidraad
                </a>
            </p>

            <p style="margin:0 0 10px 0;">
                Let op: de <strong>€40 korting (ELITE-X-FREE)</strong> geldt niet voor Chasing Goals 12 weken.
            </p>

            <p style="margin:10px 0 0 0;">
                <strong>Belangrijk: 12-minuten Coopertest</strong><br>
                Nog niet gedaan? Voer de test deze week uit en stuur je afstand (meters)
                via de chat in de online omgeving naar je coach.
            </p>

        @else
            {{-- 5) Basis – 12 & 24 weken (pakket_a) --}}
            <h4 style="margin:0 0 10px 0;font-size:16px;">
                Belangrijke vervolgstap voor je training
            </h4>

            <p style="margin:0 0 10px 0;">
                Hoi {{ $firstName ?: $user->name }},
            </p>

            <p style="margin:0 0 10px 0;">
                Let op: de <strong>€40 korting (ELITE-X-FREE)</strong> en de <strong>Voedingsleidraad</strong>
                gelden niet voor het Basis-pakket.
            </p>

            <p style="margin:10px 0 0 0;">
                <strong>Belangrijk: 12-minuten Coopertest</strong><br>
                Heb je de test nog niet gedaan? Leg ’m deze week af en stuur je afstand (in meters)
                via de chat in de online omgeving door aan je coach.
            </p>
        @endif

    </div>

    {{-- FOOTER --}}
    <div style="text-align:center;line-height:2.0;padding:0 25px 20px 25px;font-size:13px;">
        <p style="margin:0 0 10px 0;">
            <strong>Copyright (C) 2025 | 2Befit Lifestyle &amp; Gym. Alle rechten voorbehouden.</strong><br>
            Je ontvangt deze e-mail omdat je lid bent van <strong>2Befit Gym</strong>, gebruikmaakt van onze
            <strong>coachingpakketten</strong> of je hebt aangemeld voor updates over trainingen, events en
            2Befit Supplements.
        </p>

        <p style="margin:0 0 10px 0;">
            Ons adres:<br>
            2Befit Lifestyle &amp; Gym<br>
            Groenestraat 61<br>
            6991 GD Rheden<br>
            Nederland
        </p>

        <p style="margin:0;">
            Ons mailingadres is:<br>
            <a href="mailto:coaching@2befitlifestyle.nl" style="color:#000;text-decoration:none;">
                coaching@2befitlifestyle.nl
            </a>
        </p>
    </div>
</div>
</body>
</html>