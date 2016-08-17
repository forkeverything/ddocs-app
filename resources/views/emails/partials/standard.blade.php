@extends('emails.partials.layout')

@section('content')
    <!-- Notification 3  -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="full" bgcolor="#e3e6ed"
           style="background-image: url('patterns/noise_pattern_with_crosslines.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;"
           oldcss="background-image: url('patterns/graphy.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;">
        <tr>
            <td align="center"
                style="background-image: url('patterns/noise_pattern_with_crosslines.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;"
                id="not3ChangeBG"
                oldcss="background-image: url('patterns/graphy.png'); background-attachment: initial; background-color: initial; background-size: inherit; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: repeat;">


                <!-- Mobile Wrapper -->
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="mobile">
                    <tr>
                        <td width="100%" height="100" align="center">

                            <!-- SORTABLE -->
                            <div class="sortable_inner ui-sortable">

                                <!-- Space -->
                                <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="full" object="drag-module-small">
                                    <tr>
                                        <td width="100%" height="50"></td>
                                    </tr>
                                </table><!-- End Space -->

                                <!-- Space -->
                                <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="full" object="drag-module-small">
                                    <tr>
                                        <td width="100%" height="50"></td>
                                    </tr>
                                </table><!-- End Space -->

                                <!-- Shadow -->
                                <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                       class="full" style="border-radius: 6px;">
                                    <tr>
                                        <td width="100%"
                                            style="border-radius: 6px; box-shadow: rgba(0, 0, 0, 0.0980392) 0px 0px 6px 0px; background-color: rgb(255, 255, 255);"
                                            bgcolor="#ffffff">

                                            <!-- Start Top -->
                                            <div style="display: none" id="element_024107482905265587"></div>
                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                                   class="mobile" bgcolor="@yield('header-color')" object="drag-module-small"
                                                   style="border-top-right-radius: 6px; border-top-left-radius: 6px; background-color: @yield('header-color');">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <!-- Header Text -->
                                                        <table width="300" border="0" cellpadding="0" cellspacing="0"
                                                               align="center"
                                                               style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                                               class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="35"></td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%"
                                                                    style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 27px; color: #ffffff; line-height: 35px; font-weight: 400;"
                                                                    class="fullCenter"
                                                                    cu-identify="element_0016738670945827705">
                                                                    @yield('header-text')
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="35"
                                                                    style="font-size: 1px; line-height: 1px;">
                                                                    &nbsp;</td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                                   class="mobile" bgcolor="#ffffff" object="drag-module-small"
                                                   style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <!-- Header Text -->
                                                        <table width="350" border="0" cellpadding="0" cellspacing="0"
                                                               align="center"
                                                               style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                                               class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="40"></td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%"
                                                                    style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 40px; color: #3f4345; line-height: 42px; font-weight: 700;"
                                                                    class="fullCenter"
                                                                    cu-identify="element_05260859095445161">
                                                                    @yield('subheading')
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="8"
                                                                    style="font-size: 1px; line-height: 1px;">
                                                                    &nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%"
                                                                    style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 14px; color: #8e9197; line-height: 24px; font-weight: 400;"
                                                                    class="fullCenter"
                                                                    cu-identify="element_06298370220045268">
                                                                    @yield('summary')
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="35"
                                                                    style="font-size: 1px; line-height: 1px;">
                                                                    &nbsp;</td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table><!-- End Second -->

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                                   class="mobile" bgcolor="#ffffff" object="drag-module-small"
                                                   style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <!-- Text -->
                                                        <table width="330" border="0" cellpadding="0" cellspacing="0"
                                                               align="center"
                                                               style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                                               class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="15"
                                                                    style="font-size: 1px; line-height: 1px;">
                                                                    &nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="middle" width="100%"
                                                                    style="text-align: center; font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 14px; color: #8e9197; line-height: 24px; font-weight: 400;"
                                                                    class="fullCenter"
                                                                    cu-identify="element_0760624951354917">
                                                                    @yield('body')
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                                   class="mobile" bgcolor="#ffffff" object="drag-module-small"
                                                   style="background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="330" border="0" cellpadding="0" cellspacing="0"
                                                               align="center"
                                                               style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                                               class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="30"
                                                                    style="font-size: 1px; line-height: 1px;">
                                                                    &nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" align="center">

                                                                    <!-- SORTABLE -->
                                                                    <div class="sortable_inner ui-sortable">
                                                                        <table border="0" cellpadding="0"
                                                                               cellspacing="0" align="center"
                                                                               class="buttonScale">
                                                                            <tr>
                                                                                <td align="center" height="36"
                                                                                    bgcolor="#3f4345"
                                                                                    style="border-radius: 5px; padding-left: 20px; padding-right: 20px; font-family: &quot;Open Sans&quot;, Helvetica, Arial, sans-serif; color: rgb(255, 255, 255); font-size: 13px; font-weight: 700; line-height: 1px; background-color: rgb(63, 67, 69);">

                                                                                    <a href="@yield('button-link')"
                                                                                       style="color: #ffffff; text-decoration: none; width: 100%;"
                                                                                       cu-identify="element_021444065277121083">@yield('button-text')</a>

                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>

                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="display: none" id="element_05668071449398047"></div>

                                            <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                                                   class="mobile" bgcolor="#ffffff" object="drag-module-small"
                                                   style="border-bottom-right-radius: 6px; border-bottom-left-radius: 6px; background-color: rgb(255, 255, 255);">
                                                <tr>
                                                    <td width="100%" valign="middle" align="center">

                                                        <table width="330" border="0" cellpadding="0" cellspacing="0"
                                                               align="center"
                                                               style="text-align: center; border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"
                                                               class="fullCenter">
                                                            <tr>
                                                                <td width="100%" height="40"
                                                                    style="font-size: 1px; line-height: 1px;">
                                                                    &nbsp;</td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                </table><!-- End Shadow -->

                                @include('emails.partials.signature')

                            </div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

@endsection