<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', 'Notifikasi CleanUP Shoes')</title>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        table {
            border-collapse: collapse !important;
        }
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #09090b !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #f8fafc;
        }
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 10px !important;
            }
            .content-padding {
                padding: 24px 16px !important;
            }
            .mobile-stack {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .mobile-center {
                text-align: center !important;
            }
        }
    </style>
</head>
<body style="background-color: #09090b; margin: 0; padding: 0; width: 100%;">
    <!-- Core Wrapper -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #09090b; width: 100%;">
        <tr>
            <td align="center" valign="top" style="padding: 40px 10px 40px 10px;">
                <!-- Main Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 600px; background-color: #121214; border-radius: 16px; border: 1px solid rgba(249, 115, 22, 0.15); overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Top Glowing Accent -->
                    <tr>
                        <td height="4" style="background: linear-gradient(90deg, #f97316 0%, #ea580c 100%); line-height: 4px; font-size: 4px;">&nbsp;</td>
                    </tr>
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" valign="top" style="padding: 30px 40px 20px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff;">
                                        CleanUP<span style="color: #f97316;">Shoes</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; padding-top: 5px; font-weight: 600;">
                                        Premium Care & Restoration
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Sub-Header/Banner Accent (Optional) -->
                    @hasSection('banner_title')
                    <tr>
                        <td align="center" valign="top" style="padding: 0 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background: rgba(249, 115, 22, 0.06); border-radius: 10px; border: 1px solid rgba(249, 115, 22, 0.1);">
                                <tr>
                                    <td align="center" style="padding: 12px; font-size: 14px; font-weight: 700; color: #f97316; letter-spacing: 0.5px; text-transform: uppercase;">
                                        @yield('banner_title')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif
                    
                    <!-- Content Body -->
                    <tr>
                        <td align="left" valign="top" class="content-padding" style="padding: 40px 40px 30px 40px; color: #f8fafc; font-size: 15px; line-height: 1.6;">
                            
                            @yield('content')
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" valign="top" style="padding: 0 40px 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 25px;">
                                <tr>
                                    <td align="center" style="font-size: 13px; color: #94a3b8; line-height: 1.5; font-weight: 500;">
                                        @php
                                            try {
                                                $waNumber = \App\Models\Setting::where('key', 'whatsapp_number')->value('value') ?? '628123456789';
                                            } catch (\Exception $e) {
                                                $waNumber = '628123456789';
                                            }
                                            $waNumberClean = preg_replace('/[^0-9]/', '', $waNumber);
                                        @endphp
                                        Butuh bantuan? Hubungi kami via WhatsApp di <a href="https://wa.me/{{ $waNumberClean }}" style="color: #f97316; text-decoration: none; font-weight: 600;">+{{ $waNumber }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size: 12px; color: #475569; padding-top: 15px; font-weight: 600; letter-spacing: 0.02em;">
                                        &copy; {{ date('Y') }} CleanUPShoes. All Rights Reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
