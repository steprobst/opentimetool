<!--

$Id: index.tpl 119 2017-09-16 11:04:33Z munix9 $

The template for the asp-account request. Not used in usual net-Mode.

These macros are expanded by the template engine.

-->

{%include vp/Application/HTML/Macro/EditData.mcr%}
{%include common/macro/common.mcr%}
{%include common/macro/table.mcr%}

<form action="{$_SERVER['PHP_SELF']}" method="post">
    <table class="outline">

        {%table_headline('account name')%}
        {%EditData_input($data,'accountName','account name')%}
        <tr> 
            <td>&nbsp;</td>
            <td>
                <input type="submit" value="OK">
            </td>
        </tr>

    </table>
</form>
