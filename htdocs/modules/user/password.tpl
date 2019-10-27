<!--

$Id$

-->

{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include vp/Application/HTML/Macro/Form.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

{if($passwd !== true)}
    <form method="post" action="{$_SERVER['PHP_SELF']}">
        <table class="outline">
            <tfoot>
                {%EditData_saveButton(false)%}
            </tfoot>

            <tbody>
            {if($config->auth->savePwd || (isset($data['is_LDAP_user']) && !$data['is_LDAP_user']))}
                {%EditData_password('password',t('password').' *')%}
                {%EditData_password('password1',t('repeat password').' *')%}
            </tbody>
        </table>
    </form>

{%common_getJS()%}
