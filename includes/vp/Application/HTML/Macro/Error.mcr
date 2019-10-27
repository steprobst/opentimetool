<!--

$Id: Error.mcr 119 2017-09-16 11:04:33Z munix9 $

-->

<!--
    this is actually only needed in the main.tpl
-->
{%macro Error_show( &$configObj )%}
    
    {if( $configObj->anyErrorOrMessage() )}
        <table class="message" width="100%">
            <thead>
                <tr>
                    <th class="message">Messages</th>
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
