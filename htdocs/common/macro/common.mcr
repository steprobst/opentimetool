<!--

$Id: common.mcr 123 2019-09-03 15:03:36Z munix9 $

-->
<!--
   
    DONT include other macros here!
    since this file gets included in headline.tpl
    only those macros which are included in there too can be resolved
    other will not be known there, so they can not be parsed and used!!!

-->


<!--
    show an input field with the given and the calendar popup

    @param  string  the input value name
    @param  string  the current value
    @param  string  the name of the form in which the input field will be used
-->
{%macro common_dateInput( $name , $value=0 , $formName='editForm' , $after='(dd.mm.yyyy)' , $setTodayIfEmpty=false )%}
    {global $util}

    <input name="{$name}"
    value="{echo $value?date('d.m.Y',$util->makeTimestamp($value)):''}"
    size="10"
    onclick="popUpCalendar(this,{$formName}['{$name}'],'dd.mm.yyyy')"
    onblur="autoCorrectDate('{$formName}','{$name}' , {$setTodayIfEmpty?'true':'false'} )"/>
    {$T_after}


<!--
    show an input field for entering a time

    @param  string  the input value name
    @param  string  the current value, as a timestamp
    @param  string  the name of the form in which the input field will be used
-->
{%macro common_timeInput( $name='newData[timestamp_time]' , $value=0 , $formName='editForm' )%}
    {global $util}

    <input name="{$name}"
    value="{echo date('H:i',$value)}"
    size="5"
    onblur="autoCorrectTime('{$formName}','{$name}')"/>


<!--
    this macro simply includes the requested js-file with proper headers etc.
-->
{%macro common_getJS( $which='common' , $isPhpJs=false )%}
    {global $config,$tempLoadedJsFiles,$lang}

    <!-- prevent from loading JS-files twice within one page
         first - to reduce traffic
         second - to prevent from JS-errors
         and i am sure i would forget to remove some includes if i would try to correct all the pages
         so i do this, this is secure
     -->
    {if( !@$tempLoadedJsFiles[$which] )}
        { $tempLoadedJsFiles[$which] = true}
        {switch( $which )}
            {case 'calendar':}
            <script src="{$config->applPathPrefix}/external/calendar/popcalendar.js.php"></script>
            {break;case 'common':}
            <script src="{$config->applPathPrefix}/common/js/{$which}.js.php"></script>
            {break;default:}
                {if($isPhpJs)}
                    <script src="{$config->applPathPrefix}/common/js/{$which}.js.php"></script>
                {else}
                    <script src="{$config->applPathPrefix}/common/js/{$which}.js"></script>
            {break}

<!--
    shows a table row with a textarea for a comment

    @param  string  a value to preset the textarea
    @param  string  the name used for the textarea
-->
{%macro common_commentRow($currentComment='',$name='newData[comment]')%}
    {global $util}

    {if($util->isDesktop())}
        { $cols = 50}
    {else}
        { $cols = 30}

    <tr>
        <td>comment</td>
        <td>
            <textarea name="{$name}" cols="{$cols}" rows="5">{$currentComment}</textarea>
        </td>
    </tr>


<!--
    @param  string  the subchapter, since the pageProp->get('manualChapter') mostly gets the right chapter
    @param  string  the string to be shown inside the link
    @param  string  in case u are linking to a chapter, which is not refered to by pageProp->get('manualChapter') give it here
-->
{%macro common_help( $subChapter='' , $string='' , $chapter=null )%}
    {echo common_getHelp( $subChapter , $string , $chapter )}

{%macro common_getHelp( $subChapter='' , $string='' , $chapter=null )%}
    {global $config,$pageProp}

    {if( !$string )}
        { $string='<img src="help.gif" class="button" style="vertical-align:bottom;" width="16" height="16" alt="Help">'}


    {if( $chapter == null )}
        { $chapter = $pageProp->get('manualChapter')}


    { $helpUrl = $config->applPathPrefix.'/modules/manual/de/manual.html#'.$chapter.($subChapter&&$subChapter!==true?'_'.$subChapter:'') }

    {return '<a href="javascript://" onclick="openHelpWindow(\''.$helpUrl.'\')" title="Help">'.$string.'</a>'}



{%macro common_editButton( $url='' )%}
    <a title="edit" href="{$url}">
        <img class="button" src="edit.gif" width="16" height="17" alt="edit">
    </a>


{%macro common_removeAndConfirmButton( $url='' , $text='')%}
    <a title="remove" href="javascript:removeConfirm('{$url}' , '{$text?$text:''}')">
        <img class="button" src="remove.gif" width="16" height="17" alt="remove">
    </a>

{%macro common_removeAndConfirmButtonAll( $url='' , $text='')%}
    <a title="remove" href="javascript:removeConfirmAll('{$url}' , '{$text?$text:''}')">
        <img class="button" src="remove.gif" width="16" height="17" alt="remove">
    </a>


{%macro common_removeAndConfirmButtonAlt( $func='' , $id='')%}
    <a title="remove" href="javascript:{$func}('{$id}')">
        <img class="button" src="remove.gif" width="16" height="17" alt="remove">
    </a>



<!--
    this is actually only needed in the main.tpl
-->
{%macro common_showError( &$configObj )%}

    {if( $configObj->anyErrorOrMessage() )}
        <table class="message" width="100%">
            <thead>
                <tr>
                    <th class="message">
                        {if( $configObj->anyError() )}
                            {%common_help(null,null,'errors')%}
                        {else}
                            {%common_help(null,null,'messages')%}
                        Messages
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="message">
                        {if( $configObj->anyError() )}
                            <span class="warning">
                                {$configObj->getErrors()}
                            </span>
                        {if( $configObj->anyMessage() )}
                            <span class="success">
                                {$configObj->getMessages()}
                            </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <table><tr><td height="5"></td></tr></table>


{%macro common_switchMobileDesktop($pos='top')%}
    {global $config,$util}

    {if($util->isDesktop())}
        { $title = 'Mobile-Log'}
        { $param = 0}
    {else}
        { $title = 'Desktop Version'}
        { $param = 1}

    {if($pos=='navi')}
        { $path = "'" . $config->applPathPrefix . "/modules/time/today.php'"}
    {else}
        { $path = 'window.location.pathname'}

    <div class="mdSwitch {$pos}">
        <a href="javascript://"
           onclick="window.location={$path}+'?isDesktop={$param}';">{$title}</a>
    </div>
