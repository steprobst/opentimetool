<!--

$Id: customer.mcr 119 2017-09-16 11:04:33Z munix9 $

-->

<!--
   @param   array   all the customers to show
   @param   int     the selected customer's id
-->
{%macro customersAsOptions($customers,$selected=0)%}
    {foreach( $customers as $aCustomer )}
        <option value="{$aCustomer['id']}"
            {if( $aCustomer['id'] == $selected )}
                selected="selected"
        >
        {$aCustomer['name']}
        </option>
