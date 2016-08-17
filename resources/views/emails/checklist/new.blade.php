@extends('emails.partials.layout')

@section('content')
    <!-- New Checklist  -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full"  bgcolor="#69b8f1"style="background-image: url('patterns/noise_pattern_with_crosslines.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;" oldcss="background-image: url('patterns/graphy.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;">
        <tr>
            <td align="center" style="background-image: url('patterns/noise_pattern_with_crosslines.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;" id="not4ChangeBG"oldcss="background-image: url('patterns/graphy.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;">


                <!-- Mobile Wrapper -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                    <tr>
                        <td width="100%" align="center">

                            <!-- SORTABLE -->
                            <div class="sortable_inner ui-sortable">

                                <!-- Space -->
                                <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="full" object="drag-module-small">
                                    <tr>
                                        <td width="100%" height="50"></td>
                                    </tr>
                                </table><!-- End Space -->

                                <!-- Space -->
                                <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="full" object="drag-module-small">
                                    <tr>
                                        <td width="100%" height="50"></td>
                                    </tr>
                                </table><!-- End Space -->

                                <!-- Main -->
                                <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="full" style="border-radius: 6px;">
                                    <tr>
                                        <td width="100%" style="border-radius: 6px; background-color: rgb(255, 255, 255);" bgcolor="#ffffff">

                                            <!-- Start Top -->
                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#3d85b8"object="drag-module-small" style="border-top-right-radius: 6px; border-top-left-radius: 6px; background-color: rgb(61, 133, 184);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <!-- Header Text -->
                                                        <table width="280" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="35"></td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%" style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 18px; color: #ffffff; line-height: 26px; font-weight: 700;" class="fullCenter"  cu-identify="element_023822367923382282">
                                                                    New Checklist</td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Image 113px -->
                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#3d85b8" style="background-image: url('http://rocketway.net/themebuilder/products/notifications/templates/notify2/images/overlay1.png'); background-color: rgb(61, 133, 184); background-position: center bottom; background-repeat: repeat-x;"object="drag-module-small">
                                                <tr>
                                                    <td width="400" height="15" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="400" valign="middle" style="text-align: center; line-height: 1px;" align="center">

                                                        <table width="100" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                                                            <tr>
                                                                <td width="100" height="35" align="center"  class="image113">
                                                                    <a href="#">
                                                                    </a><a href="#" style="text-decoration: none;"><img src="{{ env('DOMAIN') }}/images/person_113.png" width="113" alt="" border="0" ></a>

                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table><!-- End Image 113px -->

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#ffffff"object="drag-module-small" style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="300" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="400" height="30" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%" style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 34px; color: #3d85b8; line-height: 40px; font-weight: 100;" class="fullCenter"  cu-identify="element_04729914344851718">
                                                                    {{ $maker->name }}
                                                                    <br>
                                                                    ({{ $maker->email }})
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#ffffff"object="drag-module-small" style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="300" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="30" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%" style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 13px; color: #5f6a74; line-height: 24px; font-weight: 400;" class="fullCenter"  cu-identify="element_02547036622968528">
                                                                    {{ $maker->name }} has created a list of files to get from you. <span style="font-weight: bold;">If you don't know what this is about, please ignore this email.</span>&nbsp;Otherwise, click the link below to see and upload the required files.</td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#ffffff"object="drag-module-small" style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="300" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="35" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#ffffff"object="drag-module-small" style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="300" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="100%" align="center">
                                                                    <table border="0" cellpadding="0" cellspacing="0" align="center" class="buttonScale">
                                                                        <tr>
                                                                            <td align="center" height="40" bgcolor="#3d85b8"style="border-radius: 5px; padding-left: 25px; padding-right: 25px; font-family: &quot;Open Sans&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255); font-size: 15px; font-weight: 700; line-height: 1px; background-color: rgb(61, 133, 184);">

                                                                                <a href="{{ env('DOMAIN') }}/checklist/{{ hashId($checklist) }}"  style="color: #ffffff; text-decoration: none; width: 100%;"cu-identify="element_07613353369622293">View Checklist</a>

                                                                            </td>
                                                                        </tr>
                                                                    </table>

                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#ffffff"object="drag-module-small" style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="300" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="20" style="font-size: 1px; line-height: 1px;">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%" style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 13px; color: #5f6a74; line-height: 24px; font-weight: 400;" class="fullCenter"  cu-identify="element_0146200719600305"><span style="font-style: italic;">The link above is private and made special for you. Please don't share it before telling {{ $maker->name }}.</span></td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile" bgcolor="#ffffff"object="drag-module-small" style="border-bottom-right-radius: 6px; border-bottom-left-radius: 6px; background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="300" border="0" cellpadding="0" cellspacing="0" align="center" style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="40"></td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                </table><!-- End Main -->
                                @include('emails.partials.signature')
                            </div>

                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table><!-- End New Checklist -->
    @endsection
