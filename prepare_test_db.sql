UPDATE users SET
		session_id='',
		password='',
		api_key='aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
		email='',
		openid='',
		vorname='',
		nachname='',
		strasse='',
		plz='',
		ort='',
		telefon='',
		jabber='',
		notification_method='email',
		activated=0,
		website='',
		icq='',
		about='',
		permission=8;

INSERT INTO `users` (`id`, `session_id`, `nickname`, `password`, `openid`,
					 `api_key`, `vorname`, `nachname`, `strasse`, `plz`, `ort`,
					 `telefon`, `email`,  `jabber`, `icq`, `website`, `about`,
					 `allow_node_delegation`, `notification_method`,
					 `permission`, `create_date`, `update_date`, `activated`)
VALUES
					(NULL, '', 'admin', '$2a$08$MrCZlG8G5uJlqqkB6u2LMOxz3oMM7U.cow7DaDiFR354AHuDjt0V6', '',
					'ducfuiwenfweur3irt38rti23erzm23ie', 'Toller', 'Administrator', 'Teststraße 23', '345345', 'Musterort',
					'', 'test@noreply.org', '', '', '', '', 0, 'email',
					'120', '2013-10-15 00:00:00', '2013-10-15 00:00:00', '0');

UPDATE config SET
		value=''
WHERE name='twitter_token' OR
	  name='google_maps_api_key' OR
	  name='jabber_server' OR 
	  name='jabber_username' OR 
	  name='jabber_password' OR 
	  name='twitter_username' OR 
	  name='twitter_token';

TRUNCATE TABLE user_remember_mes;

UPDATE routers SET router_auto_assign_hash='';