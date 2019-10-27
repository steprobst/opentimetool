<!--

$Id: dialog.tpl 121 2018-02-09 16:53:20Z munix9 $

-->

{%include common/macro/common.mcr%}
{%include vp/Application/HTML/Macro/Error.mcr%}

{include($layout->getHeaderTemplate(true))}

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    {if($pageProp->get('pageHeader'))}
        <tr>
            <td class="pageHeader" width="99%">
                <span class="content">{$T_pageProp->get('pageHeader')}</span>
            </td>
            <td class="pageHeader" nowrap="nowrap" align="right">
                <span class="content">{%common_help()%}</span>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    <tr>
        <td colspan="2">
            {%common_showError( $config )%}
            {include($layout->getContentTemplate('',true))}
            <br>
        </td>
    </tr>
</table>

{include($layout->getFooterTemplate(true))}
