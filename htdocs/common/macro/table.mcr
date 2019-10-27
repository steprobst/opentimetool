<!--

$Id: table.mcr 121 2018-02-09 16:53:20Z munix9 $

-->
                             
<!--
    this macro is mainly here so the table headline can be translated properly
    if i had added the help link to the headline the translation wont work
-->
{%macro table_headline( $text , $helpSubTopic=true )%}
    <tr>
        <th colspan="2">            
            {if( $helpSubTopic )}
                {%common_help($helpSubTopic)%}&nbsp;
            {$T_text}
        </th>
    </tr>
