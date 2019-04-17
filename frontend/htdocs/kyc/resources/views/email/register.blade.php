<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
        <title>
        </title>
        <!--[if !mso]><!-- -->
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
            <!--<![endif]-->
            <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
                <meta content="width=device-width, initial-scale=1.0" name="viewport">
                    <style type="text/css">
                        #outlook a { padding: 0; }  .ReadMsgBody { width: 100%; }  .ExternalClass { width: 100%; }  .ExternalClass * { line-height:100%; }  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }  p { display: block; margin: 13px 0; }
                    </style>
                    <!--[if !mso]><!-->
                    <style type="text/css">
                        @media only screen and (max-width:480px) {    @-ms-viewport { width:320px; }    @viewport { width:320px; }  }
                    </style>
                    <!--<![endif]-->
                    <!--[if mso]><xml>  <o:OfficeDocumentSettings>    <o:AllowPNG/>    <o:PixelsPerInch>96</o:PixelsPerInch>  </o:OfficeDocumentSettings></xml><![endif]-->
                    <!--[if lte mso 11]><style type="text/css">  .outlook-group-fix {    width:100% !important;  }</style><![endif]-->
                    <!--[if !mso]><!-->
                    <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
                        <style type="text/css">
                            @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);
                        </style>
                        <!--<![endif]-->
                        <style type="text/css">
                            @media only screen and (min-width:480px) {    .mj-column-per-100 { width:100%!important; }  }
                        </style>
                    </link>
                </meta>
            </meta>
        </meta>
    </head>
    <body style="background: #FFFFFF;">
        <div class="mj-container" style="background-color:#FFFFFF;">
            <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">      <![endif]-->
            <div style="margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="font-size:0px;width:100%;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;">
                                <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                                <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="left" style="word-wrap:break-word;font-size:0px;padding:0px 20px 0px 20px;">
                                                    <div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px;line-height:22px;text-align:left;">
                                                        <p>
                                                            Hi {{ $User->first_name }}
                                                        </p>
                                                        <p>
                                                            Welcome to {{env('WEBSITE_NAME')}}. The revolutionary Cryptocurrency Exchange that will make your everyday trading a breeze.
                                                        </p>
                                                        <h3 style="line-height: 100%;">
                                                            <strong>
                                                                Following are your login credentials:
                                                            </strong>
                                                        </h3>
                                                        <p>
                                                            <strong>
                                                                Username
                                                            </strong>
                                                            : {{ $User->email }}
                                                            <br>
                                                                <strong>
                                                                    Password
                                                                </strong>
                                                                : Your Registered Password
                                                            </br>
                                                        </p>
                                                        <h3 style="line-height: 100%;">
                                                            Refer a friend & Enjoy the following benefits:
                                                        </h3>
                                                        <ul>
                                                            <li>
                                                                Special Trading fees.
                                                            </li>
                                                            <li>
                                                                For each successful person you have referred, a percentage of their traded profit will be transferred to your wallet.
                                                            </li>
                                                            <li>
                                                                A tempting referral bonus will be credited to your wallet for each verified registration.
                                                            </li>
                                                        </ul>
                                                        <p>
                                                            Login here :
                                                            <a href="{{ route('login') }}">
                                                                {{ route('login') }}
                                                            </a>
                                                        </p>
                                                        <p>
                                                            All you need to do is, share this special link with your friends:
                                                        </p>
                                                        <a href="{{ route('ref') }}?id={{ $User->referral_code }}">
                                                            {{ route('ref') }}?id={{ $User->referral_code }}
                                                        </a>
                                                        <p>
                                                            Best regards,
                                                            <br>
                                                                Team {{env('WEBSITE_NAME')}}
                                                            </br>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
        </div>
    </body>
</html>