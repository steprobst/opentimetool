<!--

$Id: main.tpl 123 2019-09-03 15:03:36Z munix9 $

-->

{%include common/macro/common.mcr%}
{%include vp/Application/HTML/Macro/Error.mcr%}

{include($layout->getHeaderTemplate(true))}

<script>
    function inLineCountDown(secs = 10, url = "{$config->home}")
    \{
        if (secs < 1) \{
            window.location = url;
        \} else \{
            //var _secs = (secs<10?"0":"") + secs;
            document.getElementById("inLineCountDown_id").innerHTML = secs;
            window.setTimeout(function() \{
                inLineCountDown(--secs, url);
            \}, 1000);
        \}
    \}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    {if($util->isDesktop())}
        <thead>
            <tr>
                <td colspan="5" valign="top" class="layoutWithBgColor">
                    {include($layout->getHeadlineTemplate(true))}
                </td>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="5" class="layoutWithBgColor" align="center">
                    <div class="table statusLine foot">
                        <div class="tr">
                            <div class="td">
                                {$_openTimetool}
                                &nbsp;<i>{$account->isAspVersion()?'asp':'net'}</i>
                                {if($config->demoMode)}
                                    <i>/demo</i>
                            </div>

                            { $canBeAdmin=$user->canBeAdmin()}
                            {if(!is_array($canBeAdmin) && $canBeAdmin!==false)}
                                <div class="td">
                                    licensed for: {$session->account->numUsers} user
                                </div>
                            {if($account->isAspVersion())}
                                <div class="td">
                                    account name: {$account->getAccountName()}
                                </div>
                            {if(@$session->account->expires)}
                                <div class="td">
                                    your account expires: {$dateTime->formatDate($session->account->expires)}
                                </div>

                            <div class="td">
                                authentication mode: {$config->auth->method}
                            </div>

                            {if($userAuth->isLoggedIn())}
                                <div class="td" id="logoutText">
                                    auto-logout in ...
                                </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>

    <tbody>
        <tr>
            {if($util->isDesktop())}
                <td class="layoutWithBgColor" width="10">{$utilHtml->getSpacer(10)}</td> <!-- space column -->
                <td class="layoutWithBgColor" width="117">{$utilHtml->getSpacer(117)}</td> <!-- the navi column -->
                <td width="15">{$utilHtml->getSpacer(15)}</td> <!-- spacer between the text and the left side -->
                <td width="100%">&nbsp;</td> <!-- content space -->
                <td width="15">{$utilHtml->getSpacer(15)}</td>
            {else}
                <td width="100%"> <!-- content space -->
                    {if($userAuth->isLoggedIn())}
                        {%common_switchMobileDesktop()%}
                    &nbsp;
                </td>
        </tr>

        <tr>
            {if($util->isDesktop())}
                <td class="layoutWithBgColor">&nbsp;<br></td>
                <td valign="top" class="layoutWithBgColor">
                    {include($layout->getNavigationTemplate(true))}
                </td>
                <td>&nbsp;<br></td>

            <td valign="top">
                <table width="100%">
                    {if($pageProp->get('pageHeader'))}
                        <thead>
                            <tr>
                                <td class="pageHeader" width="99%">
                                    {if(!$util->isDesktop())}
                                        <span style="font-size:10px;">openTimetool &middot; </span>
                                    <span class="content">{$T_pageProp->get('pageHeader')}</span>
                                </td>
                                <td class="pageHeader" nowrap="nowrap" align="right">
                                    <span class="content">{%common_help()%}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                        </thead>

                    <tbody>
                        <tr>
                            <td colspan="2">
                                {%common_showError( $config )%}
                                {include($layout->getContentTemplate('',true))}
                                {if($util->isDesktop() || !$userAuth->isLoggedIn())}
                                    <br>
                                {else}
                                    {%common_switchMobileDesktop('bottom')%}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>

            {if($util->isDesktop())}
                <td>&nbsp;</td>    
        </tr>

        {if($util->isDesktop())}
            <tr>
                <td colspan="2" align="right" valign="bottom" class="layoutWithBgColor"><br><img src="dashedGreyHorizonal1.gif" width="137" height="19" alt="">
                </td>

                <td colspan="3" valign="bottom" class="layout"><br><img src="dashedGreyHorizonal2.gif" width="58" height="19" alt="">
                </td>
            </tr>
    </tbody>
</table>

<script>
    var _countDownTime = {$userAuth->options['expire']};

    var timeNow = new Date();
    var _startCountDownTime = timeNow.getTime() / 1000;

    function updateLogoutCountDown()
    \{
        var timeNow = new Date();
        var _now = timeNow.getTime() / 1000;

        timeLeft = _countDownTime-(_now-_startCountDownTime);
        _hours = parseInt(timeLeft/3600);
        _minutes = parseInt((timeLeft-(_hours*3600))/60);
        _seconds = parseInt(timeLeft%60);
        showTimeLeft = (_hours<10?"0":"")+_hours +":"+
                       (_minutes<10?"0":"")+_minutes+":"+
                       (_seconds<10?"0":"")+_seconds;

        document.getElementById("logoutText").firstChild.data = "auto-logout in "+showTimeLeft+" h";
        if (timeLeft>0) \{
            window.setTimeout("updateLogoutCountDown()", 1000);
        \} else \{
            document.getElementById("logoutText").firstChild.data = "... AUTO LOGOUT NOW ...";
        \}
    \}

    {if ($util->isDesktop() && $userAuth->isLoggedIn())}
        updateLogoutCountDown();
</script>

{include($layout->getFooterTemplate(true))}
