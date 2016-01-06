<fieldset>
	<legend>{lang}wcf.acp.pageMenuItem.permissions{/lang}</legend>
		
	<div class="formElement" id="permissionsDiv">
		<div class="formFieldLabel">
			<label for="permissions">{lang}wcf.acp.pageMenuItem.permissions{/lang}</label>
		</div>
		<div class="formField">
			{assign var="counter" value=1}
			{foreach from=$permissions key=category item=values}
				<fieldset>
					<legend>
						<a onclick="openList('permissions{$counter}Div', { save:false })"><img src="{RELATIVE_WCF_DIR}icon/minusS.png" id="permissions{$counter}DivImage" alt="" title="{lang}wcf.global.button.collapsible{/lang}" /></a>
						{@$category}
					</legend>
					<div class="formElement" id="permissions{$counter}Div">
						{htmlcheckboxes options=$values name="permissions" selected=$selectedPermissions disableEncoding=true}
					</div>
					
				</fieldset>
				<script type="text/javascript">//<![CDATA[
					inlineHelp.register('permissions');
					initList('permissions{$counter}Div', 0);
				//]]></script>	
				{assign var="counter" value=$counter+1}
			{/foreach}	
		</div>
		<div class="formFieldDesc hidden" id="permissionsHelpMessage">
			{lang}wcf.acp.pageMenuItem.permissions.description{/lang}
		</div>
	</div>
	<script type="text/javascript">//<![CDATA[
		inlineHelp.register('permissions');
	//]]></script>	
</fieldset>