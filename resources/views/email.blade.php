@php
    use App\Helpers\Provider;
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title> {{ $subject }} </title>
    <!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
    <!--[if mso]>
        <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG/>
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
    <!--[if lte mso 11]>
        <style type="text/css">
          .mj-outlook-group-fix { width:100% !important; }
        </style>
        <![endif]-->
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700" rel="stylesheet" type="text/css" />
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,700);
    </style>
    <!--<![endif]-->
    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
                max-width: 100%;
            }
        }
    </style>
    <style type="text/css">
        @media only screen and (max-width:480px) {
            table.mj-full-width-mobile {
                width: 100% !important;
            }

            td.mj-full-width-mobile {
                width: auto !important;
            }
        }
    </style>
    <style type="text/css">
        a,
        span,
        td,
        th {
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }
    </style>
</head>

<body style="background-color:#54595f;">
    <div
        style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        {{ str(collect($lines)->filter(fn($l) => is_string($l))->join(' ') ?? ($subject ?? ''))->stripTags()->words(50) }}
    </div>
    <div style="background-color:#54595f;">
        <!--[if mso | IE]>
      <table
         align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"
      >
        <tr>
          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
      <![endif]-->
        <div style="margin:0px auto;max-width:600px;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                style="width:100%;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                            <!--[if mso | IE]>
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">

        <tr>

            <td
               class="" style="vertical-align:top;width:600px;"
            >
          <![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix"
                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="vertical-align:top;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="font-size:0px;word-break:break-word;">
                                                <!--[if mso | IE]>

        <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="20" style="vertical-align:top;height:20px;">

    <![endif]-->
                                                <div style="height:20px;">   </div>
                                                <!--[if mso | IE]>

        </td></tr></table>

    <![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]>
            </td>

        </tr>

                  </table>
                <![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]>
          </td>
        </tr>
      </table>

      <table
         align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"
      >
        <tr>
          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
      <![endif]-->
        <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;border-radius:4px;max-width:600px;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                style="background:#ffffff;background-color:#ffffff;width:100%;border-radius:4px;">
                <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                            <!--[if mso | IE]>
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">

        <tr>

            <td
               class="" style="vertical-align:top;width:600px;"
            >
          <![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix"
                                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="vertical-align:top;" width="100%">
                                    <tbody>
                                        <tr>
                                            <td align="center"
                                                style="font-size:0px;padding:8px 0;word-break:break-word;">
                                                <table border="0" cellpadding="0" cellspacing="0"
                                                    role="presentation"
                                                    style="border-collapse:collapse;border-spacing:0px;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width:70px;">
                                                                <img height="auto"
                                                                    src="{{ 'data:image/png;base64,' . base64_encode(File::get(public_path('email-logo.png'))) }}"
                                                                    style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                                                    width="150" />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                <p
                                                    style="border-top:dashed 1px lightgrey;font-size:1px;margin:0px auto;width:100%;">
                                                </p>
                                                <!--[if mso | IE]>
        <table
           align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:dashed 1px lightgrey;font-size:1px;margin:0px auto;width:550px;" role="presentation" width="550px"
        >
          <tr>
            <td style="height:0;line-height:0;">
              &nbsp;
            </td>
          </tr>
        </table>
      <![endif]-->
                                            </td>
                                        </tr>
                                        @isset($caption)
                                            <tr>
                                                <td align="center"
                                                    style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <div
                                                        style="font-family:Roboto, Helvetica, Arial, sans-serif;font-size:24px;font-weight:300;line-height:30px;text-align:center;color:#000000;">
                                                        {{ $caption }}
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            @foreach ($lines as $line)
                                                @if (is_string($line))
                                                    <tr>
                                                        <td align="left"
                                                            style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                            <div
                                                                style="font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;font-weight:300;line-height:20px;text-align:left;color:#000000;">
                                                                {!! str($line)->replace('<a ', '<a style="color: #2e58ff; text-decoration: none"') !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @elseif (is_array($line) && isset($line['link']))
                                                    <tr>
                                                        <td align="center" vertical-align="middle"
                                                            style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                            <table border="0" cellpadding="0" cellspacing="0"
                                                                role="presentation"
                                                                style="border-collapse:separate;line-height:100%;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td align="center" bgcolor="#54595f"
                                                                            role="presentation"
                                                                            style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:8px 16px;background:#54595f;"
                                                                            valign="middle">
                                                                            <a href="{{ $line['link'] }}"
                                                                                style="display: inline-block; background: #54595f; color: white; font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 20px; margin: 0; text-decoration: none; text-transform: none; padding: 8px 16px; mso-padding-alt: 0px; border-radius: 3px;"
                                                                                target="_blank"> {{ $line['title'] }}
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                @else
                                                @endif
                                            @endforeach

                                            @isset($meta['footnote'])
                                                <tr>
                                                    <td style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                        <p
                                                            style="border-top:dashed 1px lightgrey;font-size:1px;margin:0px auto;width:100%;">
                                                        </p>
                                                        <!--[if mso | IE]>
                    <table
                       align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:dashed 1px lightgrey;font-size:1px;margin:0px auto;width:550px;" role="presentation" width="550px"
                    >
                      <tr>
                        <td style="height:0;line-height:0;">
                          &nbsp;
                        </td>
                      </tr>
                    </table>
                  <![endif]-->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center"
                                                        style="font-size:0px;padding:10px 25px;word-break:break-word;">

                                                        <div
                                                            style="font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;font-weight:300;line-height:20px;text-align:center;color:#929090;">
                                                            {!! $meta['footnote'] !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endisset
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]>
                                                                                            </td>

                                                                                        </tr>
                                                                                                  </table>
                                                                                                <![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--[if mso | IE]>
                                                                                          </td>
                                                                                        </tr>
                                                                                      </table>

                                                                                      <table
                                                                                         align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"
                                                                                      >
                                                                                        <tr>
                                                                                          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                                                                                      <![endif]-->
            <div style="margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                    style="width:100%;">
                    <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                                <!--[if mso | IE]>
                                                                                                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">

                                                                                        <tr>

                                                                                            <td
                                                                                               class="" style="vertical-align:top;width:600px;"
                                                                                            >
                                                                                          <![endif]-->
                                <div class="mj-column-per-100 mj-outlook-group-fix"
                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                        style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="center"
                                                    style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <div
                                                        style="font-family:Roboto, Helvetica, Arial, sans-serif;font-size:14px;font-weight:300;line-height:20px;text-align:center;color:#fafafa;">
                                                        @isset($meta['copyright'])
                                                            {!! $meta['copyright'] !!}
                                                        @else
                                                            &copy;{{ now()->format('Y') }}
                                                            {{ dbconfig('app_name') }}.,
                                                            All Rights Reserved.
                                                        @endisset
                                                    </div>

                                                </td>
                                            </tr>

                                            <tr>
                                                <td align="center"
                                                    style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                                    <!--[if mso | IE]>
                                                                                      <table
                                                                                         align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                                                      >
                                                                                        <tr>

                                                                                              <td>
                                                                                            <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]>
                                                                                            </td>

                                                                                        </tr>

                                                                                                  </table>
                                                                                                <![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--[if mso | IE]>
                                                                                          </td>
                                                                                        </tr>
                                                                                      </table>

                                                                                      <table
                                                                                         align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600"
                                                                                      >
                                                                                        <tr>
                                                                                          <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
                                                                                      <![endif]-->
            <div style="margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                    style="width:100%;">
                    <tbody>
                        <tr>
                            <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                                <!--[if mso | IE]>
                                                                                                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">

                                                                                        <tr>

                                                                                            <td
                                                                                               class="" style="vertical-align:top;width:600px;"
                                                                                            >
                                                                                          <![endif]-->
                                <div class="mj-column-per-100 mj-outlook-group-fix"
                                    style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                        style="vertical-align:top;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td style="font-size:0px;word-break:break-word;">
                                                    <!--[if mso | IE]>

                                                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td height="1" style="vertical-align:top;height:1px;">

                                                                                    <![endif]-->
                                                    <div style="height:1px;">   </div>
                                                    <!--[if mso | IE]>

                                                                                        </td></tr></table>

                                                                                    <![endif]-->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--[if mso | IE]>
                                                                                            </td>

                                                                                        </tr>

                                                                                                  </table>
                                                                                                <![endif]-->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--[if mso | IE]>
                                                                                          </td>
                                                                                        </tr>
                                                                                      </table>
                                                                                      <![endif]-->
        </div>


    </body>

    </html>
