<form id="login" name="login" action="" method="post" class="forms" />
	<fieldset>
		<legend>Football Team Tool login</legend>
		<input type="hidden" name="action" value="login" />
		<div class="box_light_50">
			<ul class="content">
{					if $error}
				<li>
					<div class="msg_ko clearfix">Login or password incorrect, please try again.</div>
				</li>
{					/if}
				<li>
					<label for="email">Username:</label><input type="text" name="username" id="email" class="input_l" />
				</li>
				<li>
					<label for="password">Password:</label><input type="password" name="password" id="password" class="input_l" />
				</li>
				<li>
					<input type="submit" name="submit_login" value="Login" class="button" />
				</li>
			</ul>
		</div>
	</fieldset>
</form>
